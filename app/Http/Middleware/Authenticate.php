<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('balai') || $request->is('balai/*')) {
            return route('balai.login');
        }

        return $request->expectsJson() ? null : route('login');
    }
}