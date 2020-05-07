<?php

namespace App\Services;

use App\Http\Requests\PageUpdateRequest;
use App\Models\Page;
use Illuminate\Support\Facades\DB;

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
            if ($page->content === null) {
                throw new \Exception('Page content should not be null.', 400);
            }

            DB::beginTransaction();

            $page->update(['published' => true]);

            if ($page->published) {
                (new FileService)->storePage($page);
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return $page;
    }
}