<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function show_files_in_group(User $user,Group $group)
    {
        return $this->is_owner_group($user,$group) || $this->user_in_group($user,$group) || $group->isPublic();
    }

    public function is_owner_group(User $user,Group $group)
    {
        return ($user->id == $group->id_user) || ($user->role=='admin');
    }

    public function user_in_group(User $user,Group $group):bool{
        return ($group->users()->where("id_user",$user->id)->exists());
    }

    public function add_file_to_group(User $user,Group $group)
    {
        return /*$this->is_owner_group($user,$group) || */
            $this->user_in_group($user,$group) || $group->isPublic();
    }

    public function add_users(User $user,Group $group)
    {
        return ($user->id === $group->id_user) || ($user->role=='admin') || $group->isPublic();
    }   public function display(User $user,Group $group)
    {
        return ($user->id === $group->id_user) || ($user->role=='admin') ;
    }
    public function delete_users(User $user,Group $group)
    {
        return ($user->id === $group->id_user) || ($user->role=='admin');
    }

    public function delete_group(User $user,Group $group)
    {
        return $this->is_owner_group($user,$group);
    }
}
//
//id_user,id_group
//31,21
//32,22
//33,23
//34,24
//35,25
//36,26
//37,27
//38,28
//39,29
//40,30
//21,31
//22,32
//23,33
//24,34
//25,35
//26,36
//27,37
//28,38
//29,39
//30,40
