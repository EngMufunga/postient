<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction()
    {
        $pageTitle = 'Transaction Logs';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::with('user')->orderBy('id','desc');

        $items = $transactions->searchable(['trx', 'user:username'])->dateFilter()->filter(['trx_type', 'remark'])->paginate(getPaginate());

        return view('Admin::reports.transactions', compact('pageTitle', 'items','remarks'));
    }

    public function loginHistory(Request $request)
    {
        $loginLogs = UserLogin::orderBy('id','desc')->with('user');
        $pageTitle = 'User Login History';
        if ($request->search) {
            $pageTitle = 'User Login History - ' . $request->search;
        }
        $loginLogs = $loginLogs->searchable(['user:username'])->dateFilter()->latest()->paginate(getPaginate());
        return view('Admin::reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip',$ip)->with('user')->searchable(['user:username'])->dateFilter()->latest()->paginate(getPaginate());
        return view('Admin::reports.logins', compact('pageTitle', 'loginLogs','ip'));

    }

    public function notificationHistory(Request $request){
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id','desc');

        $logs = $logs->with('user')->searchable(['user:username'])->dateFilter()->latest()->paginate(getPaginate());
        return view('Admin::reports.notification_history', compact('pageTitle','logs'));
    }

    public function emailDetails($id){
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('Admin::reports.email_details', compact('pageTitle','email'));
    }
}
