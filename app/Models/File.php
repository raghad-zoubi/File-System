<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, $id_file)
 * @method static find($idFile)
 */
class File extends Model
{
  use HasFactory;
    protected $table = "files";
    protected $fillable = ['id_user','name','path','status'];
    protected $hidden = ['pivot','id_user'];

    public function user()
    {
        return $this->belongsTo(User::class,"id_user","id")->select(["users.id","users.name"])->withDefault();
    }

    public function userBookings(){
        return $this->belongsToMany(User::class,"user_files",
            "id_file",
            "id_user",
            "id",
            "id"
        )->withTimestamps()->select(["users.id","users.name"])->whereNull("user_files.deleted_at");
    }

    public function groups(){
        return $this->belongsToMany(Group::class,"group_files",
            "id_file",
            "id_group",
            "id",
            "id"
        )->withTimestamps();
    }


    public function CheckisBooking()
    {
        return $this->userBookings()->exists();
    }


}

//    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
//
//    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
//        return $this->belongsTo(Group::class);
//    }

