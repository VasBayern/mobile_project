<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandleImageTrait
{

    // protected $directoryPath = 'public/hinh-anh/';

    /**
     * Instantiate a new controller instance
     *
     * @param  string $directoryPath
     * 
     * @return void
     */
    protected function __contruct($directoryPath)
    {
        // $this->directoryPath = $directoryPath;
    }

    /**
     * Upload image when request has image file
     * 
     * @param  string $directory
     * @param  string $requestName 
     * @param  file $requestImage
     * 
     * @return string 
     */
    protected function handleUploadImage($directory, $name, $image)
    {
        $this->removeImageDirectory($directory);

        $nameImage = Str::slug($name) . '.' . $image->extension();
        $pathImage = Storage::putFileAs($directory, $image, $nameImage);

        return Storage::url($pathImage);
    }

    /**
     * Rename image when update name
     * 
     * @param  string $directory
     * @param  string $name Current name
     * @param  string $path Current path image
     * @param  string $requestName Request name to update  
     * 
     * @return string 
     */
    protected function renameStorageImage($directory, $name,  $path, $requestName)
    {
        $arrayPathImage = explode('/', $path);
        $oldNameImage = end($arrayPathImage);
        $oldPathImage = $directory . '/' . $oldNameImage;
        $newPathImage = Str::replaceLast(Str::slug($name), Str::slug($requestName), $oldPathImage);

        if ($newPathImage != $oldPathImage) {
            Storage::move($oldPathImage, $newPathImage);
        }

        return Storage::url($newPathImage);
    }

    /**
     * Remove image folder when delete item
     * 
     * @param  string $directory
     */
    protected function removeImageDirectory($directory)
    {
        if (Storage::exists($directory)) {
            Storage::deleteDirectory($directory);
        }
    }
}
