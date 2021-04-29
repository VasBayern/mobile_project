<?php

namespace App\Models;

use App\Traits\ConditionQueryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Tag(
 *      name="Rom",
 * )
 * @OA\Schema(
 *      title="Rom",
 *      @OA\Xml(name="Rom"),
 *      @OA\Property(property="id", type="integer", format="int64", example="1"),
 *      @OA\Property(property="name", type="integer", example="64 (64GB)"),
 * )
 */
class Rom extends Model
{
    use HasFactory, ConditionQueryTrait;

    /**
     * The columns that are used for sorting data
     * 
     * @var array
     */
    const SORT_COLUMN = ['id', 'name', 'created_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * filter rom with condition
     * @param array $condition
     * 
     * @return collection
     */
    public function getRomWithOrder($condition)
    {
        return $this->getCollectionDataWithOrder($condition, Rom::SORT_COLUMN);
    }
}
