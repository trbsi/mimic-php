<?php
namespace App\Helpers;

use Exception;
use Image;
use Imagick;
use Illuminate\Support\Facades\Storage;

class FileUpload
{
    public const FILE_UPLOAD_SERVER = 'server';
    public const FILE_UPLOAD_AWS = 'aws';

    /**
     * Upload file to S3
     *
     * @param object $file File object. Access via: $request->file('file_name')
     * @param string $path Any path you want to for S3 or your server
     * @param string $uploadWhere Where to upload file to, S3 or server?
     * @param array|string $allowType What kind of file to allow to upload
     * @return string File url
     */
    public function upload($file, $path, $uploadWhere, $allowType = null)
    {
        if ($file) {
            if ($allowType !== null) {
                $this->validateFile($allowType, $file);
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

        abort(400, __('file.errors.file_not_chosen'));
    }

    /**
     * Upload file to Server
     *
     * @param object $file File object
     * @param string $path Path you want to save file to
     * @return string URL to a file
     */
    private function uploadToServer(string $path, object $file): string
    {
        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            //save file info
            $fileInfo['mime'] = $file->getMimeType();
            //generate name
            $fileName = sprintf('%s.%s', md5(time() . mt_rand()), $file->getClientOriginalExtension());
            //move file
            $file->move($path, $fileName);
            //correct image orientation
            $this->correctImageOrientation($path.$fileName, $fileInfo);
            return $fileName;
        } catch (Exception $e) {
            abort(400, __('validation.error_upload_file'));
        }
    }

    /**
     * @param string $relativePath Relative path where you want to upload file on S3
     * @param string $sourceFile Path to a file, including file itself
     * @return string URL to a file
     */
    private function uploadToAws(string $relativePath, string $sourceFile): string
    {
        try {
            $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
            $fileName = sprintf('%s.%s', md5(time() . mt_rand()), $extension);
            $destinationFile = ltrim($relativePath, '/').$fileName;
            $uploadedFile = Storage::cloud()->put($destinationFile, fopen($sourceFile, 'r+'), 'public');
            return Storage::cloud()->url($destinationFile);
        } catch (Exception $e) {
            abort(500, trans('validation.error_upload_file'));
        }
    }

    /**
     * Validate if file is allowed to upload
     *
     * @param array|string $allowType
     * @param object $file File object, got through: $request->file('file_name')
     * @return void
     */
    private function validateFile($allowType, object $file): void
    {
        if (is_array($allowType)) {
            $fileAllowed = false;
            foreach ($allowType as $type) {
                //check if allowed type of a file can be found inside Mime type. If you can find it that means this file is allowed
                if (strpos($file->getMimeType(), $type) !== false) {
                    $fileAllowed = true;
                }
            }
            if ($fileAllowed === false) {
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
     *
     * @param Mimic|MimicResponse $model Mimic or MimicResponse model
     * @param string $file This is name of an image to get path to
     */
    public function resizeAndLowerQuality(object $model, string $file): ?object
    {
        try {
            $imagePath = $model->getAbsolutePathToFile($model->user_id, $file, $model);
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $imagePath);

            if (strpos($mime, 'image') !== false) {
                $img = Image::make($imagePath);
             
                // resize the image to a width of 1600 and constrain aspect ratio (auto height)
                // prevent possible upsizing
                $image = $img->resize(1600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $img->save($imagePath, 60);

                return $image;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Correct image orientation
     *
     * @param  string $filePath Path to image, e.g. public/files/user/96/2018/03/6a97012502fa31c28d9767c4eb49d678.jpg
     * @param array $fileInfo Information about file, like MIME
     * @see https://stackoverflow.com/questions/19456036/detect-exif-orientation-and-rotate-image-using-imagemagick
     * @return void
     */
    public function correctImageOrientation($filePath, $fileInfo)
    {
        if (strpos($fileInfo['mime'], 'image') === false) {
            return;
        }
        
        $image = new Imagick($filePath);
        switch ($image->getImageOrientation()) {
            case Imagick::ORIENTATION_TOPLEFT:
                break;
            case Imagick::ORIENTATION_TOPRIGHT:
                $image->flopImage();
                break;
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateImage("#000", 180);
                break;
            case Imagick::ORIENTATION_BOTTOMLEFT:
                $image->flopImage();
                $image->rotateImage("#000", 180);
                break;
            case Imagick::ORIENTATION_LEFTTOP:
                $image->flopImage();
                $image->rotateImage("#000", -90);
                break;
            case Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateImage("#000", 90);
                break;
            case Imagick::ORIENTATION_RIGHTBOTTOM:
                $image->flopImage();
                $image->rotateImage("#000", 90);
                break;
            case Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateImage("#000", -90);
                break;
            default: // Invalid orientation
                break;
        }
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        $image->writeImage();
    }
}
