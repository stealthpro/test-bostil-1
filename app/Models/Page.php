<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    const CONTENT_MAX_SIZE = 1024 * 1024;

    protected $fillable = ['title', 'content', 'published', 'folder_id'];

    protected $casts = [
        'published' => 'boolean',
        'folder_id' => 'integer'
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
            (new FileService())->deletePage($page);
        });
    }

}
