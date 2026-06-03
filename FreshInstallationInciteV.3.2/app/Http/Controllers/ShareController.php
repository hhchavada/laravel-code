<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\EPaper;
use App\Models\LiveNews;
use App\Models\ShortVideo;
use App\Models\EPaperAnalytics;

class ShareController extends Controller
{
  
    public function shareBlog(Request $request)
    {
        $blogId = $request->input('id');
        $blog = Blog::where('id',$blogId)->with('image')->first();
        if(!$blog){
          return 'Invalid Blog Id';  
        }
        
        $isIOS = $request->header('user-agent') && strpos($request->header('user-agent'), 'iPhone') !== false;
       if ($isIOS) {
            $deepLinkUrl = setting('ios_schema') .'/blog/'. $blogId;
        } else {
            $deepLinkUrl = setting('android_schema') .'/blog/'. $blogId;
        }
        if ($isIOS) {
            $fallbackUrl = setting('appstore_url'); 
        } else {
            $fallbackUrl = setting('playstore_url');
        }
        $ogTags = "
            <meta property='og:title' content='{$blog->seo_title}'>
            <meta property='og:description' content='{$blog->seo_description}'>            
            <meta property='og:url' content='{$deepLinkUrl}'>
        ";
      	if (isset($blog->image) && $blog->image != '') {
            $blogImage = $blog->image->image ?? '';

            if ($blogImage != '') {
                $blogImageURL = url('uploads/blog/768x428/' . $blogImage);
                $ogTags .= "<meta property='og:image' content='{$blogImageURL}'>";
              
            }
        }
        $script = "
            <script>
                setTimeout(function() {
                    window.location.href = '$fallbackUrl';
                }, 100); // Redirect to the App Store or Play Store after 10 seconds
                window.location.href = '$deepLinkUrl';
            </script>
        ";
        $htmlContent = "<html><head>{$ogTags}</head><body>{$script}</body></html>";
        return response($htmlContent)->header('Content-Type', 'text/html');
    }
    
    
    public function shareENews(Request $request)
    {
        $Id = $request->id;

        $data = EPaper::where('id', $Id)->first();
        if (!$data) {
            return response('Invalid E News Id', 404);
        }

        // Analytics entry
        EPaperAnalytics::create([
            'e_paper_id' => $Id,
            'type'       => 'share',
        ]);

        // Detect iOS
        $userAgent = $request->header('User-Agent', '');
        $isIOS = str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad');

        // Deep link
        $deepLinkUrl = $isIOS
            ? setting('ios_schema') . '/e-news/' . $Id
            : setting('android_schema') . '/e-news/' . $Id;

        // Fallback store URL
        $fallbackUrl = $isIOS
            ? setting('appstore_url')
            : setting('playstore_url');

        // Open Graph tags
        $ogTags = "
            <meta property='og:title' content=\"" . e($data->name) . "\">
            <meta property='og:description' content=\"" . e($data->name) . "\">
            <meta property='og:url' content=\"" . e($deepLinkUrl) . "\">
        ";

        if (!empty($data->image)) {
            $imageUrl = url('uploads/e-paper/' . $data->image);
            $ogTags .= "<meta property='og:image' content=\"" . e($imageUrl) . "\">";
        }

        // Redirect script
        $script = "
            <script>
                window.location.href = '{$deepLinkUrl}';
                setTimeout(function () {
                    window.location.href = '{$fallbackUrl}';
                }, 1500);
            </script>
        ";

        $htmlContent = "<!DOCTYPE html>
            <html>
                <head>{$ogTags}</head>
                <body>{$script}</body>
            </html>";

        return response($htmlContent)->header('Content-Type', 'text/html');
    }

    
    
    public function shareLiveNews(Request $request)
    {
        $Id = $request->id;
        $data = LiveNews::where('id',$Id)->first();
        if(!$data){
          return 'Invalid Live News Id';  
        }
        
        $isIOS = $request->header('user-agent') && strpos($request->header('user-agent'), 'iPhone') !== false;
       if ($isIOS) {
            $deepLinkUrl = setting('ios_schema') .'/live-news/'. $Id;
        } else {
            $deepLinkUrl = setting('android_schema') .'/live-news/'. $Id;
        }
        if ($isIOS) {
            $fallbackUrl = setting('appstore_url'); 
        } else {
            $fallbackUrl = setting('playstore_url');
        }
        $ogTags = "
            <meta property='og:title' content='{$data->company_name}'>
            <meta property='og:description' content='{$data->company_name}'>           
            <meta property='og:url' content='{$deepLinkUrl}'>
        ";
      	if (isset($data->image) && $data->image != '') {
            $Image = $data->image ?? '';

            if ($Image != '') {
                $ImageURL = url('uploads/live-news/' . $Image);
                $ogTags .= "<meta property='og:image' content='{$ImageURL}'>";
              
            }
        }
        $script = "
            <script>
                setTimeout(function() {
                    window.location.href = '$fallbackUrl';
                }, 100); // Redirect to the App Store or Play Store after 10 seconds
                window.location.href = '$deepLinkUrl';
            </script>
        ";
        $htmlContent = "<html><head>{$ogTags}</head><body>{$script}</body></html>";
        return response($htmlContent)->header('Content-Type', 'text/html');
    }
    
    
    public function shareShorts(Request $request)
    {
        $Id = $request->id;
        $data = ShortVideo::where('id',$Id)->first();
        if(!$data){
          return 'Invalid Short Video Id';
        }
        
        $isIOS = $request->header('user-agent') && strpos($request->header('user-agent'), 'iPhone') !== false;
        if ($isIOS) {
            $deepLinkUrl = setting('ios_schema') .'/shorts/'. $Id;
        } else {
            $deepLinkUrl = setting('android_schema') .'/shorts/'. $Id;
        }
        if ($isIOS) {
            $fallbackUrl = setting('appstore_url'); 
        } else {
            $fallbackUrl = setting('playstore_url');
        }
        $ogTags = "
            <meta property='og:title' content='{$data->title}'>
            <meta property='og:description' content='{$data->description}'>           
            <meta property='og:url' content='{$deepLinkUrl}'>
        ";
      	if (isset($data->background_image) && $data->background_image != '') {
            $Image = $data->background_image ?? '';

            if ($Image != '') {
                $ImageURL = url('uploads/short_video/' . $Image);
                $ogTags .= "<meta property='og:image' content='{$ImageURL}'>";
              
            }
        }
        $script = "
            <script>
                setTimeout(function() {
                    window.location.href = '$fallbackUrl';
                }, 100); // Redirect to the App Store or Play Store after 10 seconds
                window.location.href = '$deepLinkUrl';
            </script>
        ";
        $htmlContent = "<html><head>{$ogTags}</head><body>{$script}</body></html>";
        return response($htmlContent)->header('Content-Type', 'text/html');
    }
    
}