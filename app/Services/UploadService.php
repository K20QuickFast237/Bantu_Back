<?php 
namespace App\Services;

use App\Traits\UploadTrait;

class UploadService
{
    use UploadTrait;

    public function upload($file, $path)
    {
        return $this->uploadFile($file, $path);
    }
}