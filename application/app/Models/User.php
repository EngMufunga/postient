<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use App\Traits\HasPermissions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, UserNotify, HasPermissions;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'address' => 'object',
            'ver_code_send_at' => 'datetime'
        ];
    }


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=', Status::PAYMENT_INITIATE);
    }


    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname || $this->lastname ? $this->firstname . ' ' . $this->lastname : '@'.$this->username,
        );
    }

    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', Status::USER_ACTIVE);
    }

    public function scopeBanned()
    {
        return $this->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', Status::UNVERIFIED);
    }


    public function scopeEmailVerified()
    {
        return $this->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance()
    {
        return $this->where('balance','>', 0);
    }

}
