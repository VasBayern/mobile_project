<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *      title="Email",
     *      description="Email",
     *      example="admin@example.com"
     * )
     *
     * @var string
     */
    private $email1; //$email fail send email

    /**
     * @OA\Property(
     *      title="Name",
     *      description="Username",
     *      example="Nguyen Van A"
     * )
     *
     * @var string
     */
    private $name;



    /**
     * @OA\Property(
     *      title="Password",
     *      description="Password",
     *      example="yourpassword"
     * )
     *
     * @var string
     */
    private $password;

    /**
     * @OA\Property(
     *      title="Avatar",
     *      description="Avatar Image",
     *      example="./avatar.ipg"
     * )
     *
     * @var string
     */
    private $avatar;

    /**
     * @OA\Property(
     *      title="Role",
     *      description="Role account: default 0 - guest",
     *      format="int64",
     *      example="0"
     * )
     *
     * @var integer
     */
    private $role;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
