<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use anlutro\LaravelSettings\Facade as ContentSetting;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use DB;
use Illuminate\Support\Facades\Http;

class UpdateApplicationController extends Controller
{

    public function index(Request $request)
    {
        try{
            $data['result'] = array();
            return view('admin.check-update.index',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }


    public function updateWebsite()
    {
        $this->info('Downloading the update...');
        $zipFile = 'update.zip';
        $updateUrl = 'https://updatescc.fluttertop.com/incite/' . $zipFile;

        $response = Http::get($updateUrl);
        if ($response->successful()) {
            file_put_contents($zipFile, $response->body());

            $zip = new ZipArchive;
            $extractToDir = base_path();
            $this->info('Before extraction: ' . $extractToDir);
            if ($zip->open($zipFile) === TRUE) {
                // Adjust the path as needed
                $zip->extractTo($extractToDir);
                $zip->close();
                // Delete migrations
                DB::table('migrations')->delete();
                // Run database migrations
                Artisan::call('migrate', ['--force' => true, '--seed' => true]);
                // Existing code for post-update logic...
                Artisan::call('up');

                // Additional commands to clear caches and configurations
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                
                ContentSetting::set('website_updates', false);
                ContentSetting::save();
                // Existing code to bring the application out of maintenance mode...
                return redirect()->back()->with('success', "Site updated");
            } else {
                $this->info('Failed to open the zip file: ' . $zipFile);
            }
        } else {
            $this->info('Failed to download update: ' . $response->status());
            return redirect()->back()->with('error', "Failed to download update");
        }
    }
  
  	private function info($string)
  	{
        \Log::info($string);
    }
}