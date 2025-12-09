<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Register TikTok
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            'SocialiteProviders\\TikTok\\TikTokExtendSocialite@handle'
        );
        
        // Register Instagram
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            'SocialiteProviders\\Instagram\\InstagramExtendSocialite@handle'
        );
    }
}

