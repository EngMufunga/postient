<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\GPTModel;
use App\Models\Plan;
use App\Models\Platform;
use App\Models\SocialPlatform;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;


class PlanController extends Controller
{
    public function index($status = 'all')
    {
        $query = Plan::searchable(['name'])->latest();

        switch ($status) {
            case 'disable':
                $query->where('status', Status::DISABLE);
                break;
            case 'enable':
                $query->where('status', Status::ENABLE);
                break;
            case 'all':
                $query->whereIn('status', [Status::ENABLE, Status::DISABLE]);
                break;
            default:

                break;
        }

        $items = $query->paginate(getPaginate());

        if (request()->ajax()) {
            return response()->json([
                'html' => view('Admin::components.tables.plan_data', compact('items'))->render(),
                'pagination' => $items->hasPages() ? view('Admin::components.pagination', compact('items'))->render() : '',
            ]);
        }

        $pageTitle = ucfirst($status) . ' Plans';
        return view('Admin::plan.index', compact('items', 'pageTitle'));
    }


    public function create()
    {
        $pageTitle = 'Add New Plan';
        $platforms = Platform::where('status', Status::ENABLE)->pluck('name', 'id');
        return view('admin.plan.create', compact('pageTitle', 'platforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('plans')->where(function ($query) use ($request) {
                    return $query->where('type', $request->duration_type);
                }),
            ],
            'platform_access'  => ['required', 'exists:platforms,id'],
            'short_description'  => ['required'],
            'price'        => ['required', 'numeric', 'gt:0'],
            'connected_profile' => ['required', 'numeric', 'gte:1'],
            'schedule_post' => ['required', 'numeric', 'gte:1'],
            'type' => ['required', 'in:1,2'],
            'contents' => ['required', 'array', 'min:1'],
            'generated_content_count' => ['required_if:ai_assistant_status,1', 'numeric', 'gte:0'],
        ]);


        $plan = new Plan();
        $plan->name = $request->name;
        $plan->type = $request->type;
        $plan->platform_access = json_encode($request->platform_access);
        $plan->short_description = $request->short_description;
        $plan->price = $request->price;
        $plan->connected_profile = $request->connected_profile;
        $plan->schedule_post = $request->schedule_post;
        $plan->status = Status::ENABLE;
        $plan->ai_assistant_status = $request->ai_assistant_status ? Status::ENABLE : Status::DISABLE;
        $plan->schedule_status = $request->schedule_status ? Status::ENABLE : Status::DISABLE;
        $plan->feature_status = $request->feature_status ? Status::ENABLE : Status::DISABLE;

        if ($request->feature_status) {
            $chcekPlan = Plan::where('feature_status', Status::ENABLE)->exists();
            if ($chcekPlan) {
                foreach (Plan::where('feature_status', Status::ENABLE)->get() as $chcek) {
                     $chcek->feature_status = Status::DISABLE;
                     $chcek->save();
                }
            }
        }


        $plan->generated_content_count = $request->generated_content_count ?? 0;
        $plan->contents = json_encode($request->contents);
        $plan->save();

        $notify[] = ['success', 'Plan added successfully'];
        return to_route('admin.plan.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Update plan';
        $plan = Plan::findOrFail($id);
        $platforms = Platform::where('status', Status::ENABLE)->pluck('name', 'id');
        return view('admin.plan.edit', compact('pageTitle', 'plan', 'platforms'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('plans')->where(function ($query) use ($request) {
                    return $query->where('type', $request->duration_type);
                })->ignore($id),
            ],
            'platform_access'  => ['required', 'exists:platforms,id'],
            'short_description'  => ['required'],
            'price'        => ['required', 'numeric', 'gt:0'],
            'connected_profile' => ['required', 'numeric', 'gte:1'],
            'schedule_post' => ['required', 'numeric', 'gte:1'],
            'type' => ['required', 'in:1,2'],
            'contents' => ['required', 'array', 'min:1'],
            'generated_content_count' => ['required_if:ai_assistant_status,1', 'numeric', 'gte:0'],
        ]);



        $plan = Plan::findOrFail($id);
        $plan->name = $request->name;
        $plan->type = $request->type;
        $plan->platform_access = json_encode($request->platform_access);
        $plan->short_description = $request->short_description;
        $plan->price = $request->price;
        $plan->connected_profile = $request->connected_profile;
        $plan->schedule_post = $request->schedule_post;
        $plan->feature_status = $request->feature_status ? Status::ENABLE : Status::DISABLE;
        $plan->schedule_status = $request->schedule_status ? Status::ENABLE : Status::DISABLE;

        if ($request->feature_status) {
            $chcekPlan = Plan::where('feature_status', Status::ENABLE)->exists();
            if ($chcekPlan) {
                foreach (Plan::where('feature_status', Status::ENABLE)->get() as $chcek) {
                     $chcek->feature_status = Status::DISABLE;
                     $chcek->save();
                }
            }
        }

        $plan->ai_assistant_status = $request->ai_assistant_status ? Status::ENABLE : Status::DISABLE;
        $plan->generated_content_count = $request->generated_content_count ?? 0;
        $plan->contents = json_encode($request->contents);
        $plan->save();

        $notificationMessage = trans('Subscription package updated successfully');
        $notify[] = ['success', $notificationMessage];
        return to_route('admin.plan.index')->withNotify($notify);
    }

    public function status($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->status = $plan->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $plan->save();

        $notify[] = ['success', 'Status updated successfully'];
        return to_route('admin.plan.index')->withNotify($notify);
    }

    public function subscriptions()
    {
        $pageTitle = 'Subscriptions';
        $subscriptions = Subscription::searchable(['plan:name', 'user:username', 'user:firstname', 'user:lastname'])->with(['plan', 'user'])->latest()->paginate(getPaginate());
        return view('Admin::plan.subscriptions', compact('pageTitle', 'subscriptions'));
    }

}
