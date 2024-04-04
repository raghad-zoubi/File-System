<?php

use App\Http\Controllers\ProcessCanController;
use App\Http\Controllers\test;
use App\Http\Middleware\LogRequest;
use App\Http\Middleware\LogRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BookingFileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProcessGroupController;
use App\Http\Controllers\SearchController;

//use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("ss", function () {

});





Route::get('test', [test::class, 'listFile']);


Route::prefix("filemanagement")->group(function () {
    Route::prefix("auth")->controller(AuthenticationController::class)->group(function () {
        Route::get("user", "MyData");
        Route::get("users", "Users");
        Route::post("register", "Register");
        Route::post("login", "Login");
        Route::get("h", "hh");
        Route::delete("logout", "Logout");
    });
    Route::prefix("file")->group(function () {
        Route::controller(FileController::class)->group(function () {
            Route::post("create", "CreateFile");
            Route::post("update", "UpdateFile");
            Route::post("delete", "DeleteFile");
            Route::get("show", "ShowMyFiles");
            Route::get("all", "All");
            Route::post("report", "ReportFile");
            Route::post("read", "DownloadFile");

        }
        );

        Route::prefix("booking/check")->controller(BookingFileController::class)->group(function () {
            Route::post("in", "CheckIn");
            Route::post("out", "CheckOut");
        });
    });

    Route::prefix("group")->group(function () {
        Route::controller(GroupController::class)->group(function () {
            Route::get("allGroup", "All_Group");
            Route::get("allUser", "All_User");
            Route::get("allFile", "All_File");
            //////Admin
            Route::get("myGroup", "myGroup");
            Route::get("myFile/{id_group}", "myFile");
            Route::get("myUser/{id_group}", "myUser");
           /// awner Or Admin

            Route::post("create", "create");
            Route::get("delete/{id_group}", "delete");

            Route::prefix("file")->group(function (){
                Route::post("add","AddFiletoGroup");
                Route::post("remove","RemoveFileFromGroup");
            });
            Route::prefix("user")->group(function (){
                Route::post("add","AddUsertoGroup");
                Route::post("remove","RemoveUserFromGroup");
            });

        });
    });

            Route::prefix("can")->group(function () {
                Route::controller(ProcessCanController::class)->group(function () {

            Route::get("group", "showgroup");
            Route::get("file/{id_group}", "showfile");



        });
    });

});


