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
    use WithFaker;

    public function testGetAll()
    {
        $folders = factory(Folder::class, 20)->create();
        $pages = factory(Page::class, 40)->create();

        $params = [
            'page' => random_int(1, 4),
            'per_page' => random_int(1, 15),
            'sort' => 'desc'
        ];

        $queryString = implode(
            '&',
            array_map(fn($k, $v) => "$k=$v", array_keys($params), array_values($params))
        );

        $response = $this->get("/api/pages?$queryString");
        $response->assertStatus(200);

        $responsePages = $pages
            ->whereIn('id', Arr::pluck($response->json('data'), 'id'))
            ->all();

        foreach ($responsePages as $page) {
            $response->assertJsonFragment(PageListResource::make($page)->jsonSerialize());
        }
    }

    public function testCreatePage()
    {
        $folders = factory(Folder::class, 20)->create();

        $payload = [
            'title' => $this->faker->unique()->words(random_int(2, 3), true),
            'folder_id' => optional(Folder::query()->inRandomOrder()->first())->id
        ];

        $response = $this->post('/api/pages', $payload);

        $response->assertStatus(201);
    }

    public function testShowPage()
    {
        $folders = factory(Folder::class, 20)->create();
        $page = factory(Page::class)->create();

        $response = $this->get("/api/pages/{$page->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(PageResource::make($page)->jsonSerialize());
    }

    public function testUpdatePage()
    {
        $folders = factory(Folder::class, 20)->create();
        $page = factory(Page::class)->create();

        $payload = [
            'title' => $this->faker->unique()->words(random_int(2, 3), true),
            'content' => trim($this->faker->randomHtml()),
            'folder_id' => optional(Folder::query()->inRandomOrder()->first())->id
        ];

        $response = $this->put("/api/pages/{$page->id}", $payload);

        $response->assertStatus(200);

        $page->fill($payload);
        $page->updated_at = $response->json('data.updated_at');
        if ($page->isDirty('content')) {
            $page->published = false;
        }
        $response->assertJsonFragment(PageResource::make($page)->jsonSerialize());
    }

    public function testDeletePage()
    {
        $page = factory(Page::class)->create();

        $response = $this->delete("/api/pages/{$page->id}");

        $response->assertStatus(204);
    }

    public function testPublishPage()
    {
        Storage::fake('public');

        $folders = factory(Folder::class, 20)->create();
        $page = factory(Page::class)->create();

        $response = $this->post("/api/pages/{$page->id}/publish");

        $response->assertStatus(200);
    }

    public function testPublishPageWithoutContent()
    {
        Storage::fake('public');

        $folders = factory(Folder::class, 20)->create();
        $page = factory(Page::class)->create(['content' => null]);

        $response = $this->post("/api/pages/{$page->id}/publish");

        $this->assertEquals(400, $response->exception->getCode());
    }
}
