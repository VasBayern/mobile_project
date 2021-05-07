<?php

namespace App\Observers;

use App\Models\ImageProduct;
use App\Models\Product;
use App\Traits\HandleImageTrait;
use Illuminate\Http\Request;

class ProductObserver
{
    use HandleImageTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = false;

    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        if ($this->request->hasFile('images')) {
            $directory = Product::DIRECTORY_PATH . $product->id;
            foreach ($this->request->file('images') as $key => $file) {
                $imageName = $key == 0 ? $this->request->name : $this->request->name . '-' . $key;
                $path = $this->handleUploadImage($directory, $imageName, $file);

                ImageProduct::create([
                    'product_id' => $product->id,
                    'path' => $path
                ]);
            }
        }
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        // $directory = Product::DIRECTORY_PATH . $product->id;

        // $originalName = $product->getOriginal('name');
        // $requestName = $this->request->name;

        // if ($this->request->has('delete_images') && count($this->request->delete_images) > 0) {
        //     foreach ($this->request->delete_images as $deleteImageID) {
        //         $image = ImageProduct::where(['id' => $deleteImageID, 'product_id' => $product->id])->first();
        //         $this->removeImageFile($directory, $image->path);
        //         $image->delete();
        //     }
        // }

        // $imageIDs = ImageProduct::where('product_id', $product->id)->pluck('id');
        // if ($originalName != $requestName) {
        //     foreach ($imageIDs as $imageID) {
        //         $image = ImageProduct::where('id', $imageID)->first();
        //         $originalPath = $image->path;
        //         $image->path = $this->renameStorageImage($directory, $originalName, $originalPath, $requestName);
        //         $image->save();
        //     }
        // }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        $directory = Product::DIRECTORY_PATH . $product->id;
        $this->removeImageDirectory($directory);
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
