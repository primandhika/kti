<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Arsip extends Model
{
    use SoftDeletes;

    protected $table = 'arsip';

    protected $fillable = [
        'nama_file',
        'file_name',
        'path',
        'mime_type',
        'jenis',
        'kategori_arsip',
        'tags',
        'deskripsi',
        'folder',
        'ukuran',
        'original_dimensions',
        'is_public',
        'alt_text',
        'caption',
        'uploaded_by',
    ];

    protected $casts = [
        'ukuran' => 'integer',
        'tags' => 'array',
        'is_public' => 'boolean',
    ];

    protected $appends = ['url', 'ukuran_readable'];

    /**
     * Relationship: User yang upload
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Relationship: Usage tracking (polymorphic)
     */
    public function usages()
    {
        return $this->hasMany(ArsipUsage::class);
    }

    /**
     * Get full URL dari file
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Get ukuran file dalam format readable
     */
    public function getUkuranReadableAttribute()
    {
        $bytes = $this->ukuran;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is image
     */
    public function isImage()
    {
        return $this->jenis === 'image';
    }

    /**
     * Check if file is document
     */
    public function isDocument()
    {
        return in_array($this->jenis, ['document', 'pdf']);
    }

    /**
     * Get usage summary (digunakan di mana saja)
     */
    public function getUsageSummary()
    {
        return $this->usages()
            ->select('usable_type', \DB::raw('count(*) as count'))
            ->groupBy('usable_type')
            ->get()
            ->mapWithKeys(function ($usage) {
                $type = class_basename($usage->usable_type);
                return [$type => $usage->count];
            });
    }
}
