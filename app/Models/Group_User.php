<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group_User extends Model
{
    use HasFactory;

    protected $table = "group_users";
    protected $fillable = ['id_user', 'id_group'];

    public function groupUsers(){
        return $this->belongsToMany(User::class,
            'group_users',

            "id_user",
            "id");

    }

    public function userGroups(){
        return $this->belongsToMany(Group::class,
            'group_users',
            "id_group",
            "id");

    }


    public function users(){
        return $this->belongsToMany(User::class);
    }public function groups(){
        return $this->belongsToMany(Group::class);
    }

}
