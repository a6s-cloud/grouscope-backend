<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Str;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestParameters = $request->all();
        $snakeParameters = array();
        foreach($requestParameters as $key => $value) {
            $snakeParameters[Str::snake($key)] = $value;
        }
        $request->replace($snakeParameters);

        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
