<?php

namespace App\Services\Platform;

use App\Models\Platform;
use App\Models\SocialAccount;
use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;

class Youtube
{
    /**
     * Connect Twitter Account for the user
     */
    public function connect($user): array
    {
        try {
            $platform = Platform::where('name', 'Youtube')->firstOrFail();
            $authUser = auth()->user();

            $exists = SocialAccount::where('user_id', $authUser->id)
                ->where('platform_id', $platform->id)
                ->where('meta_profile_id', $user->id)
                ->exists();

            if ($exists) {
                $account = SocialAccount::where('user_id', $authUser->id)
                ->where('platform_id', $platform->id)
                ->where('meta_profile_id', $user->id)
                ->first();

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

                return ['error' => false, 'message' => 'Youtube account connected successfully.'];
        } catch (\Throwable $th) {
            Log::error('Youtube connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect Youtube account.'];
        }
    }


    // public function publishPost($post, $socialAccount)
    // {
    //     info("postToYouTube");
    //     $user = $post->user;
    //     $accessToken = $this->refreshAccessToken($socialAccount);
        
    //     // Log::error('Access Token: ' . $accessToken . '\n' . $socialAccount);
    //     $description = $post->post_content;
    //     $title       = $post->title;

    //     $mediaAssets = $post->mediaAssets ?? [];

    //     if ($mediaAssets->isEmpty()) {
    //         Log::warning("No media assets found for TikTok post", ['post_id' => $post->id]);
    //         return;
    //     }


    //     $firstMedia = $mediaAssets->where('type', 2)->first();

    //     if (!$firstMedia) {
    //         Log::warning("No video media asset found for YouTube post", ['post_id' => $post->id]);
    //         return;
    //     }

    //     $videoPath = url('/') . '/' . getFilePath('postMedia') . '/' . $firstMedia->filename;


    //     $tags        = explode(' ', $post->tags);
    //     $privacy     = 'public';
    //     $endPoint = "https://www.googleapis.com/upload/youtube/v3/videos?uploadType=multipart&part=snippet,status";

    //     $metadata = [
    //         'snippet' => [
    //             'title'       => $title,
    //             'description' => $description,
    //             'tags'        => $tags,
    //         ],
    //         'status'  => [
    //             'privacyStatus'           => $privacy,
    //             'selfDeclaredMadeForKids' => false,
    //             'commentStatus'           => 'allowed',
    //         ],
    //     ];

    //     $videoData = file_get_contents($videoPath);
    //     $delimiter = uniqid();
    //     $body  = "--{$delimiter}\r\n";
    //     $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
    //     $body .= json_encode($metadata) . "\r\n";
    //     $body .= "--{$delimiter}\r\n";
    //     $body .= "Content-Type: video/mp4\r\n\r\n";
    //     $body .= $videoData . "\r\n";
    //     $body .= "--{$delimiter}--";

    //     $headers = [
    //         "Authorization: Bearer {$accessToken}",
    //         "Content-Type: multipart/related; boundary={$delimiter}",
    //         "Content-Length: " . strlen($body),
    //     ];
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $endPoint);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    //     $response = curl_exec($ch);

