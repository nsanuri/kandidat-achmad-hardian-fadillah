<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpCode extends Model
{
    protected $table = 'otps';
    public $timestamps = false; 
    protected $fillable = ['email','otp','created_at','expires_at','used'];

    public function isExpired()
    {
        return Carbon::now()->gt(Carbon::parse($this->expires_at));
    }
}
