<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContactDetails extends Model
{
    protected $fillable = [
        'tel_no',
        'mobile_no',
    ];
}
