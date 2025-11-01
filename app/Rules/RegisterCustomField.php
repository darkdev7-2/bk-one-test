<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function getPageSetting;

class RegisterCustomField implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private $isEdit;

    public function __construct($isEdit = false)
    {
        $this->isEdit = $isEdit;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regiCustomFields = json_decode(getPageSetting('register_custom_fields'), true);
        if ($regiCustomFields) {
            foreach ($regiCustomFields as $key => $field) {
                if ($field['validation'] == 'required' && ! $this->isEdit && ! isset($value[$field['name']])) {
                    $fail(__('The :attribute field is required.', ['attribute' => $field['name']]));
                } elseif (in_array($field['type'], ['file', 'camera']) && isset($value[$field['name']]) && ! in_array($value[$field['name']]?->getMimeType(), ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
                    $fail(__('The :attribute field must be a file of type: jpg, jpeg, png, gif.', ['attribute' => $field['name']]));
                }
            }
        }
    }
}
