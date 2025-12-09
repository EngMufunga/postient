<?php

namespace App\Http\Controllers\Gateway;

use App\Models\User;
use App\Models\Deposit;
use App\Constants\Status;
use App\Lib\FormProcessor;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{

    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $pageTitle = 'Deposit Methods';

        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function planPayment($id)
    {
        $plan = Plan::findOrFail($id);
        $subscriptionCheck = Subscription::where('user_id', auth()->id())->where('expired_at', '>=', now())->where('plan_id', $id)->first();
        if ($subscriptionCheck) {
            $notify[] = ['error', 'You already subscribed this plan'];
            return back()->withNotify($notify);
        }

        $planCheck = Subscription::where('user_id', auth()->id())->where('expired_at', '>', now())->whereNot('plan_id', $id)->exists();

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $pageTitle = 'Subscription Payment';

        return view('UserTemplate::payment.subscriptions', compact('gatewayCurrency', 'pageTitle', 'plan', 'planCheck'));
    }


    public function subscriptionPayment(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'gateway' => ['required', 'string'],
            'method_code' => [
                Rule::when(fn ($input) => $input->gateway !== 'wallet', ['required', 'string'], ['nullable']),
            ],
            'currency' => [
                Rule::when(fn ($input) => $input->gateway !== 'wallet', ['required', 'string'], ['nullable']),
            ],
        ]);

        if($plan->price > $request->amount){
            $notify[] = ['error', 'Plan price not matched. Try again.'];
            return back()->withNotify($notify);
        }

        if($request->gateway == 'wallet'){
            $user = auth()->user();
            if($user->balance < $request->amount){
                $notify[] = ['error', 'You do not have sufficient balance for subscription.'];
                return back()->withNotify($notify);
            }

            $user->balance -= $request->amount;
            $user->save();

            $trx = getTrx();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $request->amount;
            $transaction->charge = 0;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type = '-';
            $transaction->details = 'Subscription Payment Via Wallet Balance';
            $transaction->trx = $trx;
            $transaction->remark = 'subscription';
            $transaction->save();


            $days = $plan->type == 1 ? 30 : 365;

            $subscription = new Subscription();
            $subscription->user_id = $user->id;
            $subscription->plan_id = $plan->id;
            $subscription->amount = $request->amount;
            $subscription->status = Status::SUBSCRIPTION_RUNNING;
            $subscription->expired_at = now()->addDays((int)$days);
            $subscription->started_at = now();
            $subscription->save();

            $user->plan_id = $plan->id;
            $user->post_count = $plan->schedule_post;
            $user->connected_profile = $plan->connected_profile;
            $user->started_at = now();
            $user->expired_at = now()->addDays((int)$days);


            $user->schedule_status = $plan->schedule_status ? Status::YES : Status::NO;
            $user->ai_assistant_status = $plan->ai_assistant_status ? Status::YES : Status::NO;
            $user->generated_content = $plan->generated_content_count ?? 0;
            $user->save();

            notify($user, 'PLAN_SUBSCRIBE', [
                'plan_name' => $plan->name,
                'amount' => showAmount($request->amount),
                'trx' => $trx,
                'post_balance' => showAmount($user->balance)
            ]);


            $notify[] = ['success', 'Subscription successful'];
            return to_route('user.subscriptions')->withNotify($notify);
        }


        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->plan_id = $plan->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositInsert(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);


        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (isset($data->session)) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit,$isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_PENDING || $deposit->status == Status::PAYMENT_INITIATE) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via '.$deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.log', 'approved');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);



            if($deposit->plan_id){
                $plan = Plan::find($deposit->plan_id);
                if($plan)
                {
                    $user->balance -= $plan->price;
                    $user->save();

                    $trx = getTrx();

                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount = $deposit->amount;
                    $transaction->charge = 0;
                    $transaction->post_balance = $user->balance;
                    $transaction->trx_type = '-';
                    $transaction->details = 'Subscription Payment Via '.$deposit->gatewayCurrency()->name;;
                    $transaction->trx = $deposit->trx;
                    $transaction->remark = 'subscription';
                    $transaction->save();


                    $days = $plan->type == 1 ? 30 : 365;

                    $subscription = new Subscription();
                    $subscription->user_id = $user->id;
                    $subscription->plan_id = $plan->id;
                    $subscription->amount = $plan->price;
                    $subscription->status = Status::SUBSCRIPTION_RUNNING;
                    $subscription->expired_at = now()->addDays((int)$days);
                    $subscription->started_at = now();
                    $subscription->save();

                    $user->plan_id = $plan->id;
                    $user->post_count = $plan->schedule_post;
                    $user->connected_profile = $plan->connected_profile;
                    $user->started_at = now();
                    $user->expired_at = now()->addDays((int)$days);


                    $user->schedule_status = $plan->schedule_status ? Status::YES : Status::NO;
                    $user->ai_assistant_status = $plan->ai_assistant_status ? Status::YES : Status::NO;
                    $user->generated_content = $plan->generated_content_count ?? 0;
                    $user->save();

                    notify($user, 'PLAN_SUBSCRIBE', [
                        'plan_name' => $plan->name,
                        'amount' => showAmount($deposit->amount),
                        'trx' => $deposit->trx,
                        'post_balance' => showAmount($user->balance)
                    ]);

                }
            }


        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $pageTitle = 'Deposit Confirm';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method','gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from '.$data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details',$data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }


}
