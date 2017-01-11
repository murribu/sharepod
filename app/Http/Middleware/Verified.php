<?php
namespace App\Http\Middleware;

use Closure;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

class Verified
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
        $user = Auth::user();
        if ($user && $user->verified){
            return $next($request);
        }

        return response()->json(['message' => 'You must verify your email address first'], 403);
    }
}
