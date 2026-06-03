<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;
use anlutro\LaravelSettings\Facade as ContentSetting;

class UpdateApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application code from the folder';

    /**
     * Execute the console command.
     */

        public function handle()
    {
        $this->info('Checking for new updates...');

        $localVersion = $this->getVersion(base_path('public/version.json'));
        $latestVersion = $this->getRemoteVersion('https://updatescc.fluttertop.com/incite/version.json');
        
        if ($localVersion < $latestVersion) {
            ContentSetting::set('website_updates', true);
            ContentSetting::save();
            $this->info("New update available: {$latestVersion}. Starting update process...");
        } else {
            ContentSetting::set('website_updates', false);
            ContentSetting::save();
            $this->info("No update available. The current version is {$localVersion}.");
        }
    }

        private function getVersion($filePath)
    {
        return json_decode(file_get_contents($filePath), true)['version'];
    }

        private function getRemoteVersion($url)
    {
        return json_decode(file_get_contents($url), true)['version'];
    }

}
