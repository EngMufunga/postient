<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MenuItem extends Model
{
    protected $table = 'menu_items';

    protected $guarded = [];

    public function menus()
    {
        return $this->belongsToMany(Menu::class)
            ->using(MenuMenuItem::class, 'menu_menu_items')
            ->withPivot(['order', 'parent_id'])
            ->withTimestamps();
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
            $html = '<span class="badge badge--success">'.trans('Active').'</span>';
        }else{
            $html = '<span><span class="badge badge--warning">'.trans('Inactive').'</span></span>';
        }
        return $html;
    }

}
