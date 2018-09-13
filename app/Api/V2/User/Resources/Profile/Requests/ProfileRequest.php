<?php

namespace App\Api\V2\User\Resources\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bio' => 'string|nullable|max:10000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'bio.string' => __('api/user/profile/validations.bio.string'),
            'bio.max' => __('api/user/profile/validations.bio.max'),
        ];
    }
}
