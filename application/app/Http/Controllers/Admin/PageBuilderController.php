<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\Frontend;
use App\Models\MenuItem;
use App\Models\MenuMenuItem;
use Illuminate\Support\Facades\View;

class PageBuilderController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function managePages()
    {
        $key = 'policy_pages';
        $section = @getPageSections()->$key;
        if (!$section) {
            return abort(404);
        }
        $elements = Frontend::where('data_keys', $key . '.element')->orderBy('id')->orderBy('id','desc')->get();
        $pdata = Page::all();
        $pageTitle = 'Manage System Pages';
        return view('Admin::frontend.builder.pages', compact('section','elements', 'key','pageTitle','pdata'));
    }

    public function managePagesSave(Request $request){

        $request->validate([
            'name' => 'required|min:3|string|max:40',
            'slug' => 'required|min:3|string|max:40',
        ]);

        $exists = Page::where('name', $request->name)->orWhere('slug', slug($request->slug))->exists();

        if ($exists) {
            $notify[] = ['error','Page name or slug already exists. Please choose a different name and slug, then try again.'];
            return back()->withNotify($notify);
        }

        $page = new Page();
        $page->name = $request->name;
        $page->slug = slug($request->slug);
        $page->save();
        $notify[] = ['success', 'New page has been added successfully'];
        return back()->withNotify($notify);

    }

    public function managePagesUpdate(Request $request){

        $page = Page::where('id',$request->id)->firstOrFail();
        $request->validate([
            'name' => 'required|min:3|string|max:40',
            'slug' => 'required|min:3|string|max:40'
        ]);

         $exists = Page::where('id', '!=', $page->id)
                    ->where(function ($query) use ($request) {
                        $query->where('name', $request->name)
                            ->orWhere('slug', slug($request->slug));
                    })
                    ->exists();

        if ($exists) {
            $notify[] = ['error','Page name or slug already exists. Please choose a different name and slug, then try again.'];
            return back()->withNotify($notify);
        }

        if (MenuItem::where('page_id', $page->id)->exists()) {
            $menuItems = MenuItem::where('page_id', $page->id)->get();
            foreach ($menuItems as $menuItem) {
                $menuItem->title = $request->name;
                $menuItem->url = slug($request->name);
                $menuItem->save();
            }
        }

        $page->name = $request->name;
        $page->slug = slug($request->slug);
        $page->save();

        $notify[] = ['success', 'Page has been updated successfully'];
        return back()->withNotify($notify);

    }

    public function managePagesDelete($id){
        $page = Page::findOrFail($id);
        if (MenuItem::where('page_id', $page->id)->exists()) {
            $menuItems = MenuItem::where('page_id', $page->id)->get();
            foreach ($menuItems as $menuItem) {
                $check = MenuMenuItem::where('menu_item_id', $menuItem->id)->exists();
                if ($check) {
                    MenuMenuItem::where('menu_item_id', $menuItem->id)->delete();
                }
                $menuItem->delete();
            }
        }

        $page->delete();
        $notify[] = ['success', 'Page has been deleted successfully'];
        return back()->withNotify($notify);
    }



    public function manageSection($id)
    {
        $pdata = Page::findOrFail($id);
        $pageTitle = 'Manage '.$pdata->name.' Page';
        $sections =  getPageSections(true);

        return view('Admin::frontend.builder.index', compact('pageTitle','pdata','sections'));
    }



    public function manageSectionUpdate($id, Request $request)
    {
        $request->validate([
            'secs' => 'nullable|array',
        ]);

        $page = Page::findOrFail($id);

        $newData = [];

        if (!$request->secs) {
            $page->secs = null;
        }else{
            foreach($request->secs as $sec){
                if(View::exists($this->activeTemplate.'sections.'.$sec)){
                    $newData[] = $sec;
                }
            }

            if(!empty($newData)){
                $page->secs = json_encode($newData);
            }else{
                $page->secs = null;
            }
        }


        $page->save();
        $notify[] = ['success', 'Page sections has been updated successfully'];
        return back()->withNotify($notify);
    }
}
