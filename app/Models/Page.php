<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Page extends Model
{
    const CONTENT_MAX_SIZE = 1024 * 1024;

    protected $fillable = ['title', 'content', 'published', 'folder_id'];

    protected $casts = [
        'published' => 'boolean'
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeNotPublished($query)
    {
        return $query->where('published', false);
    }


    protected static function boot()
    {
        parent::boot();

        self::deleted(function (self $page) {
            $path = config('pages.path');

            File::delete("$path/{$page->id}.html");
        });
    }

}
