<?php

namespace App\Http\Requests;

use App\Models\ProviderProfile;
use App\Rules\AddressFormatRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique(ProviderProfile::class)->ignore($this->user()->providerProfile->id),
            ],
            'description' => 'sometimes|string|nullable|max:300',
            'address' => ['sometimes', 'string', 'nullable', 'max:50', new AddressFormatRule],
            'website' => 'sometimes|url|nullable|max:30', // change to active_url (only working link can be accepted)
        ];

    }
}
