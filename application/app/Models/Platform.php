<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


class Platform extends Model
{
    protected $table = 'platforms';


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

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }


}
