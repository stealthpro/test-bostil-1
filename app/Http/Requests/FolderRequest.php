<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderRequest extends FormRequest
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
     *     schema="FolderRequest",
     *     type="object",
     *     @OA\Property(property="title", type="string", description="Название"),
     * )
     */
    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('folders')->ignore($this->folder),
            ]
        ];
    }
}
