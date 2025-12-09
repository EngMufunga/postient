<?php
namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\User;
use App\Models\Deposit;
use App\Constants\Status;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\NotificationLog;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManageUsersController extends Controller
{

    public function allUsers($status = 'all')
    {
        $query = User::query();

        $pageTitle = 'All Users';
        $scope = null;

        switch ($status) {
            case 'all':
                $scope = null;
                $pageTitle = 'All Users';
                break;
            case 'active':
                $query->active();
                $scope = 'active';
                $pageTitle = 'Active Users';
                break;
            case 'banned':
                $query->banned();
                $scope = 'banned';
                $pageTitle = 'Banned Users';
                break;
            case 'email_verified':
                $query->emailVerified();
                $scope = 'emailVerified';
                $pageTitle = 'Email Verified Users';
                break;
            case 'email_unverified':
                $query->emailUnverified();
                $scope = 'emailUnverified';
                $pageTitle = 'Email Unverified Users';
                break;
            case 'mobile_verified':
                $query->mobileVerified();
                $scope = 'mobileVerified';
                $pageTitle = 'Mobile Verified Users';
                break;
            case 'mobile_unverified':
                $query->mobileUnverified();
                $scope = 'mobileUnverified';
                $pageTitle = 'Mobile Unverified Users';
                break;
            case 'with_balance':
                $query->withBalance();
                $scope = 'withBalance';
                $pageTitle = 'Users with Balance';
                break;
            default:

                break;
        }

        return $this->renderUserList($scope, $pageTitle);

    }

    protected function userData($scope = null){

        $baseQuery = $scope ? User::$scope() : User::query();
        $dataQuery =  User::query();

        $summaryQuery = clone $dataQuery;


        $totalUser = (clone $summaryQuery)->count();

        $activeUser = (clone $summaryQuery)->active()->count();
        $activeUserPercent = ($totalUser > 0) ? number_format((($activeUser / $totalUser) * 100), 2) : 0;

        $moibileVerifiedUser = (clone $summaryQuery)->mobileVerified()->count();
        $moibileVerifiedUserPercent = ($totalUser > 0) ? number_format((($moibileVerifiedUser / $totalUser) * 100), 2) : 0;

        $emailVerifiedUser = (clone $summaryQuery)->emailVerified()->count();
        $emailVerifiedUserPercent = ($totalUser > 0) ? number_format((($emailVerifiedUser / $totalUser) * 100), 2) : 0;


        $users =  $baseQuery->searchable(['username','email'])->dateFilter()->latest()->paginate(getPaginate());

        return [
            'data'=>$users,
            'summery'=>[
                'active_user'=>$activeUser,
                'active_user_percent'=>$activeUserPercent,
                'mobile_verified_user'=>$moibileVerifiedUser,
                'mobile_verified_user_percent'=>$moibileVerifiedUserPercent,
                'email_verified_user'=>$emailVerifiedUser,
                'email_verified_user_percent'=>$emailVerifiedUserPercent,
                'total'=>$totalUser
            ]
        ];

    }


    private function renderUserList($scope = null, $pageTitle = 'All Users')
    {
        $userData = $this->userData($scope);
        $items = $userData['data'];
        $summery = $userData['summery'];

        $widget = [
            'total_user' => $summery['total'],
            'active_user' => $summery['active_user'],
            'active_user_percent' => $summery['active_user_percent'],
            'mobile_verified_user' => $summery['mobile_verified_user'],
            'mobile_verified_user_percent' => $summery['mobile_verified_user_percent'],
            'email_verified_user' => $summery['email_verified_user'],
            'email_verified_user_percent' => $summery['email_verified_user_percent'],
        ];

        if (request()->ajax()) {
            return response()->json([
                'html' => view('Admin::components.tables.user_data', compact('items'))->render(),
                'pagination' => $items->hasPages() ? view('Admin::components.pagination', compact('items'))->render() : '',
            ]);
        }

        return view('Admin::users.list', compact('pageTitle', 'items', 'widget'));
    }

    public function bulkActionForm(Request $request)
    {
        $request->validate([
            'action' => 'required|in:banned,unbanned,mobile_verified,email_verified',
            'user_ids' => 'required|array|min:1',
            'message' => 'required_if:action,banned'
        ]);

        $action = $request->action;
        $ids = $request->user_ids;
        $users = User::whereIn('id', $ids)->get();



        if($action == 'banned') {
            foreach ($users as $user) {
                $user->status = Status::USER_BAN;
                $user->ban_reason = $request->message;
                $user->save();
            }
        }else if($action == 'unbanned') {
            foreach ($users as $user) {
                $user->status = Status::USER_ACTIVE;
                $user->ban_reason = null;
                $user->save();
            }
        }else if($action == 'mobile_verified') {
            foreach ($users as $user) {
                $user->sv = Status::VERIFIED;
                $user->save();
            }
        }else if($action == 'email_verified') {
            foreach ($users as $user) {
                $user->ev = Status::VERIFIED;
                $user->save();
            }
        }

        response()->json(['status' => 'success', 'message' => 'Users status updated successfully.']);
    }

    public function create()
    {
        $pageTitle = 'Create User';
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        return view('Admin::users.create', compact('pageTitle', 'countries'));
    }

    public function store(Request $request)
    {
        $countryData = (array)json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));
        $request->validate([
            'email' => 'required|string|email|unique:users',
            'mobile' => 'required|regex:/^([0-9]*)$/',
            'password' => 'required|string|min:6',
            'username' => 'required|unique:users|min:6',
            'mobile_code' => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country' => 'required|in:'.$countries,
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
        ]);

        if(preg_match("/[^a-z0-9_]/", trim($request->username))){
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $exist = User::where('mobile',$request->mobile_code.$request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        $user = new User();
        $user->firstname = trim($request->firstname);
        $user->lastname = trim($request->lastname);
        $user->email = strtolower(trim($request->email));
        $user->password = Hash::make($request->password);
        $user->username = trim($request->username);
        $user->ref_by =  0;
        $user->country_code = $request->country_code;
        $user->mobile = $request->mobile_code . $request->mobile;
        $user->address = [
            'address' => isset($request->address) ? $request->address : null,
            'state' => isset($request->state) ? $request->state : null,
            'zip' => isset($request->zip) ? $request->zip : null,
            'country' => isset($request->country) ? $request->country : null,
            'city' => isset($request->city) ? $request->city : null
        ];
        $user->status = Status::USER_ACTIVE;
        $user->kv = Status::VERIFIED;
        $user->ev = Status::VERIFIED;
        $user->sv = Status::VERIFIED;
        $user->ts = Status::DISABLE;
        $user->tv = Status::VERIFIED;
        $user->reg_step = Status::REG_COMPLETED;
        $user->save();

        $notify[] = ['success', 'User created successfully'];
        return redirect()->route('admin.users.detail', $user->id)->withNotify($notify);
    }

    public function detail($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'User Details / @'.$user->username;

        $totalDeposit = Deposit::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('user_id',$user->id)->count();
        $totalPosts = Post::where('user_id', $user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        return view('Admin::users.detail', compact('pageTitle', 'user','totalDeposit','totalTransaction','countries', 'totalPosts'));
    }





    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $countryData = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countryArray   = (array)$countryData;
        $countries      = implode(',', array_keys($countryArray));

        $countryCode    = $request->country;
        $country        = $countryData->$countryCode->country;
        $dialCode       = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:40|unique:users,mobile,' . $user->id,
            'country' => 'required|in:'.$countries,
        ]);
        $user->mobile = $dialCode.$request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
                            'address' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zip,
                            'country' => $country,
                        ];
        $user->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $user->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $user->ts = $request->ts ? Status::VERIFIED : Status::UNVERIFIED;
        $user->kv = Status::KYC_VERIFIED;
        $user->save();

        $notify[] = ['success', 'User details has been updated successfully'];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = gs();
        $trx = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $user->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', $general->cur_sym . $amount . ' has been added successfully'];

        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $user->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $user->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[] = ['success', $general->cur_sym . $amount . ' subtracted successfully'];
        }

        $user->save();

        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx =  $trx;
        $transaction->details = $request->remark;
        $transaction->save();

        notify($user, $notifyTemplate, [
            'trx' => $trx,
            'amount' => showAmount($amount),
            'remark' => $request->remark,
            'post_balance' => showAmount($user->balance)
        ]);

        return back()->withNotify($notify);
    }

    public function login($id){
        Auth::loginUsingId($id);
        return to_route('user.home');
    }

    public function status(Request $request,$id)
    {
        $user = User::findOrFail($id);
        if ($user->status == 1) {
            $request->validate([
                'reason'=>'required|string|max:255'
            ]);
            $user->status = Status::USER_BAN;
            $user->ban_reason = $request->reason;
            $notify[] = ['success','User banned successfully'];
        }else{
            $user->status = Status::USER_ACTIVE;
            $user->ban_reason = null;
            $notify[] = ['success','User unbanned successfully'];
        }
        $user->save();
        return back()->withNotify($notify);

    }


    public function showNotificationSingleForm($id)
    {
        $user = User::findOrFail($id);
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning','Notification options are disabled currently'];
            return to_route('admin.users.detail',$user->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $user->username;
        return view('Admin::users.notification_single', compact('pageTitle', 'user'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        notify($user,'DEFAULT',[
            'subject'=>$request->subject,
            'message'=>$request->message,
        ]);
        $notify[] = ['success', 'Notification sent successfully'];
        return back()->withNotify($notify);
    }


    public function showNotificationAllForm()
    {
        if (!gs('en') && !gs('sn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToUser = User::notifyToUser();
        $users        = User::active()->count();
        $pageTitle    = 'Notification to Verified Users';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('Admin::users.notification_all', compact('pageTitle', 'users', 'notifyToUser'));
    }

    public function sendNotificationAll(Request $request)
    {
        $request->validate([
            'via'                          => 'required|in:email,sms',
            'message'                      => 'required',
            'subject'                      => 'required_if:via,email',
            'start'                        => 'required|integer|gte:1',
            'batch'                        => 'required|integer|gte:1',
            'being_sent_to'                => 'required',
            'cooling_time'                 => 'required|integer|gte:1',
        ]);

        if (!gs('en') && !gs('sn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }


        $template = NotificationTemplate::where('act', 'DEFAULT')->where($request->via.'_status', Status::ENABLE)->exists();
        if(!$template){
            $notify[] = ['warning', 'Default notification template is not enabled'];
            return back()->withNotify($notify);
        }

        if ($request->being_sent_to == 'selectedUsers') {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['user' => session()->get('SEND_NOTIFICATION')['user']]);
            } else {
                if (!$request->user || !is_array($request->user) || empty($request->user)) {
                    $notify[] = ['error', "Ensure that the user field is populated when sending an email to the designated user group"];
                    return back()->withNotify($notify);
                }
            }
        }

        $scope          = $request->being_sent_to;
        $userQuery      = User::oldest()->active()->$scope();

        if (session()->has("SEND_NOTIFICATION")) {
            $totalUserCount = session('SEND_NOTIFICATION')['total_user'];
        } else {
            $totalUserCount = (clone $userQuery)->count() - ($request->start-1);
        }


        if ($totalUserCount <= 0) {
            $notify[] = ['error', "Notification recipients were not found among the selected user base."];
            return back()->withNotify($notify);
        }


        $users = (clone $userQuery)->skip($request->start - 1)->limit($request->batch)->get();

        foreach ($users as $user) {
            notify($user, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], [$request->via]);
        }

        return $this->sessionForNotification($totalUserCount, $request);
    }


    private function sessionForNotification($totalUserCount, $request)
    {
        if (session()->has('SEND_NOTIFICATION')) {
            $sessionData                = session("SEND_NOTIFICATION");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData               = $request->except('_token');
            $sessionData['total_sent'] = $request->batch;
            $sessionData['total_user'] = $totalUserCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalUserCount) {
            session()->forget("SEND_NOTIFICATION");
            $message = ucfirst($request->via) . " notifications were sent successfully";
            $url     = route("admin.users.notification.all");
        } else {
            session()->put('SEND_NOTIFICATION', $sessionData);
            $message = $sessionData['total_sent'] . " " . $sessionData['via'] . "  notifications were sent successfully";
            $url     = route("admin.users.notification.all") . "?email_sent=yes";
        }
        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }

    public function countBySegment($methodName){
        return User::active()->$methodName()->count();
    }

    public function get()
    {
        $query = User::active();

        if (request()->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . request()->search . '%')->orWhere('username', 'like', '%' . request()->search . '%');
            });
        }
        $users = $query->orderBy('id', 'desc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'users'   => $users,
            'more'    => $users->hasMorePages()
        ]);
    }

    public function notificationLog($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Notifications Sent to '.$user->username;
        $logs = NotificationLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        return view('Admin::reports.notification_history', compact('pageTitle','logs','user'));
    }
}
