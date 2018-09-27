<?php
namespace Tests\Functional\Api\V2\Mimic\Asserts;

class UserMimicAssert
{

    /**
     * @return array
     */
    public function getUserMimicsJsonStructureOnSuccess(): array
    {
        return [
            'mimics' => [
                '*' => [
                    'id',
                    'user_id',
                    'file',
                    'cloud_file',
                    'video_thumb',
                    'cloud_video_thumb',
                    'mimic_type',
                    'is_private',
                    'upvote',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'file_url',
                    'video_thumb_url',
                ]
            ]
        ];
    }

    /**
     * @param  array $data
     * @return array
     */
    public function getUserMimicsJsonOnSuccess(array $data): array
    {
        return [
            'mimics' => [
                 [
                    'id' => $data['id'],
                    'user_id' => $data['user_id'],
                    'file' => $data['file'],
                    'cloud_file' => $data['cloud_file'],
                    'video_thumb' => $data['video_thumb'],
                    'cloud_video_thumb' => $data['cloud_video_thumb'],
                    'mimic_type' => $data['mimic_type'],
                    'is_private' => $data['is_private'],
                    'upvote' => $data['upvote'],
                    'deleted_at' => $data['deleted_at'],
                    'created_at' => $data['created_at'],
                    'file_url' => $data['file_url'],
                    'video_thumb_url' => $data['video_thumb_url'],
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getUserResponsesJsonStructureOnSuccess(): array
    {
        return [
            'mimics' => [
                '*' => [
                    'id',
                    'user_id',
                    'original_mimic_id',
                    'file',
                    'cloud_file',
                    'video_thumb',
                    'cloud_video_thumb',
                    'mimic_type',
                    'upvote',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'file_url',
                    'video_thumb_url',
                    'original_mimic' => [
                        'id',
                        'user_id',
                        'file',
                        'cloud_file',
                        'video_thumb',
                        'cloud_video_thumb',
                        'mimic_type',
                        'is_private',
                        'upvote',
                        'deleted_at',
                        'created_at',
                        'updated_at',
                        'file_url',
                        'video_thumb_url'
                    ]
                ]
            ]
        ];
    }

    /**
     * @param  array $data
     * @return array
     */
    public function getUserResponsesJsonOnSuccess(array $data): array
    {
        return [
            'mimics' => [
                [
                    'id' => $data['id'],
                    'user_id' => $data['user_id'],
                    'original_mimic_id' => $data['original_mimic_id'],
                    'file' => $data['file'],
                    'cloud_file' => $data['cloud_file'],
                    'video_thumb' => $data['video_thumb'],
                    'cloud_video_thumb' => $data['cloud_video_thumb'],
                    'mimic_type' => $data['mimic_type'],
                    'upvote' => $data['upvote'],
                    'deleted_at' => $data['deleted_at'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['updated_at'],
                    'file_url' => $data['file_url'],
                    'video_thumb_url' => $data['video_thumb_url'],
                    'meta' => [
                        'width' => $data['meta']['width'],
                        'height' => $data['meta']['height'],
                        'thumbnail_width' => $data['meta']['thumbnail_width'],
                        'thumbnail_height' => $data['meta']['thumbnail_height'],
                    ],
                    'original_mimic' => [
                        'id' => $data['original_mimic']['id'],
                        'user_id' => $data['original_mimic']['user_id'],
                        'file' => $data['original_mimic']['file'],
                        'cloud_file' => $data['original_mimic']['cloud_file'],
                        'video_thumb' => $data['original_mimic']['video_thumb'],
                        'cloud_video_thumb' => $data['original_mimic']['cloud_video_thumb'],
                        'mimic_type' => $data['original_mimic']['mimic_type'],
                        'is_private' => $data['original_mimic']['is_private'],
                        'upvote' => $data['original_mimic']['upvote'],
                        'deleted_at' => $data['original_mimic']['deleted_at'],
                        'created_at' => $data['original_mimic']['created_at'],
                        'updated_at' => $data['original_mimic']['updated_at'],
                        'file_url' => $data['original_mimic']['file_url'],
                        'video_thumb_url' => $data['original_mimic']['video_thumb_url']
                    ]
                ]
            ]
        ];
    }
}
