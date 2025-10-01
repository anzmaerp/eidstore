<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\TaxModule\app\Traits\VatTaxManagement;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $icon
 * @property int $parent_id
 * @property int $position
 * @property int $home_status
 * @property int $priority
 */
class CategoryUpdateRequest extends FormRequest
{
    use VatTaxManagement;

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'image' => 'mimes:jpg,jpeg,png|max:2048',
            'priority' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('category_name_is_required'),
            'image.mimes' => translate('category_image_must_be_jpg_jpeg_png'),
            'image.max' => translate('category_image_must_not_exceed_2mb'),
            'priority.required' => translate('category_priority_is_required'),
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $taxData = $this->getTaxSystemType();
                $categoryWiseTax = $taxData['categoryWiseTax'];

                if ($categoryWiseTax && (!isset($this['tax_ids']) || empty($this['tax_ids']))) {
                    $validator->errors()->add(
                        'tax', translate('Please_add_your_category_tax') . '!'
                    );
                }
            }
        ];
    }

}
