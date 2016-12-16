<?php

namespace bishopm\base\Middleware;

use Closure, Illuminate\Support\Facades\Auth;

class RoleMiddleware {

	public function handle($request, Closure $next, $role)
	{
	    if (Auth::guest()) {
	        return redirect('/login');
	    } 
	    $roles=explode("#",$role);
	    foreach ($roles as $rr){
	    	if ($request->user()->hasRole($rr)) {	
	    		view()->share('currentUser', Auth::user());
				return $next($request);	    		
	    	}
	    }
	    return redirect('/admin');
	}
}