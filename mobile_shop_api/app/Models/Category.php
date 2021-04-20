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

    const DIRECTORY_PATH = 'public/hinh-anh/danh-muc/';

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
        $fromDate = DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d 00:00:00');
        $toDate = DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d 23:59:59');

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
     * @param  int  $paginate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfPaginate($query, $paginate)
    {
        $perPage = 100;

        switch ($paginate) {
            case 0:
                $perPage = 10;
                break;
            case 1:
                $perPage = 25;
                break;
            case 2:
                $perPage = 50;
                break;
            case 3:
                $perPage = 100;
                break;
            default:
                $perPage = 100;
                break;
        }

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
    public function scopeOfOrderBy($query, $column, $order)
    {
        $sortOrder = ($order == 1) ? 'DESC' : 'ASC';
        $sortColumn = '';

        switch ($column) {
            case 0:
                $sortColumn = 'id';
                break;
            case 1:
                $sortColumn = 'name';
                break;
            case 2:
                $sortColumn = 'sort_no';
                break;
            case 3:
                $sortColumn = 'home';
                break;
            case 4:
                $sortColumn = 'image';
                break;
            case 5:
                $sortColumn = 'created_at';
                break;
            default:
                throw new Exception("Không tìm thấy trường này");
                break;
        }

        return $query->orderBy($sortColumn, $sortOrder);
    }

    /**
     * Function get category with order condition
     * 
     * @param  array $page
     * @return array 
     */
    public static function getCategoryWithOrder($page)
    {
        $search = isset($page['search']) ? $page['search'] : '';
        $startDate = isset($page['start_date']) ? $page['start_date'] : '01/01/2000';
        $endDate = isset($page['end_date']) ? $page['end_date'] : now()->format('d/m/Y');

        return Category::ofSearch($search)
            ->ofDate($startDate, $endDate)
            ->ofOrderBy($page['sort'], $page['order'])
            ->ofPaginate($page['per_page']);
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
        if (Storage::exists($directory)) {
            Storage::deleteDirectory($directory);
        }
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
