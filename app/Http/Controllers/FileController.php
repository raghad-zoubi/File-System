<?php

namespace App\Http\Controllers;

use App\Aspects\Transactional;
use App\Models\File;
use App\Models\User_File;
use App\MyApplication\MyApp;
use App\MyApplication\Services\FileRuleValidation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @property FileRuleValidation rules
 */
class FileController extends Controller
{

//        public function upload(Request $request)
//    {
//        if ($request->hasFile('file')) {
//            $file = $request->file('file');
//            $fileName = time() . '.' . $file->getClientOriginalExtension();
//            $existingFile = File::query()->where('name', $fileName)->where('id_user',auth()->id())->get();
////            dd($existingFile);
//            if ($existingFile) {
//                return 'File already exists in database';
//            } else {
//            $file->move('uploads', $fileName);
//            $fileModel = new File;
//            $fileModel->name = $fileName;
//            $fileModel->path = '/uploads/' . $fileName;
//            $fileModel->status = 'free';
//            $fileModel->id_user =auth()->id();
//
//                $fileModel->save();
//                return 'File uploaded and added to database successfully';
//            }
//        } else {
//            return 'No file selected';
//        }
//        return response()->json($message);
//    }

///////////////////beeeeb
    public function __construct()
    {   $this->middleware(["logger"]);
        $this->middleware(["auth:sanctum"]);
        $this->middleware(["multi.auth:admin"])->only("All");
       $this->middleware(["max.files"])->only("CreateFile");
        $this->rules = new FileRuleValidation();

    }



    public function DownloadFile(Request $request){

        $request->validate($this->rules->onlyKey(["id_file"],true));
        $file = File::query()->where("id",$request->id_file)->first();
       /// $this->authorize("read_file",$file);

        if (auth()->user()->can("read_file", $file)) {
            // return MyApp::uploadFile()->DownloadFile($file->path);
            $file_contents = file_get_contents(storage_path($file->path));
            return MyApp::Json()->dataHandle($file_contents, "file");
        }
        else
            return MyApp::Json()->dataHandle("can't access", "error");

    }


    public function ReportFile(Request $request)
    {
        $request->validate($this->rules->onlyKey(["id_file"],true));
        $file = File::with(["user"])
            ->where("files.id",$request->id_file)
            ->first();
        if (auth()->user()->can("is_owner_file", $file)){
       // $this->authorize("",$file);
        $report = User_File::query()
            ->select([
                "user_files.id_user as id_user_booking",
                "users.name as name_user_booking",
                "user_files.created_at as booking_date",
                "user_files.deleted_at as unbooking_date"
            ])
            ->where("id_file",$file->id)
            ->join("users","users.id","=","id_user")
            ->orderBy("booking_date","desc")
            ->get();
        $file->report = $report;
        return MyApp::Json()->dataHandle($file,"file");}

    else
return MyApp::Json()->dataHandle("error report file.","unauthorized");

    }

    public function All()
    {
        return MyApp::Json()->dataHandle(File::with(["user","userBookings"])->get(),"files");
    }

    public function ShowMyFiles()
    {
        $files = File::with("userBookings")->where("id_user",auth()->id())->get();
        return MyApp::Json()->dataHandle($files,"files");
    }

    public function CreateFile(Request $request)
    {
             if ($request->id_group==null)
              {$request->id_group='1';}

        $request->validate($this->rules->onlyKey(["id_group","name","file"],true));

        $file = $request->file("file");

        if ($file->isValid()){
            try {
                DB::beginTransaction();
                $path = MyApp::uploadFile()->upload($file);

                $fileAdded = File::create([
                    "id_user" => auth()->id(),
                    "name" => strtolower($request->name),
                    "path" => $path,
                ]);
                $fileAdded->groups()->syncWithoutDetaching($request->id_group);
                DB::commit();
                return MyApp::Json()->dataHandle($fileAdded,"file");
            }catch (\Exception $e){
                MyApp::uploadFile()->rollBackUpload();
                DB::rollBack();

                throw new \Exception($e->getMessage());
            }
        }else{
            return MyApp::Json()->errorHandle("file",$file->getError());
        }
    }

    public function UpdateFile(Request $request)
    {
        $request->validate($this->rules->onlyKey(["file","id_file"],true));
        $file = File::with("user")->where("id",$request->id_file)->first();
        $oldPath = $file->path;
        $newFile = $request->file("file");

         //   $this->authorize("update_file", $file);
 if (auth()->user()->can("update_file", $file)){
        if ($newFile->isValid()){
            try {
                DB::beginTransaction();
                $newPath = MyApp::uploadFile()->upload($newFile);
                $file->update([
                    "path" => $newPath,
                ]);
                MyApp::uploadFile()->deleteFile($oldPath);
                DB::commit();
                return MyApp::Json()->dataHandle("Successfully updated file.","message");
            }catch (\Exception $e){
                MyApp::uploadFile()->rollBackUpload();
                DB::rollBack();
                throw new \Exception($e->getMessage(),$e->getCode());
            }
        }
        return MyApp::Json()->errorHandle("file",$newFile->getError());}
 else
     return MyApp::Json()->dataHandle("error updated file.","UnAuth");

    }

    public function DeleteFile(Request $request)
    {
        $request->validate($this->rules->onlyKey(["id_file"], true));
        $file = File::where("id", $request->id_file)->first();
     //   $this->authorize("delete_file", $file);
        if (auth()->user()->can("delete_file", $file) ){

        if ($file->CheckisBooking()) {
            return MyApp::Json()->errorHandle("file", "the File current is booking .");
        }
        DB::beginTransaction();
        $temp_path = $file->path;
        $file->delete();
        if (MyApp::uploadFile()->deleteFile($temp_path)) {
            DB::commit();
            return MyApp::Json()->dataHandle("Successfully deleted file .", "message");
        }

    }
        else{
            DB::rollBack();
            return MyApp::Json()->errorHandle("massage","UnAuth .") ;

        }
        DB::rollBack();
        return MyApp::Json()->errorHandle("file","the File current is not deleted .");
    }
}

//('12', 'boo12', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '12', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('12', 'boo12', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '12', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//
//INSERT INTO `files` (`id`, `name`, `path`, `status`, `id_user`, `created_at`, `updated_at`) VALUES ('12', 'boo12', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '12', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('21', 'boo21', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '21', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('22', 'boo22', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '22', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('23', 'boo23', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '23', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('24', 'boo24', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '24', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('25', 'boo25', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '25', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('26', 'boo26', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '26', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('27', 'boo27', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '27', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('28', 'boo28', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '28', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('29', 'boo29', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '29', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//('30', 'boo30', 'app/public/Uploads/files/1704806232raghad3.txt', 'free', '30', '2024-01-10 07:41:51', '2024-01-10 07:41:51'),
//
