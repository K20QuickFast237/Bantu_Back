<?php

use Illuminate\Support\Facades\Storage;


function getLinkToFile(?string $filePath): ?string 
{
    if (! $filePath) {
        return null;
    }

    if (strpos($filePath, 'http') === false) {
        return url(Storage::url($filePath));
    }

    return $filePath;
}