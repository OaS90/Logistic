<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class Logger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var JsonResponse $response */
        $response = $next($request);

        if ($response instanceof View) {
            return $response;
        }

        $route = $request->route() ?? 'undefined';
        $route = str_replace('/', '.', $route->uri());

        if (!is_string($route)) {
            $route = 'undefined';
        }

        $deviceUid = request()->header('h-device-uuid');

        // Генерация или получение global_request_id
        $keyTracePrefix = env('APP_NAME', 'sessions') . '_';
        $keyTrace = env('APP_KEY_TRACE', 'x-trace-id');
        $rid = request()->header($keyTrace) ?? uniqid($keyTracePrefix);
        request()->headers->set($keyTrace, $rid);

        Log::withContext([
            'rid' => $rid,
            'route' => $route,
            'request_path' => $request->path(),
            'request_method' => $request->method()
        ]);

        if ($deviceUid) {
            Log::shareContext(['deviceUID' => $deviceUid]);
        }

        Log::info("Input: params " . json_encode($request->all(), JSON_UNESCAPED_UNICODE) . ' headers '
                . json_encode($request->headers->all(), JSON_UNESCAPED_UNICODE), [
            'type' => 'input'
        ]);

        $startTime = microtime(true);

        $endTime = microtime(true);

        if ($response instanceof JsonResponse) {
            $message = json_encode($response->getData(), JSON_UNESCAPED_UNICODE);
        } elseif ($response instanceof \Illuminate\Http\Response) {
            $message = $response->getContent();
        } elseif (is_array($response)) {
            $message = json_encode($response, JSON_UNESCAPED_UNICODE);
        } else {
            $message = 'Invalid json response';
        }

        $context = [
            'type' => 'output',
            'response_status' => $response->status(),
            'execution_time' => round($endTime - $startTime, 3)
        ];

        $logMethod = 'info';

        if ($context['response_status'] >= 400) {
            $logMethod = 'notice';
        }

        if ($context['response_status'] >= 500) {
            $logMethod = 'error';
        }

        Log::$logMethod("Output: " .$message, $context);

        return $response;
    }
}
