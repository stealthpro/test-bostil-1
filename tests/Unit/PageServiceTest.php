<?php

namespace Tests\Unit;

use App\Models\Page;
use App\Services\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected string $pagesDisk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pagesDisk = config('pages.disk');
        Storage::fake($this->pagesDisk);
    }

    public function testUpdate()
    {
        $page = factory(Page::class)->create([
            'content' => 'text',
            'published' => true
        ]);

        $data = [
            'title' => $page->title,
            'content' => $page->content,
            'folder_id' => $page->folder_id,
        ];

        $service = new PageService();

        $service->update($page, $data);

        $this->assertTrue($page->published);
    }

    public function testUpdateWithChangeContent()
    {
        $page = factory(Page::class)->create([
            'content' => 'text',
            'published' => true
        ]);

        $data = [
            'title' => $page->title,
            'content' => 'new text',
            'folder_id' => $page->folder_id,
        ];

        $service = new PageService();

        $service->update($page, $data);

        $this->assertFalse($page->published);
    }

    public function testPublish()
    {
        $page = factory(Page::class)->create();

        $service = new PageService();

        try {
            $result = $service->publish($page);

            $this->assertEquals($page, $result);
            $this->assertTrue($page->published);
            Storage::disk($this->pagesDisk)->assertExists($page->file_path);
        } catch (\Throwable $exception) {
            $this->assertFalse($exception);
        }
    }

    public function testPublishWithNullContent()
    {
        $page = factory(Page::class)->create([
            'content' => null,
            'published' => false,
        ]);

        $service = new PageService();

        try {
            $result = $service->publish($page);

            $this->assertFalse($result);
        } catch (\Throwable $exception) {
            $this->assertEquals(400, $exception->getCode());
            $this->assertFalse($page->published);
        }
    }
}
