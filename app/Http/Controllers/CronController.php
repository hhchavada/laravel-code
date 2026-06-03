<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    public function runRssFeed()
    {

        $check  = Artisan::call('rss:auto-publish');

        return response()->json(['message' => 'RSS Feed processed successfully!']);
    }
}
