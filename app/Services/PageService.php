<?php

namespace App\Services;

use App\Http\Requests\PageUpdateRequest;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PageService
{

    /**
     * @param  \App\Models\Page  $page
     * @param  \App\Http\Requests\PageUpdateRequest  $request
     *
     * @return \App\Models\Page
     */
    public function update(Page $page, PageUpdateRequest $request)
    {
        $page->fill($request->validated());

        if ($page->isDirty('content')) {
            $page->published = false;
        }

        $page->save();

        return $page;
    }

    /**
     * @param  \App\Models\Page  $page
     *
     * @return \App\Models\Page
     * @throws \Throwable
     */
    public function publish(Page $page)
    {
        try {
            DB::beginTransaction();

            if ($page->content === null) {
                throw new \Exception('Page content should not be null.', 400);
            }

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