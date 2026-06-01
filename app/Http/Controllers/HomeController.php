<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function shareBlog(Request $request)
    {
        $blogId = $request->input('blog_id');
        // Build the deep link URL for the app
        $deepLinkUrl = setting('whatsapp_link').''.$blogId;

        // Build the fallback URL
        $fallbackUrl = 'https://play.google.com/store/apps/details?id=com.incite.app';

        return redirect()->away($deepLinkUrl)->header('Refresh', '10;url=' . $fallbackUrl);
    }
}
