<?php

namespace App\MyApplication;

class MyApp
{
    private static  $app = null;

    private static   $uploadFile = null;
    private static  $json = null;

    private function __construct()
    {

    }

    public static function getApp()
    {
        if(is_null(self::$app)){
            self::$app = new MyApp();
        }
        return self::$app;
    }

    public static function Json()
    {
        if(is_null(self::$json)){
            self::$json = new Json();
        }
        return self::$json;
    }
    public static function uploadFile()
    {
        if(is_null(self::$uploadFile)){
            self::$uploadFile = new UploadFile();
        }
        return self::$uploadFile;
    }

}
