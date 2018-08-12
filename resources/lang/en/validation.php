<?php

return [

    'mimic' => [
        'create' => [
            'file_should_be_image_video' => 'File should be an image or a video',
            'file_mimes_only_photo_or_video' => 'File should only be a photo (jpg or png) or a video (mp4).',
            'video_thumbnail_mimes_only_photo' => 'File should only be a photo (jpg or png).',
            'video_thumbnail_required' => 'Video thumbnail is required',
            'hashtags_are_required' => 'Hashtags are required',
            'height_is_required' => 'Image/video height is required and should be integer',
            'width_is_required' => 'Image/video width is required and should be integer',
            'thumb_height_is_required' => 'Thumbnail image height is required and should be integer',
            'thumb_width_is_required' => 'Thumbnail image width is required and should be integer',
        ]
    ],    
    'file_should_be_image' => 'File should be an image',
    'error_upload_file' => "There was an error uploading the file.",
    'is_not_a_picture' => 'is not a picture',
    'is_not_a_video' => 'is not a video',
    'mimic_is_deleted' => "This Mimic has been deleted, you can't respond to this Mimic anymore",
];
