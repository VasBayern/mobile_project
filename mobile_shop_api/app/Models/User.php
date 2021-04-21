<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * 
 * @OA\Tag(
 *      name="Authentication",
 *      description="Sanctum ToKen Authen",
 * )
 * @OA\Schema(
 *      required={"password"},
 *      @OA\Xml(name="User"),
 *      @OA\Property(property="id", type="integer", format="int64"),
 *      @OA\Property(property="email", type="string", format="email"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="password", type="string", format="password"),
 *      @OA\Property(property="avatar", type="string"),
 *      @OA\Property(property="phone", type="string", minLength=10, maxLength=10),
 *      @OA\Property(property="sex", type="integer", enum={0,1,2}, description="Sex: 0: Male, 1: Female, 2: Orther"),
 *      @OA\Property(property="birthday", type="string", format="date-time", description="Birthday: dd/mm/yyyy"),
 *      @OA\Property(property="address", type="string"),
 *      @OA\Property(property="role", type="integer", enum={0,1,2}, description="Role: 0: User, 1: Admin, 2: Staff"),
 * )
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

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
     * The directory path where the image is stored
     * 
     * @var array
     */
    const DIRECTORY_PATH = 'public/hinh-anh/tai-khoan/';

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
