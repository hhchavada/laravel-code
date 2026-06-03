<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use App\Models\Ad;
use App\Models\ShortVideo;
use App\Models\AdminNotification ;
use DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['blog'] = Blog::where('status',1)->where('type','post')->count();
        $data['quote'] = Blog::where('status',1)->where('type','quote')->count();
        $data['category'] = Category::where('status',1)->count();
        $data['user'] = User::where('status',1)->where('type','user')->count();
        $data['ad'] = Ad::where('status',1)->count();
        $data['short_video'] = ShortVideo::where('status',1)->count();
        $data['most_viewed_blogs'] = Blog::getMostViewedBlogs();
        $data['most_selected_categories'] = Category::getMostSelectedCategories();
        $totalGmailUsers = User::where('type','user')->where('login_from','google')->where('status',1)->count();
        $data['total_gmail'] = ($totalGmailUsers > 0) ? ($data['user'] / $totalGmailUsers) * 100 : 0;
        $totalEmailUsers = User::where('type','user')->where('login_from','email')->where('status',1)->count();
        $data['total_email'] = ($totalEmailUsers > 0) ? ($data['user'] / $totalEmailUsers) * 100 : 0;

        // Count total users
        $totalUsers = User::where('type', 'user')->where('status', 1)->count();

        // Get Android and iOS user counts
        $totalAndroidUsers = User::where('type', 'user')->where('device_type', 'android')->where('status', 1)->count();

        $totalIosUsers = User::where('type', 'user')->where('device_type', 'ios')->where('status', 1)->count();

        // Calculate percentages
        $data['android_percentage'] = ($totalUsers > 0) ? ($totalAndroidUsers / $totalUsers) * 100 : 0;
        $data['ios_percentage'] = ($totalUsers > 0) ? ($totalIosUsers / $totalUsers) * 100 : 0;


        // Get user counts by login type
        $totalGmailUsers = User::where('type', 'user')->where('login_from', 'google')->where('status', 1)->count();
        $totalEmailUsers = User::where('type', 'user')->where('login_from', 'email')->where('status', 1)->count();
        $totalAppleUsers = User::where('type', 'user')->where('login_from', 'apple')->where('status', 1)->count();

        // Calculate percentages with 2 decimal places
        $data['total_gmail'] = ($totalUsers > 0) ? number_format(($totalGmailUsers / $totalUsers) * 100, 2) : 0;
        $data['total_email'] = ($totalUsers > 0) ? number_format(($totalEmailUsers / $totalUsers) * 100, 2) : 0;
        $data['total_apple'] = ($totalUsers > 0) ? number_format(($totalAppleUsers / $totalUsers) * 100, 2) : 0;

        return view('admin.dashboard.index',$data);
    }


    public function markAllAsRead(Request $request)
    {
        AdminNotification::where('is_read', 0)->update(['is_read' => 1]);

        return response()->json([
            'status' => true,
            'message' => __('lang.admin_notification_marked_as_read')
        ]);
    }


    public function removeNotification(Request $request)
    {
        $notification = AdminNotification::find($request->id);

        if ($notification) {
            $notification->is_remove = 1;
            $notification->save();

            return response()->json(['status' => true, 'message' => __('lang.admin_notification_removed')]);
        }

        return response()->json(['status' => false, 'message' => __('lang.admin_notification_not_found')]);
    }



}
