<?php

namespace App\Api\V2\User\Requests;

use Dingo\Api\Http\FormRequest;
use App\Api\V2\User\Models\User;

class UserRequest extends FormRequest
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
        $id = request('id');
        return [
            'email' => sprintf('unique:users,email,%d,id|email', $id),
            'username' => sprintf('unique:users,username,%d,id|regex:%s', $id, User::USERNAME_REGEX),
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
            'email.unique' => __('api/user/validations.email.unique'),
            'email.email' => __('api/user/validations.email.email'),
            'username.unique' => __('api/user/validations.username.unique'),
            'username.regex' => __('api/user/validations.username.regex'),
        ];
    }
}
