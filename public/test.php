<?php
/*
function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

var_dump(getDirContents('files/seeds'));
*/

//get all users from the database as count
$users = 30;
	
	$rootDir = 'files/seeds';
    $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($rootDir));

    $files = array(); 

    foreach ($rii as $key=>$file) {
        if ($file->isDir()){ 
            continue;
        }

        $groupFileDir = explode("/", str_replace("\\", "/", $file->getPathname()))[2];
        $files[$groupFileDir][] = $file->getFileName(); 

    }

    //var_dump($files);

    //main mimic and its creator
    $mainUserId = 1;
    foreach ($files as $dirName => $filesTmp) { 
        foreach ($filesTmp as $arrayKey => $file) {

            //get file info
            $path_parts = pathinfo($file);
            $mime = mime_content_type($rootDir.'/'.$dirName.'/'.$file);

            //main mimic
            if($arrayKey == 0) {
                $userIdTmp = $mainUserId;
            } 
            //response mimic
            else {
                $userIdTmp = rand($mainUserId, $users);
            }

            //copy files to another directory
            $path = '/files/user/'.$userIdTmp.'/1970/01';
            if(!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $fileName = md5(mt_rand()).'.'.$path_parts['extension'];
            //copy('/files/seeds/'.$dirName.'/'.$file, $path.'/'.$fileName);

            //insert into database
            $data = 
            [
                'file' => $fileName,
                'mimic_type' => (strpos($mime, 'image') !== false) ? Mimic::TYPE_PIC : Mimic::TYPE_VIDEO,
                'upvote' => rand(1, 35),
                'user_id' => $userIdTmp
            ];

            if($arrayKey == 0) {
            	//$mainMimic = $mimic->create($data);
            } 
            //response mimic
            else {
                $mimicResponses[] = $data;
            }

        }

         //$mainMimic->mimicResponses()->createMany($mimicResponses);

    }
