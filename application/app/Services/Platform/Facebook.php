<?php

namespace App\Services\Platform;

use App\Lib\CurlRequest;
use App\Models\Platform;
use App\Models\SocialAccount;
use App\Constants\Status;
use App\Services\Platform\Instagram;
use Illuminate\Support\Facades\Log;

class Facebook
{
    public function publishPost($post, $socialAccount)
    {
        info('Posting to Facebook');
        $publishedPosts = [];

        $accessToken = $socialAccount->access_token;
        $pageId = $socialAccount->meta_profile_id;

        // STEP 0: Build message
        $message = $post->post_content ?? '';
        if ($post->tags) {
            $message .= "\n\n" . $post->tags;
        }


        $images = $post->mediaAssets->where('type', 1);
        $videos = $post->mediaAssets->where('type', 2);


        // STEP 1: Upload video (if exists)
        $videoId = null;
        if ($videos->isNotEmpty()) {
            $video = $videos->first();
            $videoPath = $this->getLocalOrRemoteFile($video->filename);
            if ($videoPath) {
                $videoId = $this->uploadVideoToFacebook($videoPath, $pageId, $accessToken, 'Video Post', $message);
                if ($videoId) {
                    info("Video uploaded successfully: {$videoId}");
                    $publishedPosts[] = [
                        'type' => 'video',
                        'social_post_id' => $videoId,
                    ];
                }
            }
        }

        // STEP 2: Upload images as unpublished media
        $imageMediaIds = [];
        if ($images->isNotEmpty()) {
            foreach ($images as $image) {
                $imagePath = $this->getLocalOrRemoteFile($image->filename);
                if ($imagePath) {
                    $mediaId = $this->uploadUnpublishedImageToFacebook($imagePath, $pageId, $accessToken);
                    if ($mediaId) $imageMediaIds[] = $mediaId;
                }
            }

            if (!empty($imageMediaIds)) {
                $imagePostId = $this->publishFacebookPostWithImages($pageId, $accessToken, $imageMediaIds, $message);
                if ($imagePostId) {
                    info("Image post published successfully: {$imagePostId}");
                    $publishedPosts[] = [
                        'type' => 'image',
                        'social_post_id' => $imagePostId,
                    ];
                }
            }
        }

        // STEP 3: Text-only post (if no media)
         if ($videos->isEmpty() && $images->isEmpty() && !empty($message)) {
            $textPostId = $this->publishFacebookTextPost($pageId, $accessToken, $message);
            if ($textPostId) {
                info("Text-only post published: {$textPostId}");
                $publishedPosts[] = [
                    'type' => 'text',
                    'social_post_id' => $textPostId,
                ];
            }
        }

        if (!empty($publishedPosts)) {
            $post->publish_date = now();
            $post->status = Status::PUBLISH;
            $post->is_schedule = Status::DISABLE;
            $post->schedule_time = null;
            $post->save();

            return [
                'status' => 'success',
                'message' => 'Facebook post(s) published successfully',
                'posts' => $publishedPosts,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'No media or message available for Facebook post',
        ];
    }

    protected function getLocalOrRemoteFile($filename)
    {
        $localPath = base_path(getFilePath('postMedia') . '/' . $filename);
        if (file_exists($localPath)) return $localPath;

        info("File not found locally: {$localPath}");
        $remoteUrl = route('home') . '/' . getFilePath('postMedia') . '/' . $filename;
        $fileData = @file_get_contents($remoteUrl);

        if ($fileData) {
            $tmpFile = sys_get_temp_dir() . '/' . basename($filename);
            file_put_contents($tmpFile, $fileData);
            info("File downloaded from remote URL to temp file: {$tmpFile}");
            return $tmpFile;
        }

        Log::error("File missing: {$remoteUrl}");
        return null;
    }


    protected function uploadUnpublishedImageToFacebook($imagePath, $pageId, $accessToken)
    {
        $endpoint = "https://graph.facebook.com/v24.0/{$pageId}/photos";

        if (!file_exists($imagePath)) {
            info("Image file not found: {$imagePath}");
            return null;
        }

        $postFields = [
            'access_token' => $accessToken,
            'source' => new \CURLFile($imagePath),
            'published' => false, // Important: Unpublished upload
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (isset($data['id'])) {
            info("Unpublished image uploaded successfully. Media ID: " . $data['id']);
            return $data['id'];
        } else {
            info("Facebook unpublished image upload failed", ['response' => $data]);
            return null;
        }
    }


    protected function publishFacebookPostWithImages($pageId, $accessToken, $mediaIds, $message)
    {
        $endpoint = "https://graph.facebook.com/v24.0/{$pageId}/feed";

        $attachedMedia = [];
        foreach ($mediaIds as $id) {
            $attachedMedia[] = ['media_fbid' => $id];
        }

        $postFields = [
            'access_token' => $accessToken,
            'message' => $message,
            'attached_media' => json_encode($attachedMedia),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (isset($data['id'])) {
            info("Multi-image post published successfully. Post ID: " . $data['id']);
            return $data['id'];
        } else {
            info("Facebook multi-image post failed", ['response' => $data]);
            return null;
        }
    }


    protected function uploadVideoToFacebook($filePath, $pageId, $accessToken, $title = 'Video Post', $description = '')
    {
        $endpoint = "https://graph-video.facebook.com/v24.0/{$pageId}/videos";

        if (!file_exists($filePath)) {
            info("Video file does not exist: {$filePath}");
            return null;
        }

        $postFields = [
            'access_token' => $accessToken,
            'title' => $title,
            'description' => $description,
            'source' => new \CURLFile($filePath),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['id'] ?? null;
    }

    protected function publishFacebookTextPost($pageId, $accessToken, $message)
    {
        $endpoint = "https://graph.facebook.com/v24.0/{$pageId}/feed";
        $postFields = [
            'access_token' => $accessToken,
            'message' => $message,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['id'] ?? null;
    }


    public function connect($user): array
    {
        try {
            $response = json_decode(
                CurlRequest::curlContent("https://graph.facebook.com/v23.0/me/accounts?access_token={$user->token}")
            );

            if (!$response || !isset($response->data) || empty($response->data)) {
                return ['error' => true, 'message' => 'Failed to fetch Facebook pages.'];
            }

            foreach($response->data as $page){
                $platform = Platform::where('name', 'Facebook')->firstOrFail();
                $authUser = auth()->user();

                $account = SocialAccount::where('user_id', $authUser->id)
                    ->where('platform_id', $platform->id)
                    ->where('meta_profile_id', $page->id)
                    ->first();

                if (!$account) {
                    $account = new SocialAccount();
                    $authUser->increment('connected_profile');
                }

                $account->user_id = $authUser->id;
                $account->platform_id = $platform->id;
                $account->meta_profile_id = $page->id;
                $account->profile_name = $page->name;
                $account->profile_image = "https://graph.facebook.com/{$page->id}/picture?width=200&height=200";
                $account->access_token = $page->access_token;
                $account->expired_at = now()->addDays(30);
                $account->status = Status::ENABLE;
                $account->save();
            }


            return ['error' => false, 'message' => 'Facebook page connected successfully.'];
        } catch (\Throwable $th) {
            Log::error('Facebook connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect Facebook page.'];
        }
    }

    public function connectPage(array $pageData): array
    {
        try {
            $pageId = $pageData['page_id'] ?? null;
            $pageName = $pageData['name'] ?? 'Facebook Page';
            $accessToken = $pageData['access_token'] ?? null;
            $igBusinessId = $pageData['instagram_business_id'] ?? null;

            if (!$pageId || !$accessToken) {
                return ['error' => true, 'message' => 'Invalid Facebook Page data provided.'];
            }

            $platform = Platform::where('name', 'Facebook')->firstOrFail();
            $authUser = auth()->user();


            $exists = SocialAccount::where('user_id', $authUser->id)->where('platform_id', $platform->id)->where('meta_profile_id', $pageId)->exists();

            if ($exists) {
                $account = SocialAccount::where('user_id', $authUser->id)->where('platform_id', $platform->id)->where('meta_profile_id', $pageId)->first();
            }else{
                $account = new SocialAccount();
            }

            $account->user_id = $authUser->id;
            $account->platform_id = $platform->id;
            $account->meta_profile_id = $pageId;
            $account->profile_name = $pageName;
            $account->profile_image = "https://graph.facebook.com/{$pageId}/picture?width=200&height=200";
            $account->access_token = $accessToken;
            $account->expired_at = now()->addDays(30);
            $account->status = Status::ENABLE;
            $account->save();





            Log::info('Facebook Page connected: ' . $pageName);



            if ($igBusinessId) {
                $appId = gs()->social_app_credential->facebook->client_id;
                $clientSecret = gs()->social_app_credential->facebook->client_secret;

                $longLivedResponse = CurlRequest::curlContent(
                    "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id={$appId}&client_secret={$clientSecret}&fb_exchange_token={$accessToken}"
                );

                $tokenData = json_decode($longLivedResponse, true);

                if (!isset($tokenData['access_token'])) {
                    Log::error('Facebook token exchange failed: ' . $longLivedResponse);
                    return ['error' => true, 'message' => 'Failed to exchange long-lived token.'];
                }

                $longLivedToken = $tokenData['access_token'];

                $igInstance = new \App\Services\Platform\Instagram();

                $igData = [
                    'instagram_business_id' => $igBusinessId,
                    'access_token' => $longLivedToken,
                    'expires_in' => $tokenData['expires_in'] ?? null,
                    'auth_user'  => $authUser
                ];

                $igConnect = $igInstance->connect($igData);

                if ($igConnect['error']) {
                    return ['error' => true, 'message' => "Instagram connect failed for Page {$pageName}: " . $igConnect['message']];
                } else {

                    return ['error' => false, 'message' => 'Facebook Page {$pageName} and Instagram connected successfully.'];
                }
            }else{
                $authUser->connected_profile = max(0, $authUser->connected_profile - 1);
                $authUser->save();

                return ['error' => false, 'message' => 'Facebook Page connected successfully.'];
            }


        } catch (\Throwable $th) {
            Log::error('Facebook connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect Facebook Page.'];
        }
    }

}
