<?php

namespace App\Services\Platform;

use App\Models\Platform;
use App\Models\SocialAccount;
use App\Lib\CurlRequest;
use App\Constants\Status;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Twitter
{
    public function connect($user): array
    {
        try {
            $platform = Platform::where('name', 'Twitter')->firstOrFail();
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

            return ['error' => false, 'message' => 'Twitter account connected successfully.'];
        } catch (\Throwable $th) {
            Log::error('Twitter connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect Twitter account.'];
        }
    }


    public function refreshAccessToken($account)
    {
        $clientId     = gs()->social_app_credential->twitter->client_id;
        $clientSecret = gs()->social_app_credential->twitter->client_secret;
        $refreshToken = $account->refresh_token;

        $postFields = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id'     => $clientId,
        ];

        $headers = [
            'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
        ];

        $response = CurlRequest::curlPostContent('https://api.twitter.com/2/oauth2/token', $postFields, $headers);
        $data = json_decode($response, true);

        if ($data && isset($data['access_token'])) {
            $account->access_token = $data['access_token'];
            $account->refresh_token = $data['refresh_token'] ?? $account->refresh_token;
            $account->expired_at = now()->addSeconds($data['expires_in']);
            $account->save();

            return $data['access_token'];
        }

        return null;
    }


    public function publishPost($post, $socialAccount)
    {
        // Use the same OAuth 2.0 token for everything
        $bearerToken = $socialAccount->access_token;

        $content = $post->post_content . ($post->tags ? "\n\n{$post->tags}" : '');
        $mediaIds = [];

        $mediaAssets = $post->mediaAssets ?? [];

        foreach ($mediaAssets as $media) {
            try {
                $filePath = $this->resolveMediaPath($media->filename);

                if ($media->type == 1) {
                    // 🖼️ Image
                    $mediaIds[] = $this->uploadImage($filePath, $bearerToken);
                } elseif ($media->type == 2) {
                    // 🎥 Video (only one per tweet)
                    $mediaIds[] = $this->uploadVideo($filePath, $bearerToken);
                    break;
                }
            } catch (\Exception $e) {
                Log::error("Media upload failed for {$media->filename}: " . $e->getMessage());
            }
        }

        try {
            $tweetId = $this->postTweet($content, $mediaIds, $bearerToken);
            if ($tweetId) {
                $tweetUrl = "https://x.com/i/web/status/{$tweetId}";

                // ✅ Update post record
                $post->publish_date = now();
                $post->status = Status::PUBLISH;
                $post->is_schedule = Status::DISABLE;
                $post->schedule_time = null;
                $post->save();

                return [
                    'status' => 'success',
                    'message' => 'Tweet posted successfully.',
                    'post_id' => $tweetId,
                    'post_url' => $tweetUrl,
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to post tweet (no tweet ID returned).'
            ];
        } catch (\Exception $e) {
            Log::error("Tweet post failed: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Tweet failed: ' . $e->getMessage(),
            ];
        }
    }


    private function resolveMediaPath($filename)
    {
        $relativePath = getFilePath('postMedia') . '/' . $filename;
        $localPath = base_path($relativePath);

        if (file_exists($localPath)) {
            return $localPath;
        }

        // fallback to remote
        $remoteUrl = route('home') . '/' . $relativePath;
        $tmpFile = sys_get_temp_dir() . '/' . basename($filename);
        $fileData = @file_get_contents($remoteUrl);

        if ($fileData === false) {
            throw new \Exception("Failed to access media file: {$remoteUrl}");
        }

        file_put_contents($tmpFile, $fileData);
        return $tmpFile;
    }


    private function uploadImage($filePath, $bearerToken)
    {
        $url = "https://upload.x.com/1.1/media/upload.json";

        $response = Http::withToken($bearerToken)
            ->attach('media', fopen($filePath, 'r'))
            ->post($url);

        if ($response->failed()) {
            throw new \Exception("Image upload failed: " . $response->body());
        }

        $data = $response->json();
        if (!isset($data['media_id_string'])) {
            throw new \Exception("Image upload missing media_id: " . $response->body());
        }

        return $data['media_id_string'];
    }

    private function uploadVideo($filePath, $bearerToken)
    {
        $url = "https://upload.x.com/1.1/media/upload.json";

        // INIT
        $init = [
            'command' => 'INIT',
            'media_type' => 'video/mp4',
            'total_bytes' => filesize($filePath),
            'media_category' => 'tweet_video'
        ];

        $initResp = Http::withToken($bearerToken)
            ->asForm()
            ->post($url, $init);

        if ($initResp->failed()) {
            throw new \Exception("Video INIT failed: " . $initResp->body());
        }

        $mediaId = $initResp->json('media_id_string');

        // APPEND chunks
        $chunkSize = 5 * 1024 * 1024;
        $handle = fopen($filePath, 'rb');
        $segmentIndex = 0;

        while (!feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $tmpFile = tempnam(sys_get_temp_dir(), 'tw_chunk_');
            file_put_contents($tmpFile, $chunk);

            $appendParams = [
                'command' => 'APPEND',
                'media_id' => $mediaId,
                'segment_index' => $segmentIndex
            ];

            $resp = \Illuminate\Support\Facades\Http::withToken($bearerToken)
                ->attach('media', fopen($tmpFile, 'r'))
                ->post($url, $appendParams);

            if ($resp->failed()) {
                throw new \Exception("Video APPEND failed: " . $resp->body());
            }

            unlink($tmpFile);
            $segmentIndex++;
        }

        fclose($handle);

        // FINALIZE
        $final = ['command' => 'FINALIZE', 'media_id' => $mediaId];
        $finalResp = Http::withToken($bearerToken)
            ->asForm()
            ->post($url, $final);

        if ($finalResp->failed()) {
            throw new \Exception("Video FINALIZE failed: " . $finalResp->body());
        }

        // Poll until processing finishes
        $processing = $finalResp->json('processing_info');
        $attempts = 0;

        while ($processing && in_array($processing['state'], ['pending', 'in_progress']) && $attempts < 20) {
            sleep($processing['check_after_secs'] ?? 5);
            $statusResp = Http::withToken($bearerToken)
                ->get($url, ['command' => 'STATUS', 'media_id' => $mediaId]);

            $processing = $statusResp->json('processing_info');
            $attempts++;
        }

        return $mediaId;
    }

    private function postTweet($status, $mediaIds = [], $bearerToken)
    {
        $url = "https://api.x.com/2/tweets";

        $payload = ['text' => $status];

        if (!empty($mediaIds)) {
            $payload['media'] = ['media_ids' => $mediaIds];
        }

        $response = Http::withToken($bearerToken)
            ->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception("Tweet failed: " . $response->body());
        }

        $data = $response->json();
        return $data['data']['id'] ?? null;
    }

}
