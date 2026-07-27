<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'label',
        'url',
        'type',
        'page_id',
        'parent_id',
        'order',
        'is_active',
        'open_new_tab',
        'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
    ];

    /**
     * Get the page associated with this menu item
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the parent menu item
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get the children menu items
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get active children
     */
    public function activeChildren()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->where('is_active', true)->orderBy('order');
    }

    /**
     * Scope untuk menu yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk menu parent (top level)
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
