<?php

namespace App\Models;

use App\Trait\setSlugTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    use HasFactory, setSlugTrait;

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
}
