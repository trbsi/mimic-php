<?php
namespace Tests\Functional\Api\V2\Mimic\Helpers;

class MimicTestHelper
{

	/**
	 * @param  array  $data Array of data from response
	 * @return string
	 */
	public static function getMimicFileName(array $data): string
	{
		return $data['mimic']['file'];
	}

	/**
	 * @param  array  $data Array of data from response
	 * @return string
	 */
	public static function getMimicVideoThumbnailName(array $data): string
	{
		$array = explode('/', $data['mimic']['video_thumb_url']);
        return end($array);
	}
}