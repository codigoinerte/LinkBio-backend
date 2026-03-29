<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['name', 'description'];
}
