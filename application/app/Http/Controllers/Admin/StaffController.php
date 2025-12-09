<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $items = Admin::searchable(['name', 'email', 'username'])->latest()->paginate(getPaginate());

        if (request()->ajax()) {
            return response()->json([
                'html' => view('Admin::components.tables.staff_data', compact('items'))->render(),
                'pagination' => $items->hasPages() ? view('Admin::components.pagination', compact('items'))->render() : '',
            ]);
        }

        $pageTitle = 'Staff Members';

        return view('Admin::staff.index', compact('items', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle = 'Create Role';
        $roles = Role::where('user_id', 0)->where('type', Status::ROLE_TYPE_ADMIN)->where('status', Status::ENABLE)->latest()->get();
        return view('Admin::staff.create', compact('pageTitle', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:admins,name|string|max:40',
            'role_id'    => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('status', Status::ENABLE);
                }),
            ],
            'email' => 'required|email|unique:admins,email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])],
            'password' => 'required',
            'username' => 'required|unique:admins,username|min:6',
        ], [
            'role_id.required' => 'Please select a role.',
            'role_id.exists' => 'The selected role is invalid or disabled.',
        ]);

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->role_id = $request->role_id;
        $admin->email = $request->email;
        if($request->hasFile('image')){
            $admin->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'));
        }
        $admin->password = bcrypt($request->password);
        $admin->username = $request->username;
        $admin->save();

        $notify[] = ['success', 'Staff created successfully'];
        return to_route('admin.staff.index')->withNotify($notify);


    }



    public function edit($id)
    {
        $pageTitle = 'Update Staff';
        $staff = Admin::findOrFail($id);
        if($staff->supperAdmin()){
            $notify[] = ['error', 'You can not update supper admin'];
            return back()->withNotify($notify);
        }
        $roles = Role::where('user_id', 0)->where('type', Status::ROLE_TYPE_ADMIN)->latest()->get();
        return view('Admin::staff.edit', compact('pageTitle', 'staff', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        if($admin->supperAdmin()){
            $notify[] = ['error', 'You can not update supper admin'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:40',
                Rule::unique('admins', 'name')->ignore($id),
            ],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('status', Status::ENABLE);
                }),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($id),
            ],
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'password' => ['nullable', 'string', 'min:6'],
            'username' => [
                'required',
                'min:6',
                Rule::unique('admins', 'username')->ignore($id),
            ],
        ], [
            'role_id.required' => 'Please select a role.',
            'role_id.exists' => 'The selected role is invalid or disabled.',
        ]);



        $admin->name = $request->name;
        $admin->role_id = $request->role_id;
        $admin->email = $request->email;
        if ($request->hasFile('image')){
            $admin->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $admin->image);
        }
        if ($request->filled('password')) {
            $admin->password = bcrypt($request->password);
        }
        $admin->username = $request->username;
        $admin->save();

        $notify[] = ['success', 'Staff updated successfully'];
        return to_route('admin.staff.index')->withNotify($notify);


    }

    public function login($id){
        $admin = Admin::findOrFail($id);
        Auth::guard('admin')->loginUsingId($admin->id);
        return to_route('admin.dashboard');
    }


    public function status($id)
    {
        $role = Role::findOrFail($id);
        $role->status = $role->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $role->save();

        $notify[] = ['success', 'Role status updated successfully'];
        return to_route('admin.role.index')->withNotify($notify);
    }


    public function remove($id)
    {
        $staff = Admin::findOrFail($id);
        if($staff->supperAdmin()){
            $notify[] = ['error', 'You can not delete supper admin'];
            return to_route('admin.staff.index')->withNotify($notify);
        }
        $role = Role::findOrFail($staff->role_id);
        if(PermissionRole::where('role_id', $role->id)->exists()){
            PermissionRole::where('role_id', $role->id)->delete();
        }
        if($staff->image){
            fileManager()->removeFile(getFilePath('adminProfile') . '/' . $staff->image);
        }
        $staff->delete();


        $notify[] = ['success', 'Staff removed successfully'];
        return to_route('admin.staff.index')->withNotify($notify);
    }


    public function setup($id)
    {
        $pageTitle = 'Setup Role Permissions';
        $staff = Admin::findOrFail($id);

        $role = Role::findOrFail($staff->role_id);
        $permissions = Permission::all()->groupBy('groupby');
        $assignedPermissions = $role->permissions->pluck('id')->toArray();
        return view('Admin::staff.setup', compact('role', 'permissions', 'assignedPermissions', 'pageTitle', 'staff'));
    }

    public function setupUpdate(Request $request, $id)
    {

        $staff = Admin::find($id);



        $role = Role::findOrFail($staff->role_id);
        $type = Status::ROLE_TYPE_ADMIN;
        $syncData = [];

        if ($request->filled('permissions')) {
            $syncData = [];

            foreach ($request->permissions as $permissionId) {
                $syncData[$permissionId] = ['type' => $type];
            }

            $role->permissions()->sync($syncData);
        } else {
            $role->permissions()->detach();
        }

        $notify[] = ['success', 'Role permissions updated successfully'];
        return to_route('admin.staff.index')->withNotify($notify);
    }

    public function seeder()
    {
        Permission::truncate();
        PermissionRole::truncate();
        $seeder = new PermissionsTableSeeder();
        $seeder->run();
        $notify[] = ['success', 'Permission data reset successfully'];
        return to_route('admin.staff.index')->withNotify($notify);
    }
}
