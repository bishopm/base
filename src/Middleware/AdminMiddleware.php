<?php

namespace bishopm\base\Middleware;

use Closure, Illuminate\Support\Facades\Auth;

class AdminMiddleware {

	public function handle($request, Closure $next)
    {
    	if (Auth::user()){
	        return $next($request);
	    } else {
	    	return redirect()->route('login');
	    }
    }

}