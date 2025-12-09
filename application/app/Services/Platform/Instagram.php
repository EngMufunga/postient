<?php

namespace App\Services\Platform;

use App\Lib\CurlRequest;
use App\Models\Platform;
use App\Models\SocialAccount;
use App\Constants\Status;
use Illuminate\Support\Facades\Log;

class Instagram
{
    public function connect(array $instagramData): array
    {
        try {
            $igBusinessId = $instagramData['instagram_business_id'] ?? null;
            $accessToken = $instagramData['access_token'] ?? null;
            $expiresIn = $instagramData['expires_in'] ?? null;
            $authUser = $instagramData['auth_user'];

            if (!$igBusinessId || !$accessToken) {
                return ['error' => true, 'message' => 'Invalid Instagram data provided.'];
            }

            // ✅ Use the correct endpoint for Business accounts
            $igProfileResponse = CurlRequest::curlContent(
                "https://graph.facebook.com/v21.0/{$igBusinessId}?fields=id,username,profile_picture_url,followers_count,media_count&access_token={$accessToken}"
            );

            $igProfile = json_decode($igProfileResponse, true);


            if (!isset($igProfile['id'])) {
                Log::error("Instagram profile fetch failed: {$igProfileResponse}");
                return ['error' => true, 'message' => 'Failed to fetch Instagram profile.'];
            }

            $platform = Platform::where('name', 'Instagram')->firstOrFail();

            $account = SocialAccount::where('user_id', $authUser->id)
                ->where('platform_id', $platform->id)
                ->where('meta_profile_id', $igProfile['id'])
                ->first();

            if (!$account) {
                $account = new SocialAccount();
            }


            $account->user_id = $authUser->id;
            $account->platform_id = $platform->id;
            $account->meta_profile_id = $igProfile['id'];
            $account->profile_name = $igProfile['username'] ?? 'Instagram';
            $account->profile_image = null;
            $account->access_token = $accessToken;
            $account->expired_at = now()->addSeconds(60 * 60 * 24 * 60);
            $account->status = Status::ENABLE;
            $account->save();

            $authUser->connected_profile = max(0, $authUser->connected_profile - 1);
            $authUser->save();

            return ['error' => false, 'message' => 'Instagram account connected successfully.'];
        } catch (\Throwable $th) {
            Log::error('Instagram connect error: ' . $th->getMessage());
            return ['error' => true, 'message' => 'Failed to connect Instagram account.'];
        }
    }


