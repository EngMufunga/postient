<?php

namespace App\Services\Platform;

use App\Models\Platform;
use App\Models\SocialAccount;
use App\Constants\Status;
use App\Lib\CurlRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class Linkedin
{
    /**
     * Connect LinkedIn Account for the user
     */
    public function connect($user): array
    {

        try {
            $platform = Platform::where('name', 'Linkedin')->firstOrFail();
            $authUser = auth()->user();

            $exists = SocialAccount::where('user_id', $authUser->id)->where('platform_id', $platform->id)->where('meta_profile_id', $user->id)->exists();

            if ($exists) {
                $account = SocialAccount::where('user_id', $authUser->id)->where('platform_id', $platform->id)->where('meta_profile_id', $user->id)->first();
            }else{
                $account = new SocialAccount();
            }

            $account->user_id = $authUser->id;
            $account->platform_id = $platform->id;
            $account->meta_profile_id = $user->id;
            $account->profile_name = $user->name;
            $account->profile_image = $user->avatar;
            $account->access_token = $user->token;
            $account->refresh_token = $user->refreshToken;
            $account->expired_at = now()->addSeconds($user->expiresIn);
            $account->status = Status::ENABLE;
            $account->save();

            $authUser->connected_profile = max(0, $authUser->connected_profile - 1);
            $authUser->save();

            return ['error' => false, 'message' => 'LinkedIn account connected successfully.'];
        } catch (\Throwable $th) {
            Log::error('Linkedin connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect Linkedin account.'];
        }
    }


    public function refreshAccessToken($account)
    {
        $clientId     = gs()->social_app_credential->linkedin->client_id;
        $clientSecret = gs()->social_app_credential->linkedin->client_secret;
        $refreshToken = $account->refresh_token;

        $response = CurlRequest::curlPostContent(
            'https://www.linkedin.com/oauth/v2/accessToken',
            [
                'grant_type'        => "refresh_token",
                'refresh_token'     => $refreshToken,
                'client_id'         => $clientId,
                'client_secret'     => $clientSecret,
            ]
        );

        $data = json_decode($response, true);
        if ($data && isset($data['access_token'])) {
            $account->access_token = $data['access_token'];
            $account->refresh_token = $data['refresh_token'] ?? $account->refresh_token;
            $account->expired_at = now()->addSeconds($data['expires_in']);
            $account->save();

            return  $data['access_token'];
        }
        return null;
    }


    public function publishPost($post, $socialAccount)
    {
        $accessToken = $socialAccount->access_token;
        $authorUrn = "urn:li:person:{$socialAccount->meta_profile_id}";
    
        if (!$accessToken || !$socialAccount->meta_profile_id) {
            return ['status' => 'error', 'message' => 'Missing LinkedIn credentials or profile ID.'];
        }
    
        $content = trim($post->post_content . ($post->tags ? "\n\n{$post->tags}" : ''));
    
        $videos = $post->mediaAssets->where('type', 2);
        $images = $post->mediaAssets->where('type', 1);
    
        $mediaAssets = [];
        $mediaCategory = 'NONE'; // Default: text-only
    
        try {
            // --- Handle video (takes priority if exists) ---
            if ($videos->isNotEmpty()) {
                $video = $videos->first(); // LinkedIn supports one video only
                $filePath = $this->resolveFilePath($video->filename);
                if (!$filePath) {
                    throw new \Exception("Video file not found: {$video->filename}");
                }
    
                $assetUrn = $this->uploadVideo($filePath, $authorUrn, $socialAccount);
                if ($assetUrn) {
                    $mediaCategory = 'VIDEO';
                    $mediaAssets[] = [
                        "status" => "READY",
                        "description" => ["text" => "Uploaded via API"],
                        "media" => $assetUrn,
                        "title" => ["text" => "Video Post"]
                    ];
                }
            }
    
            // --- Handle images (only if no video) ---
            elseif ($images->isNotEmpty()) {
                foreach ($images as $image) {
                    $filePath = $this->resolveFilePath($image->filename);
                    if (!$filePath) {
                        Log::warning("Image not found: {$image->filename}");
                        continue;
                    }
    
                    $assetUrn = $this->uploadImage($filePath, $authorUrn, $accessToken);
                    if ($assetUrn) {
                        $mediaCategory = 'IMAGE';
                        $mediaAssets[] = [
                            "status" => "READY",
                            "description" => ["text" => "Uploaded via API"],
                            "media" => $assetUrn,
                            "title" => ["text" => "Image"]
                        ];
                    }
                }
            }
    
            // --- Build the unified LinkedIn post body ---
            $body = [
                "author" => $authorUrn,
                "lifecycleState" => "PUBLISHED",
                "specificContent" => [
                    "com.linkedin.ugc.ShareContent" => [
                        "shareCommentary" => ["text" => $content ?: ''],
                        "shareMediaCategory" => $mediaCategory,
                    ]
                ],
                "visibility" => [
                    "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
                ]
            ];
    
            // Add media only if it exists
            if (!empty($mediaAssets)) {
                $body['specificContent']["com.linkedin.ugc.ShareContent"]["media"] = $mediaAssets;
            }
    
            // --- Make a single API call ---
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0'
            ])->post('https://api.linkedin.com/v2/ugcPosts', $body);
    
            $data = $response->json();
            Log::info('LinkedIn Post Response', $data);
    
            if (!empty($data['id'])) {
                $post->publish_date = now();
                $post->status = Status::PUBLISH;
                $post->is_schedule = Status::DISABLE;
                $post->schedule_time = null;
                $post->save();
    
                userNotification($post->user_id, "Your post has been published successfully on LinkedIn!", route('user.posts.index'));
    
                return ['status' => 'success', 'message' => 'LinkedIn post published successfully.', 'post' => $data];
            }
    
            return ['status' => 'error', 'message' => 'LinkedIn post creation failed.', 'response' => $data];
        } catch (\Throwable $th) {
            Log::error("LinkedIn publishPost failed: " . $th->getMessage());
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }
    
    /**
     * Helper to get local or remote file path safely
     */
    protected function resolveFilePath($filename)
    {
        $relativePath = getFilePath('postMedia') . '/' . $filename;
        $filePath = base_path($relativePath);
    
        if (file_exists($filePath)) return $filePath;
    
        $remoteUrl = route('home') . '/' . $relativePath;
        $fileData = @file_get_contents($remoteUrl);
        if ($fileData) {
            $tmpFile = sys_get_temp_dir() . '/' . basename($filename);
            file_put_contents($tmpFile, $fileData);
            return $tmpFile;
        }
    
        return null;
    }



    public function uploadImage($imageUrl, $authorUrn, $accessToken)
    {
        try {
            $endpoint = "https://api.linkedin.com/v2/assets?action=registerUpload";
            $headers = ['Content-Type: application/json', "Authorization: Bearer {$accessToken}"];

            $body = [
                "registerUploadRequest" => [
                    "recipes" => ["urn:li:digitalmediaRecipe:feedshare-image"],
                    "owner" => $authorUrn,
                    "serviceRelationships" => [
                        [
                            "relationshipType" => "OWNER",
                            "identifier" => "urn:li:userGeneratedContent"
                        ]
                    ]
                ]
            ];

            $response = CurlRequest::curlPostContent($endpoint, json_encode($body), $headers);
            $response = json_decode($response, true);

            $uploadUrl = $response['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $assetUrn = $response['value']['asset'];

            $imageData = file_get_contents($imageUrl);

            $uploadHeaders = [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: image/jpeg"
            ];

            CurlRequest::curlPutBinary($uploadUrl, $imageData, $uploadHeaders);
            return $assetUrn;
        } catch (\Exception $e) {
            info("LinkedIn image upload error: " . $e->getMessage());
            return null;
        }
    }


    public function uploadVideo($videoUrl, $authorUrn, $socialAccount)
    {
        $accessToken = $socialAccount->access_token;

        $registerUrl  = "https://api.linkedin.com/v2/assets?action=registerUpload";
        $headers      = [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json"
        ];

        $registerBody = [
            "registerUploadRequest" => [
                "owner" => $authorUrn,
                "recipes" => ["urn:li:digitalmediaRecipe:feedshare-video"],
                "serviceRelationships" => [[
                    "relationshipType" => "OWNER",
                    "identifier" => "urn:li:userGeneratedContent"
                ]]
            ]
        ];

        $registerResponse = CurlRequest::curlPostContent($registerUrl, json_encode($registerBody), $headers);
        $registerData     = json_decode($registerResponse, true);

        if (empty($registerData['value']['uploadMechanism'])) {
            throw new \Exception("Video registerUpload failed: " . $registerResponse);
        }

        $uploadUrl = $registerData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $asset     = $registerData['value']['asset'];

        $videoBinary = file_get_contents($videoUrl);
        if ($videoBinary === false) {
            throw new \Exception("Failed to load video from URL: {$videoUrl}");
        }

        $uploadHeaders = [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: video/mp4",
            "Content-Length: " . strlen($videoBinary)
        ];

        CurlRequest::curlPutBinary($uploadUrl, $videoBinary, $uploadHeaders);
        return $asset;
    }

}
