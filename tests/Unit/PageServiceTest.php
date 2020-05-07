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
    use WithFaker;

    public function testUpdateWithChangeContent()
    {
        $page = factory(Page::class)->create();

        $page->fill([
            'content' => $this->faker->unique()->randomHtml()
        ]);

        $this->assertTrue($page->isDirty('content'));

        $page->published = false;

        $this->assertTrue($page->save());
        $this->assertFalse($page->published);
    }

    public function testPublishWithNullContent()
    {
        Storage::fake('public');

        $page = factory(Page::class)->create();
        $page->update([
            'content' => null,
            'published' => false,
        ]);

        $service = new PageService();

        try {
            $result = $service->publish($page);

            $this->assertFalse($result instanceof Page);
        } catch (\Throwable $exception) {
            $this->assertEquals(new \Exception('Page content should not be null.', 400), $exception);
            $this->assertFalse($page->published);
        }
    }

    public function testPublishWithContent()
    {
        Storage::fake('public');

        $page = factory(Page::class)->create();

        $service = new PageService();

        try {
            $result = $service->publish($page);

            $this->assertEquals($page, $result);
            $this->assertTrue($page->published);
        } catch (\Throwable $exception) {
            $this->assertFalse($exception);
        }
    }
}
