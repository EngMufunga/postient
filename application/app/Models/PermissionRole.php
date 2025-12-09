<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    static public function InsertUpdateRecord($permission_ids, $role_id, $type = 'user')
    {
        PermissionRole::where('role_id', $role_id)->delete();

        foreach($permission_ids as $permission_id)
        {
            $permission = new PermissionRole();
            $permission->permission_id = $permission_id;
            $permission->role_id = $role_id;
            $permission->type = $type;
            $permission->save();
        }
    }

    static public function getPermission($role_id, $type = 'user')
    {
        return PermissionRole::where('role_id', '=', $role_id)->where('type', '=', $type)->get();
    }

    static public function getRolePermission($slug, $role_id, $type = 'user')
    {
        return PermissionRole::select('permission_roles.id')
        ->join('permissions', 'permissions.id', '=', 'permission_roles.permission_id')
        ->where('permission_roles.role_id', '=', $role_id)
        ->where('permissions.slug', '=', $slug)
        ->where('permission_roles.type', '=', $type)
        ->count();

    }

    static public function permissionRole($role_id, $type = 'user')
    {
        return PermissionRole::select('permissions.id', 'permissions.slug', 'permissions.name', 'permissions.type')
        ->join('permissions', 'permissions.id', '=', 'permission_roles.permission_id')
        ->where('permission_roles.role_id', '=', $role_id)
        ->where('permission_roles.type', '=', $type)
        ->get();
    }
}
