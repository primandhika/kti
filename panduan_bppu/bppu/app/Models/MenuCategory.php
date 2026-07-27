<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function canteenMenus(): HasMany
    {
        return $this->hasMany(CanteenMenu::class, 'category_id');
    }

    public function activeMenus(): HasMany
    {
        return $this->hasMany(CanteenMenu::class, 'category_id')
                    ->where('is_active', true)
                    ->orderBy('display_order');
    }
}
