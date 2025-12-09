<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index($status = 'all')
    {
        $admin = auth()->guard('admin')->user();

        $query = Role::searchable(['name'])->where('user_id', 0)->where('type', Status::ROLE_TYPE_ADMIN)->latest();

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
                'html' => view('Admin::components.tables.role_data', compact('items'))->render(),
                'pagination' => $items->hasPages() ? view('Admin::components.pagination', compact('items'))->render() : '',
            ]);
        }

        $pageTitle = ucfirst($status) . ' Roles';
        return view('Admin::role.index', compact('items', 'pageTitle'));


    }


    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => [
                'required',
                function ($attribute, $value, $fail) use ($id) {
                    if (!$value) {
                        return;
                    }

                    $exists = Role::where('name', $value)
                        ->where('user_id', 0)
                        ->where('type', Status::ROLE_TYPE_ADMIN)
                        ->when($id, fn($query) => $query->where('id', '!=', $id))
                        ->exists();

                    if ($exists) {
                        $fail("The $attribute already exists.");
                    }
                },
            ],
        ]);

        $role = $id ? Role::findOrFail($id) : new Role();
        $role->name = $request->name;
        $role->type = Status::ROLE_TYPE_ADMIN;
        $role->status = $id ? $role->status : Status::ENABLE;
        $role->user_id = 0;
        $role->save();

        $id ? $notify[] = ['success', 'Role updated successfully'] : $notify[] = ['success', 'Role created successfully'];
        return to_route('admin.role.index')->withNotify($notify);
    }


    public function status($id)
    {
        $role = Role::where('type', Status::ROLE_TYPE_ADMIN)->findOrFail($id);
        $role->status = $role->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $role->save();

        $notify[] = ['success', 'Role status updated successfully'];
        return to_route('admin.role.index')->withNotify($notify);
    }


    public function delete($id)
    {
        $role = Role::findOrFail($id);

        $check = Admin::where('role_id', $role->id)->exists();
        if ($check) {
            $notify[] = ['error', 'This role is already assigned to some admin. So, you can not delete this role'];
            return to_route('admin.role.index')->withNotify($notify);
        }


        if(PermissionRole::where('role_id', $role->id)->exists()){
            PermissionRole::where('role_id', $role->id)->delete();
        }
        $role->delete();

        $notify[] = ['success', 'Role deleted successfully'];
        return to_route('admin.role.index')->withNotify($notify);
    }
}
