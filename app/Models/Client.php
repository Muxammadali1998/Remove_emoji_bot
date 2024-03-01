<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nik_name',
        'full_name',
        'chat_id',
        'phone_number',
        'status',
    ];
}
