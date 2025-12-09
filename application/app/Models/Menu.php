<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $guarded = [];

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_menu_items')
            ->using(MenuMenuItem::class)
            ->withTimestamps()
            ->orderBy('menu_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuMenuItem::class);
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
