<?php

namespace Antidote\LaravelCartStripe\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class WhitelistStripeIPAddresses
{
    private $ip_addresses = [
        "3.18.12.63",
        "3.130.192.231",
        "13.235.14.237",
        "13.235.122.149",
        "18.211.135.69",
        "35.154.171.200",
        "52.15.183.38",
        "54.88.130.119",
        "54.88.130.237",
        "54.187.174.169",
        "54.187.205.235",
        "54.187.216.72",
        //internal for use in docker
        "172.21.0.1"
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(App::environment('local', 'development', 'testing'))
        {
            $this->ip_addresses[] = '127.0.0.1';
        }

        Log::info($request->getClientIp());
        if(!in_array($request->getClientIp(), $this->ip_addresses))
        {
            Log::info('Stripe Attempt from IP: '.$request->getClientIp());
            abort(403, "Unauthorized Access Stripe");
        }

        return $next($request);
    }
}
