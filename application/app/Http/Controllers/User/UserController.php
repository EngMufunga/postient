<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\Form;
use App\Models\Plan;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = auth()->user();
        $pageTitle = 'Dashboard';
        $postQuery = Post::where('user_id', $user->id);

        $widget['facebook'] = (clone $postQuery)
            ->whereHas('socialAccount', function($query) {
                $query->where('platform_id', Status::FACEBOOK);
            })->count();


        $widget['instagram'] = (clone $postQuery)
            ->whereHas('socialAccount', function($query) {
                $query->where('platform_id', Status::INSTAGRAM);
            })->count();


        $widget['twitter'] = (clone $postQuery)
            ->whereHas('socialAccount', function($query) {
                $query->where('platform_id', Status::TWITTER);
            })->count();


        $widget['linkedin'] = (clone $postQuery)
            ->whereHas('socialAccount', function($query) {
                $query->where('platform_id', Status::LINKEDIN);
            })->count();


        $widget['tiktok'] = (clone $postQuery)
            ->whereHas('socialAccount', function($query) {
                $query->where('platform_id', Status::TIKTOK);
            })->count();


        $widget['youtube'] = (clone $postQuery)
            ->whereHas('socialAccount', function($query) {
                $query->where('platform_id', Status::YOUTUBE);
            })->count();


        $widget['total_post'] = (clone $postQuery)->count();
        $widget['total_schedule_post'] = (clone $postQuery)->where('is_schedule', 1)->count();
        $widget['draft_post'] = (clone $postQuery)->where('status', 0)->count();
        $widget['publish_post'] = (clone $postQuery)->where('status', 1)->count();

        $platformPostCounts = Post::join('social_accounts', 'posts.social_account_id', '=', 'social_accounts.id')
            ->join('platforms', 'social_accounts.platform_id', '=', 'platforms.id')
            ->select('platforms.name as platform_name', DB::raw('COUNT(posts.id) as total_posts'))
            ->groupBy('platforms.id', 'platforms.name')
            ->where('posts.user_id', $user->id)
            ->where('posts.status', Status::PUBLISH)
            ->get();
        $labels = $platformPostCounts->pluck('platform_name')->toArray();
        $series = $platformPostCounts->pluck('total_posts')->toArray();
        $recentPosts = Post::where('user_id', $user->id)->latest()->limit(5)->get();
        return view('UserTemplate::dashboard', compact('pageTitle', 'labels', 'series', 'widget', 'recentPosts'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits();
        if ($request->search) {
            $deposits = $deposits->where('trx',$request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        return view('UserTemplate::deposit_history', compact('pageTitle', 'deposits'));

    }

    public function plans()
    {
        $pageTitle = 'Plans';
        $plans = Plan::searchable(['name', 'price'])->where('status', Status::ENABLE)->latest()->paginate(getPaginate());
        return view('UserTemplate::plans', compact('pageTitle', 'plans'));
    }

    public function subscriptions()
    {
        $pageTitle = 'Subscriptions';
        $subscriptions = Subscription::searchable(['plan:name', 'amount'])->with('plan')->where('user_id', auth()->id())->latest()->paginate(getPaginate());
        return view('UserTemplate::subscriptions', compact('pageTitle', 'subscriptions'));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view('UserTemplate::twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function freePlanSubscription()
    {
        $user = auth()->user();
        if($user->free_plan_used == Status::YES) {
            $notify[] = ['error', 'You have already subscribed free plan'];
            return back()->withNotify($notify);
        }
        $user->post_count = gs('schedule_post_count');
        $user->connected_profile = gs('connected_profile');
        $user->free_plan_used = Status::YES;
        $user->started_at = now();
        $user->expired_at = now()->addDays((int) gs('trail_days'));
        $user->save();
        $notify[] = ['success', 'Plan subscribed successfully'];
        return back()->withNotify($notify);
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->where('user_id', auth()->id())->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id',auth()->id())->searchable(['trx', 'amount'])->dateFilter()->filter(['trx_type', 'remark'])->latest()->paginate(getPaginate());
        return view('UserTemplate::transactions', compact('pageTitle','transactions','remarks'));
    }

    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name).'- attachments.'.$extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));

        return view('UserTemplate::user_data', compact('pageTitle','user', 'mobileCode', 'countries'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }

        $countryData = (array)json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));

        $exist = User::where('mobile',$request->mobile_code.$request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'mobile' => 'required|regex:/^([0-9]*)$/',
            'mobile_code' => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country' => 'required|in:'.$countries,
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->country_code = $request->country_code;
        $user->mobile = $request->mobile_code.$request->mobile;
        $user->address = [
            'country'=> $request->country,
            'address'=>$request->address ?? '',
            'state'=>$request->state ?? '',
            'zip'=>$request->zip ?? '',
            'city'=>$request->city ?? '',
        ];
        $user->sv = gs()->sv ? 0 : 1;
        $user->reg_step = 1;
        $user->save();

        $notify[] = ['success','Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);

    }

    public function creditRefill()
    {
        $pageTitle = 'Image Credit Refill';
        if(gs('image_generate_status') == Status::DISABLE){
            $notify[] = ['error','Image credit refill is currently disabled'];
            return to_route('user.home')->withNotify($notify);
        }
        return view('UserTemplate::credit_refill',compact('pageTitle'));
    }

    public function creditRefillConfirm(Request $request)
    {
        $request->validate([
            'credit' => 'required|numeric|gt:0',
        ]);

        if(gs('image_generate_status') == Status::DISABLE){
            $notify[] = ['error','Image credit refill is currently disabled'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();
        $creditPrice = gs('per_credit_price');
        $totalCost = $request->credit * $creditPrice;
        if($totalCost <= 0){
            $notify[] = ['error','Invalid amount'];
            return back()->withNotify($notify);
        }

        if($totalCost > auth()->user()->balance){
            $notify[] = ['error','You do not have sufficient balance for subscription.'];
            return back()->withNotify($notify);
        }

        $user->balance -= $totalCost;
        $user->image_credit += $request->credit;
        $user->save();

        $trx = getTrx();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $totalCost;
        $transaction->charge = 0;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->details = 'Subscription Payment Via Wallet Balance';
        $transaction->trx = $trx;
        $transaction->remark = 'credit_refill';
        $transaction->save();

        $notify[] = ['success','Image credit refilled successfully'];
        return redirect()->back()->withNotify($notify);
    }


    public function notifications(){
        $notifications = UserNotification::where('user_id',auth()->id())->latest()->paginate(getPaginate());
        $pageTitle = 'Notifications';
        return view('UserTemplate::notifications',compact('pageTitle','notifications'));
    }


    public function notificationRead($id){
        $notification = UserNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAll(){
        UserNotification::where('user_id',auth()->id())->where('is_read',Status::NO)->update([
            'is_read'=>Status::YES
        ]);
        $notify[] = ['success','Notifications read successfully'];
        return back()->withNotify($notify);
    }



    public function deleteAll(){
        UserNotification::where('user_id',auth()->id())->delete();
        $notify[] = ['success','All notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function singleNotificationDelete($id){
        $notification = UserNotification::where('user_id',auth()->id())->where('id',$id)->firstOrFail();
        $notification->delete();
        $notify[] = ['success','Notification deleted successfully'];
        return back()->withNotify($notify);
    }

}
