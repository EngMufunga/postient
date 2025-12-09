<?php

namespace App\Models;
use App\Traits\HasPermissions;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasPermissions;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    public function supperAdmin()
    {
        return $this->role_id == 0;
    }
}
