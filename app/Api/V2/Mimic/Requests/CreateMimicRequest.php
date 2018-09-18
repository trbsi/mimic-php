<?php

namespace App\Api\V2\Mimic\Requests;

use Dingo\Api\Http\FormRequest;

class CreateMimicRequest extends FormRequest
{
    //https://mattstauffer.com/blog/laravel-5.0-form-requests/
    
    /** @var array Validation rules */
    protected $rules = [];

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
        $this->rules = [
            'mimic_file' => 'required|file|mimes:jpeg,png,jpg,mp4',
            'meta.height' => 'required|integer',
            'meta.width' => 'required|integer',
            'meta.color' => 'required',
            'description' => 'max:1000',
        ];

        $this->videoThumbnailRule();

        return $this->rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mimic_file.required' => __('api/mimic/validations.create.file_should_be_image_video'),
            'mimic_file.file' => __('api/mimic/validations.create.file_should_be_image_video'),
            'mimic_file.mimes' => __('api/mimic/validations.create.file_mimes_only_photo_or_video'),
            'video_thumbnail.mimes' => __('api/mimic/validations.create.video_thumbnail_mimes_only_photo'),
            'video_thumbnail.file' => __('api/mimic/validations.create.file_should_be_image_video'),
            'video_thumbnail.required' => __('api/mimic/validations.create.video_thumbnail_required'),
            'meta.height.required' =>  __('api/mimic/validations.create.height_is_required'),
            'meta.height.integer' =>  __('api/mimic/validations.create.height_is_required'),
            'meta.width.required' =>  __('api/mimic/validations.create.width_is_required'),
            'meta.width.integer' =>  __('api/mimic/validations.create.width_is_required'),
            'meta.thumbnail_height.required' =>  __('api/mimic/validations.create.thumb_height_is_required'),
            'meta.thumbnail_height.integer' =>  __('api/mimic/validations.create.thumb_height_is_required'),
            'meta.thumbnail_width.required' =>  __('api/mimic/validations.create.thumb_width_is_required'),
            'meta.thumbnail_width.integer' =>  __('api/mimic/validations.create.thumb_width_is_required'),
            'description.max' =>  __('api/mimic/validations.create.description_max'),
            'meta.color.required' => __('api/mimic/validations.create.meta_color'),
        ];
    }

    /**
     * Validate video thumbnail
     *
     * Validate video thumbnail only if mimic_file is video
     *
     * @return void
     */
    private function videoThumbnailRule()
    {
        if ($this->mimic_file && strpos($this->mimic_file->getMimeType(), 'video') !== false) {
            $this->rules['video_thumbnail'] = ['required', 'file', 'mimes:jpeg,png,jpg'];
            $this->rules['meta.thumbnail_height'] = ['required', 'integer'];
            $this->rules['meta.thumbnail_width'] = ['required', 'integer'];
        }
    }
}
