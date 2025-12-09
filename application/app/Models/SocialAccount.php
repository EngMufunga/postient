<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $table = 'social_accounts';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'social_account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }
}
