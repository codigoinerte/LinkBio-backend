<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'short_description',
        'link',
        'location',
        'from',
        'to',
        'is_enabled',
        'order',
        'click',
        'visits',
    ];

    public function galery(): HasMany
    {
        return $this->hasMany(GaleryModel::class, 'origin_id')
            ->where('origin', 'projects');
    }
}
