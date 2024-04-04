<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\MyApplication\MyApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthenticationController extends Controller
{

    public function __construct()
    {
        $this->middleware(["auth:sanctum"])->only(["Logout","MyData","hh"]);
        $this->middleware(["auth:sanctum","multi.auth:admin",])->only(["Users"]);
        $this->middleware(["logger"]);

    }

    public function Users()
    {
        return MyApp::Json()->dataHandle(User::query()
            ->whereNot("role",'admin')
            ->orderBy("id","desc")->get(),"users");
    }
    public function hh()
    { $f= auth()->id();

        return MyApp::Json()->dataHandle("Successfully logged out+ $f",auth()->id());
    }

    public function MyData()
    {
        return MyApp::Json()->dataHandle(auth()->user(),"user");
    }

    public function Register(Request $request)
    { //dd('po');
        $request->validate([
            "name" => ["required","string"],
            "email" => ["required",Rule::unique("users","email"),"email"],
            "password" => ["required","min:8"],
            "role" => ["string",
                Rule::in(['user','admin']
                )],
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,//password_hash($request->password,PASSWORD_DEFAULT),
            "role" => $request->role
        ]);
        return MyApp::Json()->dataHandle($user->getWithNewToken(),"user");
    }

//    public function Login(Request $request)
//    {  //dd('user->getWithNewToken()');
//
//        $request->validate([
//            "Username" => ["required",Rule::exists("users","email")],
//            "password" => ["required","min:8"],
//        ]);
//        $user = User::where("email",$request->Username)->first();
//        if (password_verify($request->password,$user->password)){
//             return MyApp::Json()->dataHandle($user->getWithNewToken());
//            //       return response()->json($user->getWithNewToken());
//        }
//
//        $password ='d';
//
//        $password->password = ["the password is not valid"];
//
//        return MyApp::Json()->errorHandle("Validation",$password);
//    }

    public function Login(Request $request)
    {  //dd('user->getWithNewToken()');

        $request->validate([
            "email" => ["required",Rule::exists("users","email")],
            "password" => ["required","min:8"],
        ]);
        $user = User::where("email",$request->email)->first();
        if ($request->password==$user->password){
            // return MyApp::Json()->dataHandle($user->getWithNewToken());
             return response()->json($user->getWithNewToken());
        }

        $password = new class{};// =null;

        $password->password = ["the password is not valid"];

        return MyApp::Json()->errorHandle("Validation",$password);
    }
    public function Logout()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        return MyApp::Json()->dataHandle("Successfully logged out","message");
    }
}


//
//email,password
//raghad1@user,12345678
//raghad2@user,12345678
//raghad3@user,12345678
//raghad4@user,12345678
//raghad5@user,12345678
//raghad6@user,12345678
//raghad7@user,12345678
//raghad8@user,12345678
//raghad9@user,12345678
//aghad10@user,12345678
//maria1@user,12345678
//maria2@user,12345678
//maria3@user,12345678
//maria4@user,12345678
//maria5@user,12345678
//maria6@user,12345678
//maria7@user,12345678
//maria8@user,12345678
//maria9@user,12345678
//maria10@user,12345678
//bayans1@user,12345678
//bayans2@user,12345678
//bayans3@user,12345678
//bayans4@user,12345678
//bayans5@user,12345678
//bayans6@user,12345678
//bayans7@user,12345678
//bayans8@user,12345678
//bayans9@user,12345678
//ayans10@user,12345678
//samar1@user,12345678
//samar2@user,12345678
//samar3@user,12345678
//samar4@user,12345678
//samar5@user,12345678
//samar6@user,12345678
//samar7@user,12345678
//samar8@user,12345678
//samar9@user,12345678
//samar10@user,12345678
//tsnem1@user,12345678
//tsnem2@user,12345678
//tsnem3@user,12345678
//tsnem4@user,12345678
//tsnem5@user,12345678
//tsnem6@user,12345678
//tsnem7@user,12345678
//tsnem8@user,12345678
//tsnem9@user,12345678
//tsnem10@user,12345678
//Bayan1@user,12345678
//Bayan2@user,12345678
//Bayan3@user,12345678
//Bayan4@user,12345678
//Bayan5@user,12345678
//Bayan6@user,12345678
//Bayan7@user,12345678
//Bayan8@user,12345678
//Bayan9@user,12345678
//Bayan10@user,12345678
//admin@admin,12345678
