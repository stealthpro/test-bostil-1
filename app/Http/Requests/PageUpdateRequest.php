<?php

namespace App\Http\Requests;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;

class PageUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @OA\Schema(
     *     schema="PageUpdateRequest",
     *     type="object",
     *     @OA\Property(property="title", type="string", description="Название"),
     *     @OA\Property(property="content", type="string", description="Содержание"),
     *     @OA\Property(property="folder_id", type="string", description="ID папки"),
     * )
     */
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'content' => [
                'nullable',
                'string',
                'max:' . Page::CONTENT_MAX_SIZE
            ],
            'folder_id' => 'required|integer|nullable|exists:folders,id'
        ];
    }
}
