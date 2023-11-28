<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class merchant_payment_channel extends Model
{
    protected $fillable = ['merchant_id', 'channel_name'];
}
