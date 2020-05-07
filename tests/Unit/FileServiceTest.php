<?php

namespace Tests\Unit;

use App\Models\Page;
use App\Services\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    use WithFaker;

    public function testCheckDirectory()
    {
        $disk = config('pages.disk');
        $path = config('pages.path');

        Storage::fake($disk);

        $service = new FileService;

        $this->assertTrue($service->checkDirectory($path));
    }

    public function testStorePage()
    {
        $disk = config('pages.disk');

        Storage::fake($disk);

        $page = factory(Page::class)->create();

        $service = new FileService;

        try {
            $this->assertTrue($service->storePage($page));
        } catch (\Throwable $exception) {
            $this->assertEquals(new \Exception("Output folder not created.", 400), $exception);
        }
    }

    public function testDeletePage()
    {
        $disk = config('pages.disk');
        $path = config('pages.path');

        Storage::fake($disk);

        $page = factory(Page::class)->create();

        Storage::fake($disk)->put("$path/{$page->id}.html", $page->content);

        $service = new FileService;

        self::assertTrue($service->deletePage($page));
    }
}
