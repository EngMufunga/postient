<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $casts = [
        'status' => 'boolean',
        'code' => 'string',
        'extra' => 'object',
        'input_form'=> 'object',
        'supported_currencies'=>'object'
    ];

    public function currencies()
    {
        return $this->hasMany(GatewayCurrency::class, 'method_code', 'code');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function singleCurrency()
    {
        return $this->hasOne(GatewayCurrency::class, 'method_code', 'code')->orderBy('id','desc');
    }

    public function scopeCrypto()
    {
        return $this->crypto == Status::ENABLE ? 'crypto' : 'fiat';
    }

    public function scopeAutomatic()
    {
        return $this->where('code', '<', 1000);
    }

    public function scopeManual()
    {
        return $this->where('code', '>=', 1000);
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
            $html = '<span class="badge badge--success">'.trans("Enable").'</span>';
        }else{
            $html = '<span class="badge badge--warning">'.trans("Disable").'</span>';
        }

        return $html;
    }

}
