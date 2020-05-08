<?php

namespace Tests\Feature;

use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class FolderControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testGetAll()
    {
        $folders = factory(Folder::class, 20)->create();

        $params = [
            'page' => random_int(1, 2),
            'per_page' => random_int(5, 10),
            'sort' => 'desc'
        ];

        $response = $this->get(route('folders.index', $params));

        $response->assertStatus(200);

        $responseFolder = $folders
            ->whereIn('id', Arr::pluck($response->json('data'), 'id'))
            ->all();

        foreach ($responseFolder as $folder) {
            $response->assertJsonResourceFragment(FolderResource::make($folder));
        }
    }

    public function testCreateFolder()
    {
        $payload = [
            'title' => $this->faker->unique()->words(random_int(1, 2), true)
        ];

        $response = $this->post(route('folders.store'), $payload);

        $folder = Folder::query()->firstWhere($payload);
        $this->assertNotNull($folder);

        $response->assertStatus(201);
        $response->assertJsonResource(FolderResource::make($folder));
    }

    public function testShowFolder()
    {
        $folder = factory(Folder::class)->create();

        $response = $this->get(route('folders.show', [$folder->id]));

        $response->assertStatus(200);
        $response->assertJsonResource(FolderResource::make($folder));
    }

    public function testUpdateFolder()
    {
        $folder = factory(Folder::class)->create();

        $payload = [
            'title' => $this->faker->unique()->words(random_int(1, 2), true),
        ];

        $response = $this->put(route('folders.update', [$folder->id]), $payload);

        $response->assertStatus(200);

        $folder->fill($payload);
        $folder->updated_at = $response->json('data.updated_at');
        $response->assertJsonResource(FolderResource::make($folder));
    }

    public function testDeleteFolder()
    {
        $folder = factory(Folder::class)->create();

        $response = $this->delete(route('folders.destroy', [$folder->id]));

        $response->assertStatus(204);
    }
}
