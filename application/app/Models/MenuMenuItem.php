<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MenuMenuItem extends Pivot
{
    protected $table = 'menu_menu_items';

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
