<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Subscription extends Model
{
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function statusBadge(): Attribute
    {
        return new Attribute(
            get:fn () => $this->badgeData(),
        );
    }

    public function badgeData(){
        $html = '';
        if($this->status == Status::SUBSCRIPTION_RUNNING){
            $html = '<span class="badge badge--success">'.trans("Running").'</span>';
        }else{
            $html = '<span class="badge badge--danger">'.trans("Expired").'</span>';
        }

        return $html;
    }



    public function scopeRunning($query)
    {
        return $query->where('status', Status::SUBSCRIPTION_RUNNING);
    }
}
