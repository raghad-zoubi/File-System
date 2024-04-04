<?php

namespace App\Http\Controllers;

use App\Aspects\Logger;
use App\Http\Requests\GroupFileRequest;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\GroupUserRequest;
use App\Models\File;
use App\Models\Group;
use App\Models\Group_File;
use App\Models\Group_User;
use App\Models\User;
use App\MyApplication\MyApp;
use App\MyApplication\Services\GroupRuleValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Logger]

/**
 * @property  rules
 * @property GroupRuleValidation rules
 */
class GroupController extends Controller
{


    public function __construct()
    {
        $this->middleware(["auth:sanctum"]);
        $this->middleware(["multi.auth:admin"])->only(["All_Group", "All_User", "All_File"]);
        $this->rules = new GroupRuleValidation();
        $this->middleware(["logger"]);
    }


    public function All_Group()
    {
        $groups = Group::query()->select('id', 'name', 'type')->get();
        return response()->json($groups);
    }

    public function All_User()
    {
        $groups = User::query()->select('id', 'name', 'role')->get();
        return response()->json($groups);
    }

    public function All_File()
    {
        $groups = File::query()->select('id', 'name', 'path')->get();
        return response()->json($groups);
    }

    public function myGroup()
    {
        $myGroups = Group::query()->where('id_user', auth()->id())->select('id', 'name', 'type')->get();
        return response()->json($myGroups);
    }

    public function myFile($id_group)
    {//display
        $group = Group::find($id_group);

        if (auth()->user()->can("display", $group)) {

            $infoGroup = Group_File::query()
                ->where('id_group', $id_group)
                ->with('groupFiles')->get();
        } else
            $infoGroup = 'you are not owner of the group';

        return response()->json(['message' => $infoGroup]);

    }

    public function myUser($id_group)
    {
        $group = Group::find($id_group);

        if (auth()->user()->can("display", $group)) {

            $infoGroup = Group_User::query()
                ->where('id_group', $id_group)
                ->with('groupUsers')->
                first();
        } else
            $infoGroup = 'you are not owner of the group';

        return response()->json(['message' => $infoGroup]);


    }

    public function create(GroupRequest $request)
    {
        $validation = $request->validated();
        $group = Group::query()->create([

            'name' => $request->name,
            'id_user' => auth()->id(),
            'type' => $request->type,
        ]);

        return response()->json($group);
    }


    public function delete($id_group)
    {
        $group = Group::find($id_group);
        if (auth()->user()->can("delete_group", $group)) {
            $group->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(['error'], 200);
        }
    }


    public function AddFiletoGroup(\Illuminate\Http\Request $request)
    {
        $request->validate($this->rules->onlyKey(["id_group", "id_file"], true));
        $file = File::where("id", $request->id_file)->first();

        $ex = Group_File::query()->where("id_group", $request->id_group)
            ->where("id_file", $request->id_file)->exists();

        $group = Group::query()->where("id", $request->id_group)->first();
        if (auth()->user()->can("is_owner_file", $file) || auth()->user()->can("add_file_to_group", $group)) {

            if (!$ex) {
                DB::transaction(function () use ($file, $group) {
                    $file->groups()->syncWithoutDetaching($group->id);
                });
                return MyApp::Json()->dataHandle("Successfully add file to group", "message");
            } else
                return MyApp::Json()->dataHandle("nooooooooooooooooo add file to group", "message");

        }

        return MyApp::Json()->dataHandle("error add file to group", "error");


    }


    public function RemoveFileFromGroup(\Illuminate\Http\Request $request)
    {
        $request->validate($this->rules->onlyKey(["id_group", "id_file"], true));
        $file = File::where("id", $request->id_file)->first();
        $group = Group::query()->where("id", $request->id_group)->first();

        $ex = Group_File::query()->where("id_group", $request->id_group)
            ->where("id_file", $request->id_file)->exists();
        if (auth()->user()->can("is_owner_file", $file) && auth()->user()->can("is_owner_group", $group)) {
            if ($ex) {

                DB::transaction(function () use ($file, $request) {
                    $file->groups()->detach($request->id_group);
                });
                return MyApp::Json()->dataHandle("Successfully delete file From group", "message");
            }
            return MyApp::Json()->dataHandle("nooooooooooooooooo delete file From group", "message");
        }

        return MyApp::Json()->dataHandle("error delete file From group", "error");

        // throw new AccessDeniedHttpException();
    }


    public function AddUsertoGroup(\Illuminate\Http\Request $request)
        //id_user, $id_group)
    {
    $request->validate($this->rules->onlyKey(["id_group","id_user"],true));

        $user = auth()->user();

        $group = Group::find($request->id_group);
        $userGroup = Group_User::query()->where('id_user', $request->id_user)->exists();
        if (auth()->user()->can("add_users", $group)) {

            if (!$userGroup) {
                $user_group = Group_User::query()->create([
                    'id_group' => $request->id_group,
                    'id_user' => $request->id_user,
                ]);
            } else {
                $user_group = 'the user is already exists';
            }
        } else {
            $user_group = 'you are not owner of the group';
        }
        return response()->json(['message' => $user_group]);
    }

    public function RemoveUserFromGroup(\Illuminate\Http\Request $request)
        //id_user, $id_group)
    {
        $request->validate($this->rules->onlyKey(["id_group","id_user"],true));

        $user_group = null;
        $group = Group::find($request->id_group);
        $userGroup = Group_User::query()->where('id_user', $request->id_user)->exists();
        if (auth()->user()->can("delete_users", $group)) {


            if ($userGroup) {
                $userGroup = Group_User::query()->where('id_user', $request->id_user)->delete();
                $user_group = "susess";
            } else {
                $user_group = 'the user is not exists';
            }
        } else {
            $user_group = 'you are not owner of the group';
        }
        return response()->json(['message' => $user_group]);


    }


}

