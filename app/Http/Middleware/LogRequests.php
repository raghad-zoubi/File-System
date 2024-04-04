<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        // تسجيل الطلب
        DB::table('requests')->insert([
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => json_encode($request->header()),
            'body' => $request->getContent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // تنفيذ الطلب والحصول على الاستجابة
        $response = $next($request);

        // تسجيل الاستجابة
//        DB::table('responses')->insert([
//            'request_id' => $request->id, // تحتاج إلى تعديل هذا الجزء وفقًا لهيكل جدول البيانات الخاص بك
//            'status_code' => $response->getStatusCode(),
//            'headers' => json_encode($response->header()),
//            'body' => $response->getContent(),
//            'created_at' => now(),
//            'updated_at' => now(),
//        ]);
//
        return $response;
    }
}
