<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plugin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PluginController extends Controller
{
    public function index()
    {
        $pageTitle = 'Plugins';
        $plugins = Plugin::orderBy('status','desc')->get();
        return view('Admin::plugins.index', compact('pageTitle', 'plugins'));
    }


    public function update(Request $request, $id)
    {
        $extension = Plugin::findOrFail($id);
        foreach ($extension->shortcode as $key => $val) {
            $validation_rule = [$key => 'required'];
        }

        $request->validate($validation_rule);

        $shortcode = json_decode(json_encode($extension->shortcode), true);
        foreach ($shortcode as $key => $value) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $extension->shortcode = $shortcode;
        $extension->save();
        $notify[] = ['success', $extension->name . ' has been updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $extension = Plugin::findOrFail($id);
        if ($extension->status == 1) {
            $extension->status = 0;
            $notify[] = ['success', $extension->name . ' activated successfully'];
        }else{
            $extension->status = 1;
            $notify[] = ['success', $extension->name . ' activated successfully'];
        }
        $extension->save();
        return back()->withNotify($notify);
    }
}
