<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $permission = $this->getPermissionFromRequest($request);
        if (Auth::check() && !Auth::user()->can($permission)) {
            abort(403);
        }

        return $next($request);
    }

    private function getPermissionFromRequest($request)
    {
        $segments = $request->segment('2');
        
        $permission = $segments;
        
        if($segments == 'setlang'){
            $permission = 'dashboard'; 
        }
        
        if($segments == 'ads-sortable'){
           $permission = 'ads'; 
        }
         
        if($segments == 'post'){
           $permission = 'blog'; 
        }
        
        if($segments == 'add-post'){
            $permission = 'add-blog'; 
        }
        
        if($segments == 'add-quote'){
            $permission = 'add-blog'; 
        }
        
        if($segments == 'update-post'){
            $permission = 'update-blog'; 
        }
        
        if($segments == 'update-quote'){
            $permission = 'update-blog'; 
        }
        
        if($segments == 'delete-quote'){
            $permission = 'delete-blog'; 
        }
        
        if($segments == 'delete-post'){
            $permission = 'delete-blog'; 
        }

        if($segments == 'delete-selected-post'){
            $permission = 'delete-blog'; 
        }
        
        if($segments == 'delete-selected'){
            $permission = 'delete-blog'; 
        }

        if($segments == 'delete-selected-ad'){
            $permission = 'delete-ad'; 
        }

        if($segments == 'delete-selected-short-video'){
            $permission = 'delete-short-video'; 
        }
        
        if($segments == 'update-post-status'){
            $permission = 'update-blog-status'; 
        }
        
        if($segments == 'send-notification-to-users'){
            $permission = 'push-notification';
        }
        
        if($segments == 'create-short-video'){
            $permission = 'add-short-video';
        }
        
        if($segments == 'update-short-video-status'){
            $permission = 'update-short-video-column';
        }
        
        if($segments == 'edit-short-video'){
            $permission = 'update-short-video';
        }
        
        if($segments == 'short-video-analytics'){
            $permission = 'analytics-short-video';
        }
        
        if($segments == 'check-rss'){
            $permission = 'rss-feeds';
        }
        
        if($segments == 'ad-analytics'){
            $permission = 'analytics-ad';
        }
        
        if($segments == 'send-push-notification'){
            $permission = 'push-notification';
        }
        
        if($segments == 'languages'){
            $permission = 'language';
        }
        
        if($segments == 'translation-topic'){
            $permission = 'topic-translation';
        }

        if($segments == 'update-rss-autopublish-status'){
            $permission = 'update-social-media-status';
        }
        
        if($segments == 'referral-details'){
           $permission = 'user'; 
        }
        

        return $permission;
    }
}
