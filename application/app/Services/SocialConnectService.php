<?php

namespace App\Services;

use App\Lib\CurlRequest;
use App\Models\Platform;
use App\Constants\Status;
use App\Models\SocialAccount;
use App\Services\Platform\Facebook;
use App\Services\Platform\Twitter;
use App\Services\Platform\TikTok;
use App\Services\Platform\Youtube;
use App\Services\Platform\Snapchat;
use App\Services\Platform\Linkedin;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class SocialConnectService
{
    protected string $provider;

    public function __construct(string $provider)
    {
        $this->provider = $provider;
        $this->configureProvider();
    }


    public function redirect()
    {
        $notify[] = ['error', 'Something went wrong. Please try again.'];
        $scopes = implode(',', $this->getScopes($this->provider));
        
        return match ($this->provider) {
            'tiktok' => Socialite::driver('tiktok')->scopes(['user.info.basic','user.info.profile','user.info.stats','video.list','video.publish','video.upload'])->redirect(),
            'snapchat' => redirect()->away(
                'https://accounts.snapchat.com/login/oauth2/authorize?' . http_build_query([
                    'client_id' => gs()->social_app_credential->snapchat->client_id,
                    'response_type' => 'code',
                    'redirect_uri' => route('user.social.account.connect.callback', 'snapchat'),
                    'scope' => $scopes,
                    'state' => csrf_token(),
                ])
            ),
            'twitter' => Socialite::driver('twitter-oauth-2')->scopes(['tweet.read', 'users.read', 'tweet.write', 'offline.access', 'media.upload'])->redirect(),
            'facebook' => Socialite::driver('facebook')->scopes($scopes)->redirect(),
            'instagram' => Socialite::driver('facebook')
                        ->scopes([
                            'instagram_basic',
                            'pages_show_list',
                            'business_management',
                            'instagram_manage_messages',
                            'pages_read_engagement',
                            'pages_read_user_content'
                        ])
                        ->redirect(),
            'linkedin' => Socialite::driver('linkedin-openid')->scopes(['openid','profile','email','w_member_social'])->redirect(),
            'youtube' => Socialite::driver('google')->scopes([
                'openid',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/youtube',
                'https://www.googleapis.com/auth/youtube.readonly'
            ])->with(['access_type' => 'offline', 'prompt' => 'consent'])->redirect(),

            default => redirect()->back()->withNotify($notify),
        };
    }

    private function getSocialUser()
    {
        return match ($this->provider) {
            'twitter' => Socialite::driver('twitter-oauth-2')->user(),
            'facebook' => Socialite::driver('facebook')->user(),
            'instagram' => Socialite::driver('facebook')->user(),
            'linkedin-openid' => Socialite::driver('linkedin-openid')->user(),
            'youtube' => Socialite::driver('google')->user(),
        };
    }

    private function configureProvider(): void
    {
        
        $credentials = gs()->social_app_credential;
        $providerKey = $this->provider;
      
        if ($providerKey === 'linkedin') {
            $providerKey = 'linkedin-openid';
        }

        if ($providerKey === 'instagram') {
            $providerKey = 'facebook';
        }

        if ($providerKey === 'google') {
            $providerKey = 'google';
        }

        $configMap = [
            'facebook' => $credentials->facebook ?? null,
            'linkedin-openid' => $credentials->linkedin ?? null,
            'twitter' => $credentials->twitter ?? null,
            'tiktok' => $credentials->tiktok ?? null,
            'youtube' => $credentials->youtube ?? null,
            'snapchat' => $credentials->snapchat ?? null,
            'instagram' => $credentials->facebook ?? null
        ];

        $config = $configMap[$providerKey] ?? null;
        
        if (!$config) return;

        Config::set('services.' . $providerKey, [
            'client_id' => $config->client_id,
            'client_secret' => $config->client_secret,
            'redirect' => route('user.social.account.connect.callback', $providerKey),
        ]);
        
        
    


        if ($providerKey === 'youtube') {
            Config::set('services.google', [
                'client_id' => $config->client_id,
                'client_secret' => $config->client_secret,
                'redirect' => route('user.social.account.connect.callback', 'youtube'),
            ]);
        }

    }

    private function getScopes(string $provider): array
    {
        return match ($provider) {
            'facebook' => [
                'pages_manage_posts','pages_read_engagement','pages_show_list',
                'pages_manage_metadata','business_management','pages_manage_engagement',
                'pages_read_user_content','public_profile'
            ],
            'instagram' => [
                'instagram_basic', 'pages_show_list', 'business_management',
                'instagram_manage_messages', 'pages_read_engagement',
                'pages_read_user_content'
            ],
            'linkedin-openid' => ['openid','profile','email','w_member_social','r_events','rw_events'],
            'twitter' => ['tweet.read','tweet.write','users.read','offline.access','+media.write'],
            'youtube' => [
                'openid',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/youtube',
                'https://www.googleapis.com/auth/youtube.readonly'
            ],
            'tiktok' => ['user.info.basic','openid'],
            'snapchat' => ['snapchat-marketing-api','snapchat-profile','snapchat-business','openid','email','profile'],
            default => [],
        };
    }

    public function login()
    {

        
        if ($this->provider == 'linkedin-openid') {
            $provider = 'linkedin-openid';
        } else if ($this->provider == 'youtube') {
            $provider = 'google';
        } else if ($this->provider == 'twitter') {
            $provider = 'twitter';
        } else if($this->provider == 'tiktok') {
            $provider = 'tiktok';
        } elseif ($this->provider === 'instagram') {
                $provider = 'instagram'; // ✅ Instagram uses Facebook driver
            }
        else {
            $provider = 'facebook';
        }


        if ($provider === 'twitter') {
            $user = Socialite::driver('twitter-oauth-2')->user();
        }else if($provider === 'google')
        {
             $user = Socialite::driver('google')->stateless()->user();
        }
         else {
            $user = Socialite::driver($provider)->user();
        }
        
        


        if($this->provider === 'tiktok'){
            $instance = new \App\Services\Platform\Tiktok();
            $connect = $instance->connect($user);

            if ($connect['error']) {
                $notify[] = ['error', $connect['message']];
                return to_route('user.social.account.index')->withNotify($notify);
            }

            $notify[] = ['success', 'LinkedIn account connected successfully'];
            return to_route('user.social.account.index')->withNotify($notify);
        }else if ($this->provider == 'facebook') {

            $fbInstance = new \App\Services\Platform\Facebook();

            $data = CurlRequest::curlContent(
                "https://graph.facebook.com/v23.0/me/accounts?fields=id,name,access_token,instagram_business_account&access_token={$user->token}"
            );
            $response = json_decode($data, true);

            $notify = [];

            foreach ($response['data'] ?? [] as $page) {
                $pageData = [
                    'page_id' => $page['id'],
                    'name' => $page['name'],
                    'access_token' => $page['access_token'],
                    'instagram_business_id' => $page['instagram_business_account']['id'] ?? null,
                ];

                $connect = $fbInstance->connectPage($pageData);

                $notify[] = $connect;
            }

            return to_route('user.social.account.index')->withNotify($notify);

        }else if ($this->provider == 'linkedin-openid') {
            $instance = new \App\Services\Platform\Linkedin();
            $connect = $instance->connect($user);

            if ($connect['error']) {
                $notify[] = ['error', $connect['message']];
                return to_route('user.social.account.index')->withNotify($notify);
            }

            $notify[] = ['success', 'LinkedIn account connected successfully'];
            return to_route('user.social.account.index')->withNotify($notify);

        } else if ($this->provider == 'twitter') {
            $instance = new \App\Services\Platform\Twitter();

            $connect = $instance->connect($user);

            if ($connect['error']) {
                $notify[] = ['error', $connect['message']];
                return to_route('user.social.account.index')->withNotify($notify);
            }

            $notify[] = ['success', 'Twitter account connected successfully'];
            return to_route('user.social.account.index')->withNotify($notify);
            $notification = 'Twitter account connected successfully';

        } else if($this->provider == 'youtube'){
            $instance = new \App\Services\Platform\Youtube();
            $connect = $instance->connect($user);

            if ($connect['error']) {
                $notify[] = ['error', $connect['message']];
                return to_route('user.social.account.index')->withNotify($notify);
            }

            $notify[] = ['success', 'Youtube account connected successfully'];
            return to_route('user.social.account.index')->withNotify($notify);

        }
    }


}


