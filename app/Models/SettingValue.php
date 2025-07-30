<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingValue extends Model
{
    protected $fillable = [
        'name',
        'value',
        'description',
    ];
}
