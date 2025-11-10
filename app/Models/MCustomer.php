<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCustomer extends Model
{
    protected $table = 'mcustomers';
    protected $primaryKey = 'customer_id';
    protected $fillable = ['email','name','password'];
    public $timestamps = true;
    protected $hidden = ['password'];
}
