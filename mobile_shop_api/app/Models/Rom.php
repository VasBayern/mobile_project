<?php

namespace App\Models;

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
    use HasFactory;

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
}
