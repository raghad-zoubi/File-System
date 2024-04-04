<?php

namespace App\Aspects;

use AhmadVoid\SimpleAOP\Aspect;
use Illuminate\Support\Facades\Log;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Logger implements Aspect
{
    // The constructor can accept parameters for the attribute


    private $message = 'Logging...';

    public function __construct(string $message = 'Logging...')
    {
        echo "      __construct ";
    }

    public function executeBefore($request, $controller, $method)
    {
        echo "   before   ";
        Log::info($this->message);
        Log::info('Request: ' . $request->fullUrl());
        Log::info('Controller: ' . get_class($controller));
        Log::info('Header: ' . json_encode($request->header()));
        Log::info('Body: ' . json_encode($request->all()));

    }

    public function executeAfter($request, $controller, $method, $response)
    {
        echo "   After   ";

        Log::info('Response: ' . $response->getContent());
    }

    public function executeException($request, $controller, $method, $exception)
    {
        echo " exception  ";
        Log::error($exception->getMessage());
    }
}
