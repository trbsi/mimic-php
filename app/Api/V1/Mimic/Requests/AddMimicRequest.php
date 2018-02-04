<?php

namespace App\Api\V1\Mimic\Requests;

use Dingo\Api\Http\FormRequest;

class AddMimicRequest extends FormRequest
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
            'file' => 'required|file|mimes:jpeg,png,jpg,mp4',
            'video_thumb' => 'file|mimes:jpeg,png,jpg'
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
            'file.required' => trans('validation.file_should_be_image_video'),
            'file.file' => trans('validation.file_should_be_image_video'),
            'file.mimes' => trans('validation.only_photo_or_video'),
            'video_thumb.mimes' => trans('validation.only_photo_or_video'),
            'video_thumb.file' => trans('validation.file_should_be_image_video'),
        ];
    }
}