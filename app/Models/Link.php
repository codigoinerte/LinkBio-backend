<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['url', 'title', 'description', 'is_enabled', 'order', 'clicks', 'visits', 'category_id', 'user_id'];
}
