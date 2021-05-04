<?php

namespace App\Models;

use App\Traits\ConditionQueryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Tag(
 *      name="Color",
 * )
 * @OA\Schema(
 *      title="Color",
 *      @OA\Xml(name="Color"),
 *      @OA\Property(property="id", type="integer", format="int64", example="1"),
 *      @OA\Property(property="name", type="string", example="Đỏ"),
 *      @OA\Property(property="code", type="string", example="#ff0000"),
 * )
 */
class Color extends Model
{
    use HasFactory, ConditionQueryTrait;

    /**
     * The columns that are used for sorting data
     * 
     * @var array
     */
    const SORT_COLUMN = ['id', 'name', 'code', 'created_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'colors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code'];

    /**
     * filter color with condition
     * @param array $condition
     * 
     * @return collection
     */
    public function getColorWithOrder($condition)
    {
        return $this->getCollectionDataWithOrder($condition, Color::SORT_COLUMN);
    }
}
