<?php

namespace App\Http\Requests\Kids;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(title="Kids Image Upload Request", type="object", required={"photo"})
 */
class KidsImageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /**
     * @OA\Property(property="photo", type="string", format="binary", description="Rasm fayli")
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ];
    }
}