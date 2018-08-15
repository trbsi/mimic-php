<?php
namespace Tests\Functional\Api\V2\Mimic\Asserts;

class UpvoteDownvoteAssert
{
    /**
     * @return array
     */
    public function getUpvoteDownvoteJsonStructureOnSuccess(): array
    {
        return [
            'type',
            'upvotes',
        ];
    }

    /**
     * @param  array  $data
     * @return array
     */
    public function getUpvoteDownvoteJsonOnSuccess(array $data): array 
    {
        return [
            'type' => $data['type'],
            'upvotes' => $data['upvotes'],
        ];
    }
}