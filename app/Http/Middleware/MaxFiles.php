<?php

namespace App\Http\Middleware;

use App\Exceptions\FilesMaxException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class MaxFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user->Myfiles()->count() < (int)env("Max_Files_User",10)){
            return $next($request);
        }

        return response()->json(['message'=>'more than 10 file']);
    }
}
