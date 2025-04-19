<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\ProjectInvitation;

class ShareInvitationCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Only share invitation count for engineers and contractors
            if ($user->hasRole('engineer') || $user->hasRole('contractor')) {
                $invitationCount = ProjectInvitation::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->count();
                
                View::share('invitationCount', $invitationCount);
            }
        }
        
        return $next($request);
    }
}
