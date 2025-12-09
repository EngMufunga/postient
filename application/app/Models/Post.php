<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function mediaAssets()
    {
        return $this->hasMany(PostMedia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id', 'id');
    }

    public function scheduleStatusBadge(): Attribute
    {
        return new Attribute(
            get:fn () => $this->scheduleBadgeData(),
        );
    }

    public function scheduleBadgeData(){
        $html = '';
        if($this->is_schedule == Status::YES){
            $html = '<span class="badge badge--success">'.trans("Yes").'</span>';
        }else{
            $html = '<span class="badge badge--danger">'.trans("No").'</span>';
        }

        return $html;
    }
    public function statusBadge(): Attribute
    {
        return new Attribute(
            get:fn () => $this->badgeData(),
        );
    }


    public function badgeData(){
        $html = '';
        if($this->status == Status::POST_DRAFT){
            $html = '<span class="badge badge--warning">'.trans("Draft").'</span>';
        }elseif($this->status == Status::POST_PUBLISHED){
            $html = '<span class="badge badge--success">'.trans("Published").'</span>';
        }
        else{
            $html = '<span class="badge badge--warning">'.trans("Scheduled").'</span>';
        }

        return $html;
    }
}
