<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_roles')->withPivot('type')->withTimestamps();
    }


    static public function getSingle($id)
    {
        return Permission::find($id);
    }

    static public function getRecord($type = 'user')
    {
        $getPermission = Permission::where('type', '=', $type)->groupBy('groupby')->get();
        $result = array();

        foreach($getPermission as $value)
        {
            $getPermissionGroup = Permission::getPermissionGroup($value->groupby);
            $data = array();
            $data['id'] = $value->id;
            $data['name'] = $value->name;
            $group = array();
            foreach($getPermissionGroup as $valueG)
            {
                $dataG = array();
                $dataG['id'] = $valueG->id;
                $dataG['name'] = $valueG->name;
                $group[] = $dataG;
            }
            $data['group'] = $group;
            $result[] = $data;
        }
        return $result;
    }

    static public function getPermissionGroup($groupby)
    {
        return Permission::where('groupby', '=', $groupby)->get();
    }
}
