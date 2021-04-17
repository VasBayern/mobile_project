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
 *     title="Category",
 *     @OA\Xml(
 *         name="Category"
 *     )
 * )
 */
class Category extends Model
{
    use HasFactory, setSlugTrait;

    /**
     * @OA\Property(
     *     format="int64",
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $slug;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $image;

    /**
     * @OA\Property(
     *      format="int32",
     * )
     *
     * @var integer
     */
    private $sort_no;

    /**
     * @OA\Property(
     *      format="int32",
     *      description="Show in homepage => 0: False, 1: True",
     *      enum={0,1}
     * )
     *
     * @var integer
     */
    private $home;

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
