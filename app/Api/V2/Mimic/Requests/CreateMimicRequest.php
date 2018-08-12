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
            'hashtags' => 'required_without:original_mimic_id',
            'meta.height' => 'required|integer',
            'meta.width' => 'required|integer',
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
            'mimic_file.required' => trans('validation.mimic.create.file_should_be_image_video'),
            'mimic_file.file' => trans('validation.mimic.create.file_should_be_image_video'),
            'mimic_file.mimes' => trans('validation.mimic.create.file_mimes_only_photo_or_video'),
            'video_thumbnail.mimes' => trans('validation.mimic.create.video_thumbnail_mimes_only_photo'),
            'video_thumbnail.file' => trans('validation.mimic.create.file_should_be_image_video'),
            'video_thumbnail.required' => trans('validation.mimic.create.video_thumbnail_required'),
            'hashtags.required_without' => trans('validation.mimic.create.hashtags_are_required'),
            'meta.height.required' =>  trans('validation.mimic.create.height_is_required'),
            'meta.height.integer' =>  trans('validation.mimic.create.height_is_required'),
            'meta.width.required' =>  trans('validation.mimic.create.width_is_required'),
            'meta.width.integer' =>  trans('validation.mimic.create.width_is_required'),
            'meta.thumbnail_height.required' =>  trans('validation.mimic.create.thumb_height_is_required'),
            'meta.thumbnail_height.integer' =>  trans('validation.mimic.create.thumb_height_is_required'),
            'meta.thumbnail_width.required' =>  trans('validation.mimic.create.thumb_width_is_required'),
            'meta.thumbnail_width.integer' =>  trans('validation.mimic.create.thumb_width_is_required'),
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
