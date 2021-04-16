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
class User extends Authenticatable implements MustVerifyEmail
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
     *      title="Phone",
     *      description="Number Phone",
     *      example="0123456789"
     * )
     *
     * @var string
     */
    private $phone;

    /**
     * @OA\Property(
     *      title="sex",
     *      description="Sex: 0:Male, 1: Female, 2: Orther",
     *      example="0"
     * )
     *
     * @var string
     */
    private $sex;

    /**
     * @OA\Property(
     *      title="Birthday",
     *      description="Birthday: dd/mm/yyyy",
     *      example="31/12/2021"
     * )
     *
     * @var string
     */
    private $birthday;

    /**
     * @OA\Property(
     *      title="Address",
     *      description="Address",
     *      example="Cau Giay, Ha Noi"
     * )
     *
     * @var string
     */
    private $address;


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
        'name', 'email', 'password', 'phone', 'address', 'sex', 'birthday', 'avatar'
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        
    ];

    /**
     * Set avatar base on name after register
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['avatar'] = vsprintf('https://www.gravatar.com/avatar/%s.jpg?s=200&d=%s', [
            md5(strtolower($this->attributes['email'])),
            $this->attributes['name'] ? urlencode("https://ui-avatars.com/api/" . $this->attributes['name'] . "") : 'mp',
        ]);
    }
}
