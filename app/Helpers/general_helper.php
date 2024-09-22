<?php

use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use Illuminate\Support\Facades\Validator;
use App\Externals\HCAHBroadcast\BroadcastFactory;
use App\Models\MediaLink;

function constantConfig($name)
{
    return config("constant.$name");
}

function isActive(): int
{
    return constantConfig("active");
}

function globalSeparator()
{
    return constantConfig("column_separator");
}

function currentTime()
{
    return \Carbon\Carbon::now();
}

function serviceConfig($config): string
{
    return config("services.$config");
}

function isProduction(): bool
{
    return app()->isProduction();
}

function returnConfig($name)
{
    return config("constant.$name");
}

function my_array_unique($array, $keep_key_assoc = false)
{
    $duplicate_keys = array();
    $tmp = array();

    foreach ($array as $key => $val) {
        // convert objects to arrays, in_array() does not support objects
        if (is_object($val))
            $val = (array)$val;

        if (!in_array($val, $tmp))
            $tmp[] = $val;
        else
            $duplicate_keys[] = $key;
    }

    foreach ($duplicate_keys as $key)
        unset($array[$key]);

    return $keep_key_assoc ? $array : array_values($array);
}

function uploadFileToLocal($file, $path, $extra = [])
{
    try{
        $permission = "";
        $response = [];
   
        if (!empty($file)) {
            $link = $file->store($path, 'public'); // Store in storage/app/public/
            $filename=$file->getClientOriginalName();
            
            $insert= [
                "filename" => $filename,
                "link" => $link,
                "type"=>1
            ];
            $mediaID = insertData(MediaLink::class,[
                "data" => $insert,
                "id" => isActive()
            ]);            
   
            $response = [
                "id" => !empty($mediaID) ? $mediaID : 0,
                "name" => $filename,
                "link" => $link,
            ];
        }
    }catch (\Symfony\Component\HttpFoundation\File\Exception\PartialFileException $e) {
           // report($e);
           
           $response = ["error" => "The file was only partially uploaded. Please try again.",
           "id" => 0,
           "link" => "",
           "name" => "",
           ];
    } catch (\Exception $e) {
        //report($e);
        $response =  ["error" => $e->getMessage(),
           "id" => 0,
           "link" => "",
           "name" => "",
       ];
    }
    return $response;
   
 
}

function uploadFileToS3($file, $path, $extra = [])
{
    try{
        $permission = "";
        // if(isset($extra['bucket']))
        // {
        //     $permission = '';
        //     Config::set('filesystems.disks.s3.bucket', $extra['bucket']);
        //     if(isset($extra['fileName']))
        //     {
        //         Config::set('filesystems.disks.s3.url', "http://".$extra['bucket'].'/');
        //     }
        // }
        $response = [];
   
        if (!empty($file)) {
            if(isset($extra['is_base64']))
            {
                $str = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3);
                $compressFile = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
                $name = time().'_'.$str.'.png';
   
                $compressFile = file_put_contents($path.$name, $compressFile);
                $compressFile = $path . $name;
                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->optimize($compressFile);
                $ext = "png";
                $type = 1;
                $mime = "image/png";
                $format = "image";
                $filePath = public_path($path . $name);
            }
            else if(isset($extra['isCSV']))
            {
                $compressFile = public_path($file);
                $filePath = public_path($file);
                $name = time() . $file;
                $ext = 'csv';
                $format = 'csv';
                $mime = "text/csv";
                $type = 2;
            }
            else
            {
                $name = time() . $file->getClientOriginalName();
                if(isset($extra['fileName']))
                {
                    $name = $extra['fileName'].'.'.$file->getClientOriginalExtension();
                }
   
                $publicPath = public_path($path);
   
                $file->move($publicPath, $name);
   
                $compressFile = $publicPath . $name;
                $mime = trim($file->getClientMimeType());
   
                $format = "";
   
                $type = 1;
   
                switch ($mime) {
                    case "application/pdf":
                        $type = 2;
                        $format = "pdf";
                        break;
                    case "image/jpeg":
                    case "image/jpg":
                    case "image/png":
                        $format = "image";
                        break;
   
                }
                $filePath = $publicPath . $name;
            }
   
            //Storage::disk('s3')->put($filePath, file_get_contents($compressFile),$permission);
            Storage::disk('local')->putFileAs($path,new \Illuminate\Http\File($filePath),$name);
            unlink(new \Illuminate\Http\File($compressFile));
            if(!isset($extra['is_base64']) && !isset($extra['isCSV']))
            {
                $ext = strtolower($file->getClientOriginalExtension());
            }
            $filePath = $path . $name;
            if (empty($extra["no"])) {
   
                $minutes = $extra["minute"] ?? 5;
   
                $link = getS3Url($filePath,$minutes);
                // if(isset($extra['bucket']))
                // {
                //     //$link = Storage::disk('s3')->url($filePath);
                //     if($extra['bucket'] == constantConfig("hcah_in_bucket")) {
                //         $link = serviceConfig("frontend.hcahin_url")."/".$filePath;
                //     } else {
                //         $link = "https://".$extra['bucket']."/".$filePath;
                //     }
                // }
                $expiry = currentTime()->addMinutes($minutes);
                $insert = [
                    "link" => $filePath,
                    "temp_link" => $link,
                    "expiry" => $expiry,
                    "type" => $type,
                    "active" => 1,
                    "created_at" => currentTime()
                ];
                if(!isset($extra['isCSV']))
                {
                    $mediaID = insertData(MediaLink::class, [
                        "data" => $insert,
                        "id" => isActive()
                    ]);
                }
            }
   
            $response = [
                "name" => $name,
                "link" => !empty($link) ? $link : "",
                "expiry" => !empty($expiry) ? $expiry : "",
                "path" => $filePath,
                "id" => !empty($mediaID) ? $mediaID : 0,
                "mime" => $mime,
                "format" => $format,
                "extension" => $ext
            ];
   
        }
    }catch (\Symfony\Component\HttpFoundation\File\Exception\PartialFileException $e) {
           // report($e);
           
           $response = ["error" => "The file was only partially uploaded. Please try again.",
           "id" => 0,
           "link" => "",
           "name" => "",
           "expiry" => "",
           "path" => "",
           "mime" => "",
           "format" => "",
           "extension" => ""
           ];
    } catch (\Exception $e) {
        //report($e);
        $response =  ["error" => $e->getMessage(),
           "id" => 0,
           "link" => "",
           "name" => "",
           "expiry" => "",
           "path" => "",
           "mime" => "",
           "format" => "",
           "extension" => ""
       ];
    }
    return $response;
   
 
}
