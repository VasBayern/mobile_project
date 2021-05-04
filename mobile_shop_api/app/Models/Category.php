<?php

namespace App\Models;

use App\Traits\ConditionQueryTrait;
use App\Traits\SlugByNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Tag(
 *      name="Category",
 * )
 * @OA\Schema(
 *      title="Category",
 *      @OA\Xml(name="Category"),
 *      @OA\Property(property="id", type="integer", format="int64", example="1"),
 *      @OA\Property(property="name", type="string", example="Điện thoại"),
 *      @OA\Property(property="sort_no", type="integer", example="1"),
 *      @OA\Property(property="home", type="integer", example="0", enum={0,1}, description="Show in homepage => 0: False, 1: True",),
 *      @OA\Property(property="image", type="string", example="dien-thoai.png"),
 * )
 */
class Category extends Model
{
    use HasFactory, SlugByNameTrait, ConditionQueryTrait;

    /**
     * The directory path where the image is stored
     * 
     * @var array
     */
    const DIRECTORY_PATH = 'public/hinh-anh/danh-muc/';

    /**
     * The columns that are used for sorting data
     * 
     * @var array
     */
    const SORT_COLUMN = ['id', 'name', 'sort_no', 'home', 'image', 'created_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'image', 'sort_no', 'home'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function getCategoryWithOrder($condition)
    {
        return $this->getCollectionDataWithOrder($condition, Category::SORT_COLUMN);
    }
}
