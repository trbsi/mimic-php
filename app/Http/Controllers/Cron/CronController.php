<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\Mimic;
use App\Helpers\FileUpload;
use File;

class CronController extends Controller
{
    public function __construct(Mimic $mimic, FileUpload $fileUpload)
    {
        $this->mimic = $mimic;
        $this->fileUpload = $fileUpload;
    }

    /**
     * Upload 
     */
    public function uploadToAws()
    {
        //get 2 mimics where aws_file is null
        foreach ($this->mimic->whereNull('aws_file')->limit(2)->get() as $model) {
            //remove "/" from the beginning of a string
            $path = $this->mimic->getFileOrPath($model->user_id, null, $model);
            $url = $this->fileUpload->upload(public_path().$path.$model->file, ltrim($path, "/"), null, 'aws');

            $model->update(['aws_file' => $url]);
        }
    }
}    
