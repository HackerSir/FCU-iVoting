<?php

namespace App\Http\Middleware;

use Closure;
use App\Console\Commands\LogApacheModStatus;
use Illuminate\Contracts\Routing\TerminableMiddleware;

//http://stackoverflow.com/questions/31619350/correct-way-to-get-server-response-time-in-laravel
class SendAnalytics implements TerminableMiddleware {

    public function __construct() {

    }

    public function handle($request, Closure $next) {
        return $next($request);
    }

    public function terminate($request, $response) {
        $responseTime = microtime(true) - LARAVEL_START;
        LogApacheModStatus::getLogger()->info($request->getPathInfo() . ',' .  $responseTime);
    }
}
