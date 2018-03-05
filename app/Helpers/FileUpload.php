<?php
namespace App\Helpers;

use Aws\S3\Exception\S3Exception;
use Image;
use App\Helpers\AwsHelper;

class FileUpload
{
    const FILE_UPLOAD_SERVER = 'server';
    const FILE_UPLOAD_AWS = 'aws';

    public function __construct(AwsHelper $awsHelper)
    {
        $this->awsHelper = $awsHelper;
    }

    /**
     * upload file to S3
     * @param  $file File object Access via: $request->file('file_name')
     * @param  $path string any path you want to for S3 or your server
     * @param  $allowType array/string What kind of file to allow to upload
     * @param  $uploadWhere string Where to upload file to, S3 or server?
     * @return string File url
     */
    public function upload($file, $path, $allowType = null, $uploadWhere)
    {
        if ($file) {
            if ($allowType != null) {
                $this->checkFile($allowType, $file);
            }

            switch ($uploadWhere) {
                case self::FILE_UPLOAD_SERVER:
                    return $this->uploadToServer(public_path() . $path, $file);
                    break;

                case self::FILE_UPLOAD_AWS:
                    return $this->uploadToAws($path, $file);
                    break;

            }
        }

        throw new \Exception(trans('file.errors.file_not_chosen'), 400);
    }

    /**
     * Upload file to Server
     * @param  Object $file File object
     * @param  $path string any path you want to for S3 or your server
     * @return string ULR to a file
     */
    private function uploadToServer($path, $file)
    {
        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            //generate name
            $file_name = (md5(time() . mt_rand())) . "." . $file->getClientOriginalExtension();
            //move file
            $file->move($path, $file_name);
            //correct image orientation
            $this->correctImageOrientation($path.$file_name);

            return $file_name;
        } catch (S3Exception $e) {
            abort(500, trans('validation.error_upload_file'));
        }
    }

    /**
     * Upload file to AWS
     * @param  Object $file File object or string path to a file
     * @param  $path string any path you want to for S3 or your server
     * @return string ULR to a file
     */
    private function uploadToAws($path, $file)
    {
        try {
            //this is laravel's object
            if (is_object($file)) {
                $extension = $file->getClientOriginalExtension();
                $sourceFile = $file->getPathName();
            } //this is string path to a file
            else {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $sourceFile = $file;
            }

            $this->s3client = $this->awsHelper->initAwsS3client();

            $result = $this->s3client->putObject(array(
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $path . (md5(time() . mt_rand())) . "." . $extension,
                'SourceFile' => $sourceFile,
                'ContentType' => 'text/plain',
                'ACL' => 'public-read',
            ));

            return $result['ObjectURL'];
        } catch (S3Exception $e) {
            abort(500, $e->getMessage());
            abort(500, trans('validation.error_upload_file'));
        }
    }

    /**
     * check file and its extenstion
     * @param  $allowType [what kind of file to allow to upload]
     * @param  $file            File object, got through: $request->file('file_name')
     */
    private function checkFile($allowType, $file)
    {
        if (is_array($allowType)) {
            $fileAllowed = false;
            foreach ($allowType as $type) {
                //check if allowed type of a file can be found inside Mime type. If you can find it that means this file is allowed
                if (strpos($file->getMimeType(), $type) !== false) {
                    $fileAllowed = true;
                }
            }
            if ($fileAllowed == false) {
                abort(403, trans('validation.file_should_be_image_video'));
            }
        } else {
            switch ($allowType) {
                case 'image':
                    if (strpos($file->getMimeType(), 'image') === false) {
                        abort(403, $file->getClientOriginalName() . " " . trans('validation.is_not_a_picture'));
                    }
                    break;
                case 'video':
                    if (strpos($file->getMimeType(), 'video') === false) {
                        abort(403, $file->getClientOriginalName() . " " . trans('validation.is_not_a_video'));
                    }
                    break;
            }
        }
    }


    /**
     * Resize and lower quality of an image
     * @param  object $model Mimic or MimicResponse model
     * @param  string $file This is name of an image to get path to
     */
    public function resizeAndLowerQuality($model, $file = null)
    {
        try {
            if ($file === null) {
                $tmpFile = $model->file;
            } else {
                $tmpFile = $file;
            }

            $imagePath = $model->getFileOrPath($model->user_id, $tmpFile, $model, false, true);
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $imagePath);

            if (strpos($mime, 'image') !== false) {
                $img = Image::make($imagePath);

                // resize the image to a width of 1600 and constrain aspect ratio (auto height)
                // prevent possible upsizing
                $img->resize(1600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $img->save($imagePath, 60);
            }

            //if there is video thumbnail for video, upload it also
            if ($model->video_thumb && $file === null) {
                $this->resizeAndLowerQuality($model, $model->video_thumb);
            }
        } catch (\Exception $e) {
            //do something here
        }
    }

    /**
     * Correct image orientation
     *
     * @see Tutorial https://obrienmedia.co.uk/blog/fixing-ios-picture-orientation-after-uploading-with-php
     * @param  string $filePath Path to image, e.g. public/files/user/96/2018/03/6a97012502fa31c28d9767c4eb49d678.jpg
     * @return void
     */
    public function correctImageOrientation($filePath)
    {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filePath);
            if ($exif && isset($exif['Orientation'])) {
                if ($exif['Orientation'] != 1) {
                    $img = imagecreatefromjpeg($filePath);
                    $deg = 0;
                    switch ($exif['Orientation']) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filePath
                    imagejpeg($img, $filePath, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }
}
