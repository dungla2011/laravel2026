<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait StorageImageTrait
{
    public function storeUploadFile(UploadedFile $fileData, $folderStore)
    {
        $dataUpload = null;
        $fileNameOrg = $fileData->getClientOriginalName();
        $fileHash = Str::random(20).'.'.$fileData->getClientOriginalExtension();
        $filePath = $fileData->storeAs('public/'.$folderStore.'/'.auth()->id(), $fileHash);

        if ($filePath) {
            $dataUpload = [
                'file_name' => $fileNameOrg,
                //'file_path'=>$filePath
                'file_path' => Storage::url($filePath),
            ];
        }

        return $dataUpload;
    }
}
