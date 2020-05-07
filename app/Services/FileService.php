<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class FileService
{

    /**
     * @param  string  $path
     *
     * @param  string  $disk
     *
     * @return bool
     */
    public function checkDirectory(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->makeDirectory($path);
    }

    /**
     * @param  \App\Models\Page  $page
     *
     * @return bool
     * @throws \Exception
     */
    public function storePage(Page $page): bool
    {
        $disk = config('pages.disk');
        $path = config('pages.path');

        if (! $this->checkDirectory($path, $disk)) {
            throw new \Exception("Output folder not created.", 400);
        }

        return Storage::disk($disk)->put("$path/{$page->id}.html", $page->content);
    }

    /**
     * @param $page
     *
     * @return bool
     */
    public function deletePage($page): bool
    {
        $config = config('pages');

        return Storage::disk($config['disk'])->delete("{$config['path']}/{$page->id}.html");
    }
}