<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageListResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="PageListResource",
     *     type="object",
     *     @OA\Property(property="id", type="integer", description="ID"),
     *     @OA\Property(property="title", type="string", description="Название"),
     *     @OA\Property(property="published", type="boolean", description="Статус"),
     *     @OA\Property(property="folder_id", type="integer", description="ID папки"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'published' => $this->published,
            'folder_id' => $this->folder_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
