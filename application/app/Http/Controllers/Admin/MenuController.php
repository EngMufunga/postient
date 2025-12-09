<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuMenuItem;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $pageTitle = 'Menu Management';
        $menus = Menu::searchable(['name'])->withCount(['items'])->get();

        return view('Admin::menu.index',compact('pageTitle','menus'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string|max:40|unique:menus,name,' . $id,
        ]);

        if ($id) {
            $menu = Menu::findOrFail($id);
            $message = 'Menu updated successfully';
        } else {
            $menu = new Menu();
            $message = 'Menu created successfully';
            $menu->code = str_replace(' ', '_', strtolower($request->name));
        }

        $menu->name = $request->name;
        $menu->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }


    public function remove($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->items()->detach();

        foreach ($menu->items as $item) {
            $item->delete();
        }

        $menu->delete();

        $notify[] = ['success', 'Menu has been deleted successfully'];
        return back()->withNotify($notify);
    }


    public function assignMenuItem($id)
    {
        $menu = Menu::with('items')->findOrFail($id);
        $items = MenuItem::all();
        $pages = Page::all();

        $pageTitle = $menu->name . ' - Assign Items';
        return view('Admin::menu.assign_item',compact('pageTitle', 'menu', 'items', 'pages'));
    }


    public function menuItemStore(Request $request, $id)
    {
        $menu = Menu::with('items')->findOrFail($id);

        $request->validate([
            'link_type' => 'required|in:1,2,3',
            'title' => 'required_unless:link_type,3|nullable|string|max:40|unique:menu_items,title',
            'url' => 'required_if:link_type,1,2|nullable|string',
            'page_id' => 'required_if:link_type,3|nullable|exists:pages,id',
        ]);

        if($request->link_type == Status::EXTERNAL_LINK) {
            $request->validate([
                'url' => 'required|url',
            ]);
        }

        $check = MenuItem::where('title', $request->title)->whereNot('id', $id)->exists();
        if($check){
            $notify[] = ['error', 'This title has already been taken. Please choose another.'];
            return back()->withNotify($notify);
        }

        $menuItem = new MenuItem();
        $message = 'Menu created successfully';

        if($request->link_type == Status::PAGE_LINK) {
            $page = Page::find($request->page_id);
            if(!$page) {
                $notify[] = ['error', 'Page not found'];
                return back()->withNotify($notify);
            }

            $menuItem->title = $page->name;
            $menuItem->page_id = $request->page_id;
            $menuItem->link_type = $request->link_type;
            $menuItem->url = $page->slug;
            $menuItem->save();


            $menu->items()->attach($menuItem->id);


            $notify[] = ['success', $message];
            return back()->withNotify($notify);

        }else{
            $menuItem->title = $request->title;
            $menuItem->link_type = $request->link_type;
            $menuItem->url = $request->url;
            $menuItem->save();

            $menu->items()->attach($menuItem->id);

            $notify[] = ['success', $message];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'Something went wrong. Please try again'];
        return back()->withNotify($notify);
    }


    public function menuItemUpdate(Request $request, $id)
    {
        $request->validate([
            'link_type' => 'required|in:1,2,3',
            'title' => 'required_unless:link_type,3|nullable|string|max:40|unique:menu_items,title,' . $id,
            'url' => 'required_if:link_type,1,2|nullable|string',
            'page_id' => 'required_if:link_type,3|nullable|exists:pages,id',
        ]);

        if($request->link_type == Status::EXTERNAL_LINK) {
            $request->validate([
                'url' => 'required|url',
            ]);
        }

        $check = MenuItem::where('title', $request->title)->whereNot('id', $id)->exists();
        if($check){
            $notify[] = ['error', 'This title has already been taken. Please choose another.'];
            return back()->withNotify($notify);
        }

        $menuItem = MenuItem::findOrFail($id);
        $message = 'Menu updated successfully';


        if($request->link_type == Status::PAGE_LINK) {
            $page = Page::find($request->page_id);
            if(!$page) {
                $notify[] = ['error', 'Page not found'];
                return back()->withNotify($notify);
            }

            $menuItem->title = $page->name;
            $menuItem->page_id = $request->page_id;
            $menuItem->link_type = $request->link_type;
            $menuItem->url = $page->slug;
            $menuItem->save();
            $notify[] = ['success', $message];
            return back()->withNotify($notify);
        }else{
            $menuItem->title = $request->title;
            $menuItem->link_type = $request->link_type;
            $menuItem->url = $request->url;
            $menuItem->save();

            $notify[] = ['success', $message];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'Something went wrong. Please try again'];
        return back()->withNotify($notify);
    }

    public function menuItemDelete($id)
    {
        $item = MenuItem::findOrFail($id);

        $check = MenuMenuItem::where('menu_item_id', $item->id)->exists();
        if ($check) {
            MenuMenuItem::where('menu_item_id', $item->id)->delete();
        }
        $item->delete();

        $notify[] = ['success', 'Menu has been deleted successfully'];
        return back()->withNotify($notify);
    }

    public function assignMenuItemSubmit(Request $request,$id)
    {
        $request->validate([
            'menu_items' => 'nullable|array',
            'menu_items.*' => 'exists:menu_items,id',
        ]);

        $menu = Menu::findOrFail($id);

        $currentIds = $menu->items()->pluck('menu_items.id')->toArray();

        // Remove only current template's items
        $menu->items()->detach($currentIds);

        // Re-attach new ones
        if ($request->menu_items) {
            $menu->items()->attach($request->menu_items);

            $notify[] = ['success', 'Menu items updated successfully'];
            return back()->withNotify($notify);
        }

        $notify[] = ['info', 'You have not selected any menu items'];
        return back()->withNotify($notify);
    }
}
