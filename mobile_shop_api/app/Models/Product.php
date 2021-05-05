<?php

namespace App\Models;

use App\Traits\ConditionQueryTrait;
use App\Traits\SlugByNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Tag(
 *      name="Product",
 * )
 * 
 * @OA\Schema(
 *      title="Product",
 *      @OA\Xml(name="Product"),
 *      @OA\Property(property="id", type="integer", format="int64", example="1"),
 *      @OA\Property(property="name", type="string", example="iPhone 12 Pro Max 64GB"),
 *      @OA\Property(property="category_id", type="integer", example="1", ref="#/components/schemas/Category"),
 *      @OA\Property(property="brand_id", type="integer", example="1", ref="#/components/schemas/Brand"),
 *      @OA\Property(property="price_core", type="number", multipleOf=1000, example=300000),
 *      @OA\Property(property="price", type="number", multipleOf=1000, example=250000),
 *      @OA\Property(property="sort_no", type="integer", example="1"),
 *      @OA\Property(property="home", type="integer", example="0", enum={0,1}, description="Show in homepage => 0: False, 1: True"),
 *      @OA\Property(property="new", type="integer", example="0", enum={0,1}, description="New product => 0: False, 1: True"),
 *      @OA\Property(property="introduction", type="string", example="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
 *      @OA\Property(property="additional_incentives", type="string", example="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
 *      @OA\Property(property="description", type="string", example="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
 *      @OA\Property(property="specification", type="string", example="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
 * )
 */
class Product extends Model
{
    use HasFactory, SlugByNameTrait, ConditionQueryTrait;

    /**
     * The directory path where the image is stored
     * 
     * @var array
     */
    const DIRECTORY_PATH = 'public/hinh-anh/san-pham/';

    /**
     * The columns that are used for sorting data
     * 
     * @var array
     */
    const SORT_COLUMN = ['id', 'name', 'price_core', 'price', 'sort_no', 'home', 'new', 'created_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'brand_id', 'price_core', 'price', 'sort_no', 'home', 'new', 'introduction', 'additional_incentives', 'description', 'specification'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * filter product with condition
     * 
     * @param array $condition
     * 
     * @return collection
     */
    public function getProductWithOrder($condition)
    {
        return $this->getCollectionDataWithOrder($condition, Product::SORT_COLUMN);
    }

    /**
     * Get the category that owns the product
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
