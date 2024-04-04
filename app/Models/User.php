<?php

namespace App\Models;

use App\MyApplication\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


//{
//    use HasApiTokens, HasFactory, Notifiable;
//
//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array<int, string>
//     */
//    protected $fillable = [
//        'name',
//        'email',
//        'password',
//    ];
//
//    /**
//     * The attributes that should be hidden for serialization.
//     *
//     * @var array<int, string>
//     */
//    protected $hidden = [
//        'password',
//        'remember_token',
//    ];
//
//    /**
//     * The attributes that should be cast.
//     *
//     * @var array<string, string>
//     */
//    protected $casts = [
//        'email_verified_at' => 'datetime',
//    ];
//
//    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
//    {
//        return $this->hasMany(File::class);
//    }
//}

/**
 * @method static create(array $array)
 * @method static where(string $string, $email)
 */
class User extends Authenticatable

{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id',
    'name',
    'email',
    'password','role'
];

    protected $hidden = [
  //  'password','pivot',
    'remember_token','email_verified_at'
];

    public function Myfiles(){
    return $this->hasMany(File::class,"id_user","id");
}

    public function Mygroups(){
    return $this->hasMany(Group::class,"id_user","id");
}

    public function filesBookings(){
    return $this->belongsToMany(File::class,"user_files",
        "id_user",
        "id_file",
        "id",
        "id"
    )->withTimestamps();
}

    public function userGroups(){
    return $this->belongsToMany(Group::class,"group_users",
        "id_user",
        "id_group",
        "id",
        "id"
    )->withTimestamps();
}

    public function isAdmin()
{
    return $this->role === 'admin';
    }

    public function getWithNewToken(){
    $user = $this;
    $token = $user->createToken($user->name,["*"])->plainTextToken;
    $user->token = $token;
    return $this;
}


}
