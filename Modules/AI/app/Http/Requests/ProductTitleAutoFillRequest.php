<?php

namespace Modules\AI\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductTitleAutoFillRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'langCode' => 'nullable|string|max:20',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */

    public  function messages(): array{
        return [
            'name.required' => translate('product_name_is_required_to_generate_product_name'),
        ];
    }
    public function authorize(): bool
    {
        return true;
    }
}
