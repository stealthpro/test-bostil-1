<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageCreateRequest extends FormRequest
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
     *     schema="PageCreateRequest",
     *     type="object",
     *     @OA\Property(property="title", type="string", description="Название"),
     *     @OA\Property(property="folder_id", type="integer", description="ID папки"),
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
            'folder_id' => 'required|integer|nullable|exists:folders,id'
        ];
    }
}