    //     if (curl_errno($ch)) {
    //         throw new \Exception("cURL Error: " . curl_error($ch));
    //     }

    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);


    //     if ($httpCode !== 200) {
    //         throw new \Exception("YouTube API returned HTTP $httpCode: $response");
    //     }

    //     $result = json_decode($response, true);
        
    //       info("YouTube video posted response: " . $result);


    //     $postId = $result['id'];

    //     if(isset($reqult['id'])){
    //         $postId = $result['id'];

    //         $post->publish_date = now();
    //         $post->status = Status::PUBLISH;
    //         $post->is_schedule = Status::DISABLE;
    //         $post->schedule_time = null;
    //         $post->save();
    //         info("YouTube video posted successfully. Video ID: " . $postId);
    //         userNotification($user->id, "Your post has been published successfully on YouTube!", urlPath('user.posts.index'));

    //         return [
    //             'status' => 'success',
    //             'message' => 'Youtube posts published successfully'
    //         ];

    //     }else{
    //         return [
    //             'status' => 'error',
    //             'message' => 'Youtube posts published failed'
    //         ];
    //     }
    // }
    
    public function publishPost($post, $socialAccount)
    {
        info("postToYouTube started");
    
        // Refresh access token
        $accessToken = $this->refreshAccessToken($socialAccount);
    
        if (!$accessToken) {
            throw new \Exception("YouTube access token refresh failed.");
        }
    
        $user = $post->user;
        $mediaAssets = $post->mediaAssets ?? [];
    
        if ($mediaAssets->isEmpty()) {
            Log::warning("No media assets found for YouTube post", ['post_id' => $post->id]);
            return;
        }
    
        $firstMedia = $mediaAssets->where('type', 2)->first();
        if (!$firstMedia) {
            Log::warning("No video media asset found for YouTube post", ['post_id' => $post->id]);
            return;
        }
    
    
        
             $videoPath = $this->resolveFilePath($firstMedia->filename);
            if (!$videoPath) {
                throw new \Exception("Video file not found: {$firstMedia->filename}");
            }
                    
    
        $title = $post->title ?: "Untitled Video";
        $description = $post->post_content ?: "";
        $tags = array_filter(explode(' ', $post->tags)); // Clean tags
        $privacyStatus = "public";
    
        // Upload the video
        $result = $this->uploadToYouTube(
            $accessToken,
            $videoPath,
            $title,
            $description,
            $tags,
            $privacyStatus
        );
    
        if (!isset($result['id'])) {
            info("YouTube upload failed", $result);
            return [
                'status' => 'error',
                'message' => 'YouTube post published failed'
            ];
        }
    
        $videoId = $result['id'];
    
        info("YouTube video posted successfully: {$videoId}");
    
        // Save post
        $post->publish_date = now();
        $post->status = Status::PUBLISH;
        $post->is_schedule = Status::DISABLE;
        $post->schedule_time = null;
        $post->save();
    
        userNotification(
            $user->id,
            "Your post has been published successfully on YouTube!",
            urlPath('user.posts.index')
        );
    
        return [
            'status' => 'success',
            'message' => 'YouTube post published successfully',
            'video_id' => $videoId
        ];
    }

    
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
    
    
    
    
    
    protected function uploadToYouTube(
    $accessToken,
    $videoPath,
    $title,
    $description,
    $tags,
    $privacyStatus
) {
    $endpoint = "https://www.googleapis.com/upload/youtube/v3/videos?uploadType=multipart&part=snippet,status";

    $metadata = [
        'snippet' => [
            'title' => $title,
            'description' => $description,
            'tags' => $tags,
        ],
        'status' => [
            'privacyStatus' => $privacyStatus,
            'selfDeclaredMadeForKids' => false
        ],
    ];

    $videoData = file_get_contents($videoPath);
    $boundary = uniqid();

    $body = "";

    // Metadata Part
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
    $body .= json_encode($metadata) . "\r\n";

    // Video Binary Part
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: video/mp4\r\n\r\n";
    $body .= $videoData . "\r\n";

    $body .= "--{$boundary}--";

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: multipart/related; boundary={$boundary}",
        "Content-Length: " . mb_strlen($body, '8bit'),
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new \Exception("cURL Error: " . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new \Exception("YouTube API returned HTTP $httpCode: $response");
    }

    return json_decode($response, true);
}



    public function refreshAccessToken($account)
    {
        $clientId     = gs()->social_app_credential->youtube->client_id;
        $clientSecret = gs()->social_app_credential->youtube->client_secret;
        $refreshToken = $account->refresh_token;

        $response = CurlRequest::curlPostContent(
            'https://oauth2.googleapis.com/token',
            [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type'    => 'refresh_token',
            ]
        );

        $data = json_decode($response, true);
            

        if (!$data || !isset($data['access_token'])) {
            Log::error("Failed to refresh YouTube access token", ['response' => $response]);
            return null;
        }

        if ($data && isset($data['access_token'])) {
            $account->access_token = $data['access_token'];
            $account->refresh_token = $data['refresh_token'] ?? $account->refresh_token;
            $account->expired_at = now()->addSeconds($data['expires_in']);
            $account->save();

            return  $data['access_token'];

        }

        return null;
    }
}
