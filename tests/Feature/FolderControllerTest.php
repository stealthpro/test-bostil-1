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
    use WithFaker;

    public function testGetAll()
    {
        $folders = factory(Folder::class, 20)->create();

        $params = [
            'page' => random_int(1, 2),
            'per_page' => random_int(5, 10),
            'sort' => 'desc'
        ];

        $queryString = implode(
            '&',
            array_map(fn($k, $v) => "$k=$v", array_keys($params), array_values($params))
        );

        $response = $this->get("/api/folders?$queryString");

        $response->assertStatus(200);

        $responseFolder = $folders
            ->whereIn('id', Arr::pluck($response->json('data'), 'id'))
            ->all();

        foreach ($responseFolder as $folder) {
            $response->assertJsonFragment(FolderResource::make($folder)->jsonSerialize());
        }
    }

    public function testCreateFolder()
    {
        $payload = [
            'title' => $this->faker->unique()->words(random_int(1, 2), true)
        ];

        $response = $this->post('/api/folders', $payload);

        $response->assertStatus(201);
    }

    public function testShowFolder()
    {
        $folder = factory(Folder::class)->create();

        $response = $this->get("/api/folders/{$folder->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(FolderResource::make($folder)->jsonSerialize());
    }

    public function testUpdateFolder()
    {
        $folder = factory(Folder::class)->create();

        $payload = [
            'title' => $this->faker->unique()->words(random_int(1, 2), true),
        ];

        $response = $this->put('/api/folders/' . $folder->id, $payload);

        $response->assertStatus(200);

        $folder->fill($payload);
        $folder->updated_at = $response->json('data.updated_at');
        $response->assertJsonFragment(FolderResource::make($folder)->jsonSerialize());
    }

    public function testDeleteFolder()
    {
        $folder = factory(Folder::class)->create();

        $response = $this->delete('/api/folders/' . $folder->id);

        $response->assertStatus(204);
    }
}
