<?php
namespace App\Helpers\Cron;

use App\Models\Mimic;
use App\Helpers\FileUpload;

class UploadToAws
{
    public function upload()
    {
        //get 2 mimics where aws_file is null
        foreach (Mimic::whereNull('aws_file')->limit(3)->get() as $model) {
            //remove "/" from the beginning of a string
            $path = (new Mimic)->getFileOrPath($model->user_id, null, $model);
            $url = (new FileUpload)->upload(public_path().$path.$model->file, ltrim($path, "/"), null, 'aws');

            $model->update(['aws_file' => $url]);
        }
    }
}