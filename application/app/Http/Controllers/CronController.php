<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Lib\CurlRequest;
use App\Constants\Status;
use App\Models\SocialAccount;
use App\Models\Subscription;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $this->subscriptionUpdate();
        $this->socialAccountUpdate();
        $this->refreshToken();
    }

    protected function getServiceClassForAccount($account)
    {
        $name = $account->platform->name === 'Instagram' ? 'Facebook' : $account->platform->name;
        $class = "App\\Services\\Platform\\" . Str::studly($name);
                info('Service Class:' . $class);

        return class_exists($class) ? $class : null;
    }


    public function refreshToken()
    {
        $accounts = SocialAccount::active()->whereNotNull('refresh_token')->get();

        foreach ($accounts as $account) {
            $serviceClass = $this->getServiceClassForAccount($account);
            if (!$serviceClass) {
                Log::warning("Refresh token skipped: Service class [{$serviceClass}] not found for platform [{$account->platform->name}] (Account ID: {$account->id})");
                continue;
            }
            $platform = new $serviceClass();

            if (in_array($account->platform_id, [Status::YOUTUBE,Status::LINKEDIN,Status::TWITTER,Status::TIKTOK], true)) {
                try {
                    $data = $platform->refreshAccessToken($account);

                    Log::info("Access token refreshed successfully for account ID {$account->id} ({$account->platform->name}).");
                } catch (\Throwable $e) {
                    Log::error("Failed to refresh token for account ID {$account->id} ({$account->platform->name}): {$e->getMessage()}");
                }
            }
        }
    }


    public function subscriptionUpdate()
    {
        $subscriptions = Subscription::with('user')->where('started_at', '<', now())->where('expired_at', '<', now())->get();

        foreach ($subscriptions as $subscription) {
            $subscription->status = Status::DISABLE;
            $subscription->save();

            $user = $subscription->user;
            $user->plan_id      = 0;
            $user->connected_profile   = 0;
            $user->post_count   = 0;
            $user->schedule_status = Status::DISABLE;
            $user->ai_assistant_status = Status::DISABLE;
            $user->generated_content = 0;
            $user->started_at = null;
            $user->expired_at = null;
            $user->save();

            userNotification($user->id, 'Your subscription has expired.', urlPath('user.plans'));
        }
    }

    public function socialAccountUpdate()
    {
        $socialAccounts = SocialAccount::with('user')->where('expired_at', '<', now())->get();
        foreach ($socialAccounts as $account) {
            $user = $account->user;
            $user->connected_profile = max(0, $user->connected_profile - 1);
            $user->save();
            $account->status = Status::DISABLE;
            $account->save();

            userNotification($user->id, 'Your social account has expired. Please re-connect your account.', urlPath('user.social.account.index'));
        }
    }

    public function schedulePost()
    {
        $postSchedules = Post::with(['socialAccount', 'user', 'socialAccount.platform'])->where('is_schedule', Status::ENABLE)->where('schedule_time', '<=', now())->where('status', Status::POST_SCHEDULE)->take(30)->orderBy('schedule_time')->get();

        foreach ($postSchedules as $postSchedule) {
            $serviceClass = $this->getServiceClassForAccount($postSchedule?->socialAccount);
            if (!$serviceClass) {
                Log::warning("Service class [{$serviceClass}] not found for platform [{$postSchedule->socialAccount->platform->name}] (Account ID: {$postSchedule->socialAccount->id})");
                continue;
            }

            $account = $postSchedule->socialAccount;
            $user = $postSchedule->user;

            $platform = new $serviceClass();
            $response = $platform->publishPost($postSchedule, $account);


            if ($response['status'] == 'error') {
                userNotification($postSchedule->user_id, $response['message'], urlPath('user.posts.index'));
            }

            if ($response['status'] == 'success') {
                $postSchedule->publish_date = now();
                $postSchedule->status = Status::POST_PUBLISHED;
                $postSchedule->is_schedule = Status::DISABLE;
                $postSchedule->schedule_time = null;
                $postSchedule->save();
                
                userNotification($postSchedule->user_id, "Post scheduled successfully", urlPath('user.posts.index'));

                notify($postSchedule->user, 'SCHEDULE_POST', [
                    'post_title'        => $postSchedule->title,
                    'platform'        => $postSchedule->socialAccount->platform->name,
                    'profile_name'       => $postSchedule->socialAccount->profile_name,
                    'publish_time'   => now(),
                ]);

            }
        }

        $general            = gs();
        $general->last_cron = now();
        $general->save();
    }

}
