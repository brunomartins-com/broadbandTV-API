<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class VerifyKey
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle($request, Closure $next)
    {
        $response = [];
        $userCount = $this->user->where('key', '=', $request->key)->count();
        if($userCount == 0){
            return redirect('/api/key-error');
        }
        return $next($request);
    }
}
