<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * 
 * @OA\Tag(
 *      name="Authentication",
 *      description="Sanctum ToKen Authen",
 * )
 * @OA\Schema(
 *     title="User",
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
     *     format="int64"
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *      format="email"
     * )
     *
     * @var string
     */
    private $email1; //$email fail send email

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *      format="password"
     * )
     *
     * @var string
     */
    private $password;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $avatar;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $phone;

    /**
     * @OA\Property(
     *      format="int32",
     *      title="sex",
     *      description="Sex: 0:Male, 1: Female, 2: Orther",
     *      enum={0, 1, 2}
     * )
     *
     * @var string
     */
    private $sex;

    /**
     * @OA\Property(
     *      format="date-time",
     * )
     *
     * @var string
     */
    private $birthday;

    /**
     * @OA\Property()
     *
     * @var string
     */
    private $address;


    /**
     * @OA\Property(
     *      format="int32",
     *      description="Role account: default 0 - guest",
     *      enum={0, 1, 2}
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
