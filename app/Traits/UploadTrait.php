<?php 
namespace App\Traits;

trait UploadTrait
{

    public function uploadFile($file, $path)
    {
        $filename = uniqid().'.'.$file->getClientOriginalExtension();
        return $file->storeAs($path, $filename, 'public');
    }
}