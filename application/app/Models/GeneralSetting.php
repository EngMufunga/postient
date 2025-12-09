<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    protected $casts = ['mail_config' => 'object','sms_config' => 'object','global_shortcodes' => 'object','socialite_credentials' => 'object','meta_credential' => 'object','social_app_credential' => 'object'];

    public function scopeSiteName($query, $pageTitle)
    {
        $pageTitle = empty($pageTitle) ? '' : ' - ' . $pageTitle;
        return $this->site_name . $pageTitle;
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function(){
            Cache::forget('GeneralSetting');
        });
    }
}