    public function publishPost($post, $socialAccount)
    {
        try {
            info("Publishing to Instagram for post #{$post->id}");

            $accessToken = $socialAccount->access_token;
            $igBusinessAccountId = $socialAccount->meta_profile_id; // Instagram Business Account ID

            if (!$accessToken || !$igBusinessAccountId) {
                return [
                    'status' => 'error',
                    'message' => 'Missing Instagram account credentials.',
                ];
            }

            $message = $post->post_content ?? '';
            if ($post->tags) {
                $message .= "\n\n" . $post->tags;
            }

            $images = $post->mediaAssets->where('type', 1);
            $videos = $post->mediaAssets->where('type', 2);

            $publishedPosts = [];

            // === STEP 1: Video Post ===
            if ($videos->isNotEmpty()) {
                $video = $videos->first();
                $videoPath = $this->getLocalOrRemoteFile($video->filename);
                $videoUrl = $this->getPublicUrl($videoPath);

                $containerId = $this->createInstagramContainer($igBusinessAccountId, $accessToken, [
                    'media_type' => 'VIDEO',
                    'video_url' => $videoUrl,
                    'caption' => $message,
                ]);

                if (!$containerId) {
                    return ['status' => 'error', 'message' => 'Failed to create Instagram video container.'];
                }

                $publishId = $this->publishInstagramMedia($igBusinessAccountId, $accessToken, $containerId);
                if ($publishId) {
                    $publishedPosts[] = ['type' => 'video', 'social_post_id' => $publishId];
                    info("Instagram video published successfully: {$publishId}");
                }
            }

            // === STEP 2: Carousel (multiple images) ===
            elseif ($images->count() > 1) {
                $childContainerIds = [];
                foreach ($images as $image) {
                    $imagePath = $this->getLocalOrRemoteFile($image->filename);
                    $imageUrl = $this->getPublicUrl($imagePath);

                    $childId = $this->createInstagramContainer($igBusinessAccountId, $accessToken, [
                        'image_url' => $imageUrl,
                        'is_carousel_item' => true,
                    ]);

                    if ($childId) $childContainerIds[] = $childId;
                }

                if ($childContainerIds) {
                    $parentContainerId = $this->createInstagramContainer($igBusinessAccountId, $accessToken, [
                        'media_type' => 'CAROUSEL',
                        'children' => $childContainerIds,
                        'caption' => $message,
                    ]);

                    if ($parentContainerId) {
                        $publishId = $this->publishInstagramMedia($igBusinessAccountId, $accessToken, $parentContainerId);
                        if ($publishId) {
                            $publishedPosts[] = ['type' => 'carousel', 'social_post_id' => $publishId];
                            info("Instagram carousel published successfully: {$publishId}");
                        }
                    }
                }
            }

            // === STEP 3: Single Image ===
            elseif ($images->count() === 1) {
                $image = $images->first();
                $imagePath = $this->getLocalOrRemoteFile($image->filename);
                $imageUrl = $this->getPublicUrl($imagePath);

                $containerId = $this->createInstagramContainer($igBusinessAccountId, $accessToken, [
                    'image_url' => $imageUrl,
                    'caption' => $message,
                ]);

                if (!$containerId) {
                    return ['status' => 'error', 'message' => 'Failed to create Instagram image container.'];
                }

                $publishId = $this->publishInstagramMedia($igBusinessAccountId, $accessToken, $containerId);
                if ($publishId) {
                    $publishedPosts[] = ['type' => 'image', 'social_post_id' => $publishId];
                    info("Instagram image published successfully: {$publishId}");
                }
            }

            // === STEP 4: Text-only (unsupported) ===
            else {
                return [
                    'status' => 'error',
                    'message' => 'Instagram does not allow text-only posts.',
                ];
            }

            // ✅ If something published successfully, update post status
            if (!empty($publishedPosts)) {
                $post->publish_date = now();
                $post->status = Status::PUBLISH;
                $post->is_schedule = Status::DISABLE;
                $post->schedule_time = null;
                $post->save();

                return [
                    'status' => 'success',
                    'message' => 'Instagram post(s) published successfully.',
                    'posts' => $publishedPosts,
                ];
            }

            // If no successful media
            return [
                'status' => 'error',
                'message' => 'Failed to publish Instagram post(s).',
            ];

        } catch (\Throwable $th) {
            Log::error("Instagram publish error", ['post_id' => $post->id, 'error' => $th->getMessage()]);
            return [
                'status' => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }


    // === Helper: Upload container ===
    protected function createInstagramContainer($igBusinessAccountId, $accessToken, $params)
    {
        $endpoint = "https://graph.facebook.com/v24.0/{$igBusinessAccountId}/media";
        $params['access_token'] = $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (isset($data['id'])) {
            info("Instagram container created: {$data['id']}");
            return $data['id'];
        } else {
            Log::error('Instagram container creation failed', ['response' => $data]);
            return null;
        }
    }

    // === Helper: Publish container ===
    protected function publishInstagramMedia($igBusinessAccountId, $accessToken, $creationId)
    {
        $endpoint = "https://graph.facebook.com/v24.0/{$igBusinessAccountId}/media_publish";
        $postFields = [
            'creation_id' => $creationId,
            'access_token' => $accessToken,
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

    // === Helper: Local/remote file ===
    protected function getLocalOrRemoteFile($filename)
    {
        $localPath = base_path(getFilePath('postMedia') . '/' . $filename);
        if (file_exists($localPath)) return $localPath;

        $remoteUrl = route('home') . '/' . getFilePath('postMedia') . '/' . $filename;
        $fileData = @file_get_contents($remoteUrl);

        if ($fileData) {
            $tmpFile = sys_get_temp_dir() . '/' . basename($filename);
            file_put_contents($tmpFile, $fileData);
            return $tmpFile;
        }

        Log::error("File missing: {$remoteUrl}");
        return null;
    }

    // === Helper: Convert local path to public URL ===
    protected function getPublicUrl($path)
    {
        if (str_starts_with($path, 'http')) return $path;

        return route('home') . '/' . getFilePath('postMedia') . '/' . basename($path);
    }


}
