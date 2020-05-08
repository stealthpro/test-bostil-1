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
    use RefreshDatabase;
    use WithFaker;

    protected string $pagesDisk;
    protected string $pagesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pagesDisk = config('pages.disk');
        $this->pagesPath = config('pages.path');
        Storage::fake($this->pagesDisk);
    }

    public function testCheckDirectory()
    {
        $service = new FileService;

        $this->assertTrue($service->checkDirectory($this->pagesPath, $this->pagesDisk));
    }

    public function testStorePage()
    {
        $page = factory(Page::class)->create();

        $service = new FileService;

        try {
            $this->assertTrue($service->storePage($page));
            Storage::disk($this->pagesDisk)->assertExists($page->file_path);
        } catch (\Throwable $exception) {
            $this->assertEquals(400, $exception->getCode());
        }
    }

    public function testDeletePage()
    {
        $page = factory(Page::class)->create();

        Storage::disk($this->pagesDisk)->put($page->file_path, $page->content);
        Storage::disk($this->pagesDisk)->assertExists($page->file_path);

        $service = new FileService;

        self::assertTrue($service->deletePage($page));
        Storage::disk($this->pagesDisk)->assertMissing($page->file_path);
    }
}
