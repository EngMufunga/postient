<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Services\SocialConnectService;
use Illuminate\Http\Request;
use App\Services\Platform\Linkedin;
use App\Services\Platform\Tiktok;
use Illuminate\Support\Facades\Http;


use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;



class SocialAccountController extends Controller
{
    public function index()
    {
        $pageTitle = 'Connect Social Account';
        $platforms = Platform::active()->latest()->get();
        $accounts = SocialAccount::with('platform')->withCount('posts')->where('user_id', auth()->id())->active()->latest()->paginate(getPaginate());
        return view('UserTemplate::social_account', compact('pageTitle', 'accounts', 'platforms'));
    }

    public function connect($provider)
    {
        
       

        if (!activePlan()) {
            $notify[] = ['error', 'You need to subscribe to a plan to connect social accounts'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        // if(auth()->user()->connected_profile < 1){
        //     $notify[] = ['error', 'You have reached your channel limit. Please upgrade your plan'];
        //     return to_route('user.social.account.index')->withNotify($notify);
        // }


        if(!checkPlanPlatform($provider)){
            $notify[] = ['error', 'Your subscripton plan does not support this platform. You need to upgrade your plan.'];
            return to_route('user.social.account.index')->withNotify($notify);
        }

        $socialConnect = new SocialConnectService($provider);
        return $socialConnect->redirect();
    }

    public function callback($provider)
    {
        $socialLogin = new SocialConnectService($provider);

        try {
        return $socialLogin->login();
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return to_route('user.social.account.index')->withNotify($notify);
        }
    }
}
