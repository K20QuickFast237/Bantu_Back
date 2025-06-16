<?php
namespace App\Helper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class CommonHelper {
    public static function uploadFile(UploadedFile $file, $path, $oldFile = ''): string {
        if (!empty($file)) {
            // Remove old file
            if (! empty($oldFile)) {
                Storage::delete("public/$path/$oldFile");
            }

            //upload new file
            $path = $file->store("public/$path");
            $parts = explode('/', $path);

            return end($parts);
        }

        return '';
    }

    public static function removeOldFile($oldFile): void {
        if (!empty($oldFile)) {
            Storage::delete($oldFile);  // Delete file from local
        }
    }

    public static function getFileValidationRule(string $key, $type, $size=(1*500)): array {
        if (request()->hasFile($key)) {
            return [File::types($type)->max($size)];
        }

        return ['string'];
    }
}