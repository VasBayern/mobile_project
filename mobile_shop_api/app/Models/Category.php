<?php

namespace App\Models;

use App\Traits\SlugByNameTrait;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *      name="Category",
 *      description="Category Information Of Project",
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
    use HasFactory, SlugByNameTrait;

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

    /**
     * Scope a query to only include data between 2 given date
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  date  $startDate
     * @param  date  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDate($query, $startDate, $endDate)
    {
        $formatDate = config('global.datetime.format');

        $fromDate = DateTime::createFromFormat($formatDate['input_date'], $startDate)->format($formatDate['start_date']);
        $toDate = DateTime::createFromFormat($formatDate['input_date'], $endDate)->format($formatDate['end_date']);

        return $query->whereBetween('created_at', [$fromDate, $toDate]);
    }

    /**
     * Scope a query to only include data of given search
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSearch($query, $search)
    {
        return $query->where('name', 'LIKE', '%' . $search . '%');
    }

    /**
     * Scope a query to only include data of given paginate
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $paginationKey
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfPaginate($query, $paginationKey)
    {
        $paginatationPage = config('global.pagination.per_page');
        $maxRecord = config('global.pagination.max_record');
        $perPage = array_key_exists($paginationKey, $paginatationPage) == true ? $paginatationPage[$paginationKey] : $maxRecord;

        return $query->paginate($perPage);
    }

    /**
     * Scope a query to only include data of given column order
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $column
     * @param  int  $order
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOrderBy($query, $columnKey, $order)
    {
        $sortOrder = ($order == 1) ? 'DESC' : 'ASC';

        if (array_key_exists($columnKey, Category::SORT_COLUMN)) {
            $sortColumn = Category::SORT_COLUMN[$columnKey];
        } else {
            throw new Exception("Không tìm thấy trường này");
        }

        return $query->orderBy($sortColumn, $sortOrder);
    }

    /**
     * Function get category with order condition
     * 
     * @param  array $condition
     * @return array 
     */
    public static function getCategoryWithOrder($condition)
    {
        $search = isset($condition['search']) ? $condition['search'] : '';
        $startDate = isset($condition['start_date']) ? $condition['start_date'] : config('global.datetime.default_date');
        $endDate = isset($condition['end_date']) ? $condition['end_date'] : now()->format(config('global.datetime.format.input_date'));

        return Category::ofSearch($search)
            ->ofDate($startDate, $endDate)
            ->ofOrderBy($condition['sort'], $condition['order'])
            ->ofPaginate($condition['per_page']);
    }

    /**
     * Upload image when request has image file
     * 
     * @param  integer $id
     * @param  string $requestName 
     * @param  file $requestImage
     * 
     * @return string 
     */
    public static function handleUploadImage($id, $requestName, $requestImage)
    {
        $directory = Category::DIRECTORY_PATH . $id;
        Category::removeImageDirectory($id);

        $nameImage = Str::slug($requestName) . '.' . $requestImage->extension();
        $pathImage = Storage::putFileAs($directory, $requestImage, $nameImage);

        return Storage::url($pathImage);
    }

    /**
     * Rename image when update name
     * 
     * @param  integer $id
     * @param  string $name Current name
     * @param  string $path Current path image
     * @param  string $requestName Request name to update  
     * 
     * @return string 
     */
    public static function renameStorageImage($id, $name,  $path, $requestName)
    {
        $directory = Category::DIRECTORY_PATH . $id;

        $arrayPathImage = explode('/', $path);
        $oldNameImage = end($arrayPathImage);
        $oldPathImage = $directory . '/' . $oldNameImage;
        $newPathImage = Str::replaceLast($name, Str::slug($requestName), $oldPathImage);

        if ($newPathImage != $oldPathImage) {
            Storage::move($oldPathImage, $newPathImage);
        }

        return Storage::url($newPathImage);
    }

    /**
     * Remove image folder when delete item
     * 
     * @param  integer $id
     */
    public static function removeImageDirectory($id)
    {
        $directory = Category::DIRECTORY_PATH . $id;

        if (Storage::exists($directory)) {
            Storage::deleteDirectory($directory);
        }
    }
}
