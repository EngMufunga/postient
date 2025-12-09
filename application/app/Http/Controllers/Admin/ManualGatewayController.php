<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;

class ManualGatewayController extends Controller
{
    public function index($status = 'all')
    {
        $query = Gateway::manual()->latest();

        switch ($status) {
            case '1':
                $query->where('status', 1);
                break;
            case '0':
                $query->where('status', 0);
                break;
            case 'all':
                $query->whereIn('status', [0, 1]);
                break;
            default:
                break;
        }

        $items = $query->searchable(['name', 'alias'])->paginate(getPaginate());

        if (request()->ajax()) {
            return response()->json([
                'html' => view('Admin::components.tables.manual_data', compact('items'))->render(),
                'pagination' => $items->hasPages() ? view('Admin::components.pagination', compact('items'))->render() : '',
            ]);
        }

        $pageTitle = 'Manual Gateways';
        return view('Admin::gateways.manual.list', compact('pageTitle', 'items'));
    }

    public function create()
    {
        $pageTitle = 'New Manual Gateway';
        return view('Admin::gateways.manual.create', compact('pageTitle'));
    }


    public function store(Request $request)
    {

        $formProcessor = new FormProcessor();
        $this->validation($request,$formProcessor);

        $lastMethod = Gateway::manual()->orderBy('id','desc')->first();
        $methodCode = 1000;
        if ($lastMethod) {
            $methodCode = $lastMethod->code + 1;
        }

        $generate = $formProcessor->generate('manual_deposit');

        $method = new Gateway();
        $method->code = $methodCode;
        $method->form_id = $generate->id ?? 0;
        $method->name = $request->name;
        $method->alias = strtolower(trim(str_replace(' ','_',$request->name)));
        $method->status = 0;
        $method->gateway_parameters = json_encode([]);
        $method->supported_currencies = [];
        $method->crypto = 0;
        $method->description = $request->instruction;

        if($request->hasFile('image')) {
            $method->image =fileUploader($request->image,getFilePath('paymentGateway'), getFileSize('paymentGateway'));
        }


        $method->save();

        $gatewayCurrency = new GatewayCurrency();
        $gatewayCurrency->name = $request->name;
        $gatewayCurrency->gateway_alias = strtolower(trim(str_replace(' ','_',$request->name)));
        $gatewayCurrency->currency = $request->currency;
        $gatewayCurrency->symbol = '';
        $gatewayCurrency->method_code = $methodCode;
        $gatewayCurrency->min_amount = $request->min_limit;
        $gatewayCurrency->max_amount = $request->max_limit;
        $gatewayCurrency->fixed_charge = $request->fixed_charge;
        $gatewayCurrency->percent_charge = $request->percent_charge;
        $gatewayCurrency->rate = $request->rate;
        $gatewayCurrency->save();

        $notify[] = ['success', $method->name . ' Manual gateway has been added.'];
        return back()->withNotify($notify);
    }

    public function edit($alias)
    {
        $pageTitle = 'Edit Manual Gateway';
        $method = Gateway::manual()->with('singleCurrency')->where('alias', $alias)->firstOrFail();
        $form = $method->form;
        return view('Admin::gateways.manual.edit', compact('pageTitle', 'method','form'));
    }

    public function update(Request $request, $code)
    {

        $formProcessor = new FormProcessor();
        $this->validation($request,$formProcessor);

        $method = Gateway::manual()->where('code', $code)->firstOrFail();

        $generate = $formProcessor->generate('manual_deposit',true,'id',$method->form_id);
        $method->name = $request->name;
        $method->alias = strtolower(trim(str_replace(' ','_',$request->name)));
        $method->gateway_parameters = json_encode([]);
        $method->supported_currencies = [];
        $method->crypto = 0;
        $method->description = $request->instruction;
        $method->form_id = $generate->id ?? 0;

        if($request->hasFile('image')) {
            $method->image =fileUploader($request->image,getFilePath('paymentGateway'), getFileSize('paymentGateway'), $method->image);
        }
        $method->save();

        $singleCurrency = $method->singleCurrency;
        $singleCurrency->name = $request->name;
        $singleCurrency->gateway_alias = strtolower(trim(str_replace(' ','_',$method->name)));
        $singleCurrency->currency = $request->currency;
        $singleCurrency->symbol = '';
        $singleCurrency->min_amount = $request->min_limit;
        $singleCurrency->max_amount = $request->max_limit;
        $singleCurrency->fixed_charge = $request->fixed_charge;
        $singleCurrency->percent_charge = $request->percent_charge;
        $singleCurrency->rate = $request->rate;
        $singleCurrency->save();


        $notify[] = ['success', $method->name . ' manual gateway has been updated successfully'];
        return to_route('admin.gateway.manual.edit',[$method->alias])->withNotify($notify);
    }

    private function validation($request,$formProcessor)
    {
        $validation = [
            'name'           => 'required',
            'rate'           => 'required|numeric|gt:0',
            'currency'       => 'required',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'instruction'    => 'required'
        ];

        $generatorValidation = $formProcessor->generatorValidation();
        $validation = array_merge($validation,$generatorValidation['rules']);
        $request->validate($validation,$generatorValidation['messages']);
    }

    public function status($code)
    {
        $method = Gateway::where('code', $code)->firstOrFail();
        $method->status = $method->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $method->save();
        $notify[] = ['success', $method->name . ' status changed successfully'];
        return redirect()->back()->withNotify($notify);
    }
}
