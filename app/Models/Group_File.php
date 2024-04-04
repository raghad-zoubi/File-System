<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group_File extends Model
{
    use HasFactory;
    protected $table = "group_files";
    protected $fillable = ['id_group','id_file'];

    public function groupFiles(){
        return $this->belongsToMany(User::class,'group_files',
            "id_file",
            "id_group",
            "id");

    }
    public function fileGroups(){
        return $this->belongsToMany(File::class,
            'group_files',
            "id_file",
            "id");

    }

}
