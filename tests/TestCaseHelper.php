<?php
namespace Tests;

use Illuminate\Http\UploadedFile;

class TestCaseHelper
{
	public static function decodeResponse($response)
	{
		return json_decode($response->getContent(), true);
	}

	public static function returnNewUploadedFile($path, $fileName, $fileType)
	{
		return new UploadedFile(
            $path.$fileName, 
            $fileName, 
            $fileType, 
            filesize($path), 
            null, 
            true);
	}
}
