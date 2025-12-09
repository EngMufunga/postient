<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_roles')->withPivot('type')->withTimestamps();
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get:fn () => $this->badgeData(),
        );
    }

    public function badgeData(){
        $html = '';
        if($this->status == Status::ENABLE){
            $html = '<span class="badge badge--success">'.trans("Enable").'</span>';
        }else{
            $html = '<span class="badge badge--warning">'.trans("Disable").'</span>';
        }

        return $html;
    }

    public function  hasPermission($permission)
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }
}
