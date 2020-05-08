<?php

namespace Tests\Feature;

use App\Http\Resources\PageListResource;
use App\Http\Resources\PageResource;
use App\Models\Folder;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageControllerTest extends TestCase
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

    public function testGetAll()
    {
        factory(Folder::class, 20)->create();
        $pages = factory(Page::class, 40)->create();

        $params = [
            'page' => random_int(1, 4),
            'per_page' => random_int(1, 15),
            'sort' => 'desc'
        ];

        $response = $this->get(route('pages.index', $params));
        $response->assertStatus(200);

        $responsePages = $pages
            ->whereIn('id', Arr::pluck($response->json('data'), 'id'))
            ->all();

        foreach ($responsePages as $page) {
            $response->assertJsonResourceFragment(PageListResource::make($page));
        }
    }

    public function testCreatePage()
    {
        factory(Folder::class)->create();

        $payload = [
            'title' => $this->faker->unique()->words(random_int(2, 3), true),
            'folder_id' => optional(Folder::query()->inRandomOrder()->first())->id
        ];

        $response = $this->post(route('pages.store'), $payload);

        $page = Page::query()->firstWhere($payload);
        $this->assertNotNull($page);

        $response->assertStatus(201);
        $response->assertJsonResource(PageResource::make($page));
    }

    public function testShowPage()
    {
        factory(Folder::class)->create();
        $page = factory(Page::class)->create();

        $response = $this->get(route('pages.show', [$page->id]));

        $response->assertStatus(200);
        $response->assertJsonResource(PageResource::make($page));
    }

    public function testUpdatePage()
    {
        factory(Folder::class)->create();
        $page = factory(Page::class)->create();

        $payload = [
            'title' => $this->faker->unique()->words(random_int(2, 3), true),
            'content' => trim($this->faker->randomHtml()),
            'folder_id' => optional(Folder::query()->inRandomOrder()->first())->id
        ];

        $response = $this->put(route('pages.update', [$page->id]), $payload);

        $response->assertStatus(200);

        $page->fill($payload);
        $page->updated_at = $response->json('data.updated_at');
        if ($page->isDirty('content')) {
            $page->published = false;
        }
        $response->assertJsonResource(PageResource::make($page));
    }

    public function testDeletePage()
    {
        $page = factory(Page::class)->create();

        $response = $this->delete(route('pages.destroy', [$page->id]));

        $response->assertStatus(204);
    }

    public function testPublishPage()
    {
        factory(Folder::class)->create();
        $page = factory(Page::class)->create();

        $response = $this->post(route('pages.publish', [$page->id]));

        $page->published = true;

        $response->assertStatus(200);
        $response->assertJsonResource(PageResource::make($page));
        Storage::disk($this->pagesDisk)->assertExists($page->file_path);
    }

    public function testPublishPageAlreadyPublished()
    {
        factory(Folder::class)->create();
        $page = factory(Page::class)->create([
            'published' => true
        ]);

        $response = $this->post(route('pages.publish', [$page->id]));

        $response->assertStatus(404);
    }

    public function testPublishPageWithoutContent()
    {
        factory(Folder::class)->create();
        $page = factory(Page::class)->create(['content' => null]);

        $response = $this->post(route('pages.publish', [$page->id]));

        $this->assertEquals(400, $response->exception->getCode());
    }
}
