<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User_File;
use App\MyApplication\MyApp;
use App\MyApplication\Services\FileRuleValidation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * @property FileRuleValidation rules
 */
class BookingFileController extends Controller
{
    public function __construct()
    {  $this->middleware(["logger"]);
        $this->middleware(["auth:sanctum"]);
        $this->rules = new FileRuleValidation();
    }

    public function CheckIn(Request $request)
    {
//        $request->validate([
//            "ids" => [ "array"],
//            "ids.*" => ["numeric", Rule::exists("files", "id")],
            $request->validate([
                "ids" => ["required", "array"],
                "ids.*" => ["numeric", Rule::exists("files", "id")],
                "id_file" => ["numeric"],

            ]);


        $user = auth()->user();
        if($request->has("ids"))
        $files = File::query()->whereIn("id",$request->ids)->get();
else
    $files = File::query()->where("id",$request->id_file)->get();

        DB::beginTransaction();
        foreach ($files as $file){
            if (!auth()->user()->can("check_in_file", $file)) {
                DB::rollBack();
                return MyApp::Json()->errorHandle("file", "you can't Check-in file [ $file->name ] becouse you do not have the authority .");
            }
            if ($file->userBookings()->exists())
            {
                DB::rollBack();
                return MyApp::Json()->errorHandle("file","you can't Check-in file [ $file->name ] becouse it was already booked .");
            }
            $file->update([
                "status" => "used"
            ]);
            $user->filesBookings()->attach($file->id);
        }

        DB::commit();
        return MyApp::Json()->dataHandle("Successfully Check-in files .","message");
    }

    public function CheckOut(Request $request)
    {
        $request->validate($this->rules->onlyKey(["id_file"],true));
        $file = User_File::query()->where("id_user",\auth()->id())
            ->where("id_file",$request->id_file)
            ->whereNull("deleted_at")->first();
        $f=File::query()
            ->where("id",$request->id_file)
            ->where("status","=","used")->first();
        if (!is_null($file)){
            $file->update([
                "deleted_at" => Carbon::now()
            ]);
            $f->update([
                "status" => "free"
            ]);
            return MyApp::Json()->dataHandle("Successfully Check-out file .","message");
        }
        return MyApp::Json()->errorHandle("file","you can't Check-out file becouse you do not Check-in .");
    }


}
