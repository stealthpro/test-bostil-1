<?php

namespace App\Services;

use App\Http\Requests\PageUpdateRequest;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PageService
{

    public function update(Page $page, PageUpdateRequest $request)
    {
        $page->fill($request->validated());

        if ($page->isDirty('content')) {
            $page->published = false;
        }

        $page->save();

        return $page;
    }

    public function publish(Page $page)
    {
        try {
            DB::beginTransaction();

            $page->published = true;
            $page->save();

            $path = config('pages.path');

            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            File::put("$path/{$page->id}.html", $page->content);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return $page;
    }
}