<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use App\Models\User;
use App\Models\ShortVideo;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish';
    protected $description = 'Publish scheduled blog posts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $scheduledPosts = Blog::where('status', 4) 
                              ->where('schedule_date', '<=', now()) 
                              ->get();

        foreach ($scheduledPosts as $post) {
            $post->status = 1;
            $post->updated_at = $post->created_at;
            $post->save();
        }
        
        
        $scheduledShortVideoPosts = ShortVideo::where('status', 4) 
                              ->where('schedule_date', '<=', now()) 
                              ->get();

        foreach ($scheduledShortVideoPosts as $post) {
            $post->status = 1;
            $post->updated_at = $post->created_at;
            $post->save();
        }

        $this->info('Scheduled posts published successfully.');
    }
}
