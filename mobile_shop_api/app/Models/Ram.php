<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Tag(
 *      name="Ram",
 * )
 * @OA\Schema(
 *      title="Ram",
 *      @OA\Xml(name="Ram"),
 *      @OA\Property(property="id", type="integer", format="int64", example="1"),
 *      @OA\Property(property="name", type="integer", example="4 (4GB)"),
 * )
 */
class Ram extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rams';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
