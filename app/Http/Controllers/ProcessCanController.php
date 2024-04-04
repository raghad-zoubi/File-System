<?php

namespace App\Http\Controllers;

use App\Aspects\Logger;
use App\Models\File;
use App\Models\Group;
use App\Models\Group_File;
use App\Models\Group_User;
use App\Models\User;
use App\Models\User_File;
use App\MyApplication\MyApp;
use App\MyApplication\Services\FileRuleValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Logger]

class ProcessCanController extends Controller
{
    public function __construct()
    { $this->middleware(["logger"]);
        $this->middleware(["auth:sanctum"]);
    }


    public function showgroup()
    {

        $file = Group_User::query()->with('userGroups')
            ->where("id_user", \auth()->id())->get();
        return response()->json($file);


    }

    public function showfile($id_group)
    {

        if(Group_User::query()->where('id_group', $id_group)
            ->where("id_user", auth()->id())->exists()) {
                $infoGroup = Group_File::query()
                    ->with('fileGroups')->where('id_group', $id_group)
                    ->get();
            }
            else $infoGroup="you cant access to this group";
        return response()->json(['message' => $infoGroup]);


    }


}
