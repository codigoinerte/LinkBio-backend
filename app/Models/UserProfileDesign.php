<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfileDesign extends Model
{
    protected $table = 'user_designs';

    protected $fillable = [
        'user_id',
        'theme_id',
        
        'wallpaper_type',
        'wallpaper_file',
        'wallpaper_color_type',
        'wallpaper_color_custom',
        'wallpaper_pattern_type',
    ];
}
