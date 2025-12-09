<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Plan extends Model
{
    protected $casts = [
        'templates' => 'object'
    ];


    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == Status::ENABLE){
                $html = '<span class="badge badge--success">'.trans('Active').'</span>';
            }else{
                $html = '<span class="badge badge--warning">'.trans('Inactive').'</span>';
            }
            return $html;
        });
    }


    public function durationTypeBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';

            if($this->type == Status::PLAN_MONTHLY){
                $html = '<span class="badge badge--success">'.trans('Monthly').'</span>';
            }else{
                $html = '<span class="badge badge--warning">'.trans('Yearly').'</span>';
            }

            return $html;
        });
    }


    public function featureBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';

            if($this->feature_status == Status::ENABLE){
                $html = '<span class="badge badge--success">'.trans('YES').'</span>';
            }else{
                $html = '<span class="badge badge--warning">'.trans('NO').'</span>';
            }

            return $html;
        });
    }
    public function scheduleBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';

            if($this->schedule_status == Status::ENABLE){
                $html = '<span class="badge badge--success">'.trans('YES').'</span>';
            }else{
                $html = '<span class="badge badge--warning">'.trans('NO').'</span>';
            }

            return $html;
        });
    }
}
