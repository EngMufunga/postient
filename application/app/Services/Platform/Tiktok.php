<?php

namespace App\Services\Platform;

use App\Models\SocialAccount;
use App\Models\Platform;
use App\Constants\Status;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Tiktok
{
    public function connect($user): array
    {
        try {
            $platform = Platform::where('name', 'Tiktok')->firstOrFail();
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

            return ['error' => false, 'message' => 'TikTok account connected successfully.'];
        } catch (\Throwable $th) {
            Log::error('TikTok connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect TikTok account.'];
        }
    }


    public function publishPost($post, $socialAccount)
    {
        try {
            $accessToken = $socialAccount->access_token;
            $mediaAssets = $post->mediaAssets ?? [];
            $user = $post->user;

            if ($mediaAssets->isEmpty()) {
                Log::warning("No media assets found for TikTok post", ['post_id' => $post->id]);
                return ['status' => 'error', 'message' => 'No media found for post.'];
            }

            $firstMedia = $mediaAssets->where('type', 2)->first();

            if (!$firstMedia) {
                return ['status' => 'error', 'message' => 'TikTok only supports video uploads.'];
            }

            $file = url('/') . '/' . getFilePath('postMedia') . '/' . $firstMedia->filename;

            Log::info("TikTok video upload started", ['file' => $file]);

            $content = $post->post_content . ($post->tags ? "\n\n{$post->tags}" : '');

            $publishId = $this->uploadVideo($file, $accessToken, $content);

            if (!$publishId) {
                return ['status' => 'error', 'message' => 'TikTok video upload failed.'];
            }

            Log::info("TikTok video published successfully", ['publish_id' => $publishId]);
            
            
        //     Log::info("TikTok publish_id received", ['publish_id' => $publishId]);

        //     $statusData = $this->waitForPublishStatus($publishId, $accessToken);
            
        //   Log::info("TikTok publish status", ['status' => $statusData]);
    
        //     if ($statusData['status'] !== 'SUCCESS') {
        //         return ['status' => 'error', 'message' => 'TikTok failed to process video'];
        //     }
    
        //     // TikTok returns real video ID & share URL
        //     $videoId = $statusData['video_id'];
        //     $shareUrl = $statusData['share_url'];
    
        //     Log::info("TikTok video ready", [
        //         'video_id' => $videoId,
        //         'share_url' => $shareUrl
        //     ]);
    
        //     // Save in your DB
        //     $post->publish_date = now();
        //     $post->status = Status::PUBLISH;
        //     $post->is_schedule = Status::DISABLE;
        //     $post->schedule_time = null;
        //     $post->save();
    
        //     userNotification($post->user_id,
        //         "Your video has been successfully published to TikTok!",
        //         route('user.posts.index')
        //     );

        //     return [
        //         'status' => 'success',
        //         'message' => 'TikTok post published successfully',
        //         'tiktok_video_id' => $videoId,
        //         'tiktok_url' => $shareUrl
        //     ];
        


            $post->publish_date = now();
            $post->status = Status::PUBLISH;
            $post->is_schedule = Status::DISABLE;
            $post->schedule_time = null;
            $post->save();

            userNotification($post->user_id,"Your post has been published successfully on TikTok!",route('user.posts.index'));

            return ['status' => 'success', 'message' => 'TikTok post published successfully.'];

        } catch (\Throwable $th) {
            Log::error("TikTok publish error", ['message' => $th->getMessage()]);
            return ['status' => 'error', 'message' => $th->getMessage()];
        }

    }

    protected function uploadVideo(string $videoPath, string $accessToken, ?string $description = null)
    {
        $fileSize = file_exists($videoPath) ? filesize($videoPath) : 0;

        $response = Http::withToken($accessToken)
            ->post('https://open.tiktokapis.com/v2/post/publish/video/init/', [
                'post_info' => [
                    'title' => $description ?? 'My TikTok Video',
                    'privacy_level' => 'SELF_ONLY',
                    'disable_duet' => false,
                    'disable_comment' => false,
                    'disable_stitch' => false,
                ],
                'source_info' => [
                    'source' => 'PULL_FROM_URL',
                    'video_url' => $videoPath,
                ],
            ]);

        $data = $response->json();

        if (!isset($data['data']['publish_id'])) {
            Log::error('TikTok init failed', ['response' => $data]);
            return null;
        }

        $publishId = $data['data']['publish_id'];
        Log::info("TikTok video published", ['publish_id' => $publishId]);

        return $publishId;
    }
    
    
    
    protected function waitForPublishStatus(string $publishId, string $accessToken)
    {
        $maxAttempts = 10;
        $sleep = 3;
    
        for ($i = 0; $i < $maxAttempts; $i++) {
    
            $response = Http::withToken($accessToken)
                ->post('https://open.tiktokapis.com/v2/post/publish/status/', [
                    'publish_id' => $publishId
                ]);
    
 
    
            $data = $response->json() ?? [];

            Log::info("TikTok publish status check", ['response' => $data]);
    
            if (!isset($data['data']['status'])) {
                continue;
            }
    
            $status = $data['data']['status'];
    
            if ($status === 'SUCCESS') {
                return [
                    'status' => 'SUCCESS',
                    'video_id' => $data['data']['video_id'] ?? null,
                    'share_url' => $data['data']['share_url'] ?? null
                ];
            }
    
            if ($status === 'FAILED') {
                return ['status' => 'FAILED'];
            }
    
            sleep($sleep);
        }
    
        return ['status' => 'TIMEOUT'];
    }




    public function refreshAccessToken(SocialAccount $account)
    {
        $credentials = gs()->social_app_credential->tiktok;

        $response = Http::asForm()->post('https://open-api.tiktok.com/oauth/refresh_token/', [
            'client_key' => $credentials->client_id,
            'grant_type' => 'refresh_token',
            'refresh_token' => $account->refresh_token,
        ]);


        $data = $response->json('data');

        if ($data && isset($data['access_token'])) {
            $account->access_token = $data['access_token'];
            $account->refresh_token = $data['refresh_token'] ?? $account->refresh_token;
            $account->expired_at = now()->addSeconds($data['expires_in']);
            $account->save();

            return $data['access_token'];
        }

        Log::error("TikTok token refresh failed", ['response' => $response->json()]);
        return null;
    }
}
