<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use App\Aspects\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use TheSeer\Tokenizer\Exception;

#[Logger]
class test extends Controller
{

    public function listFile(Request $request) {

        echo("in controller");
            $response = File::query()->select('id', 'name', 'status')->get();
            return response()->json($response);

    }


    public function create(Request $request)
    {
        var_dump('hello from create method');
    }
}
