<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use App\Models\User;
use App\Models\RssFeed;
use App\Models\Category;
use App\Models\Language;
use App\Models\BlogTranslation;
use App\Models\BlogImage;
use App\Models\BlogCategory;
use App\Models\DeviceToken;
use App\Models\UserFeed;
use Auth;
use DB;
use Illuminate\Http\Request;

class RssAutoPublishPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:auto-publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically fetch and store RSS feed posts for eligible feeds';

    /**
     * Execute the console command.
     *
     * @return int
     */
     
    public function handle()
    {
        $currentTime = now();
    
        // Fetch RSS feeds that have auto-publishing enabled
        $rssFeeds = RssFeed::where('post_auto_publish', 1)->get();
    
        foreach ($rssFeeds as $rssFeed) {
            $lastUpdated = $rssFeed->updated_at;
            $autoPublishHour = $rssFeed->auto_publish_hour;
    
            // If updated_at is null, initialize it with the current time
            if (!$lastUpdated) {
                $rssFeed->updated_at = $currentTime;
                $rssFeed->save();
                continue; // Skip publishing this time
            }
    
            // Calculate the time difference in hours
            $hoursSinceLastUpdate = $lastUpdated->diffInHours($currentTime);
    
            // Check if the difference matches auto_publish_hour
            if ($hoursSinceLastUpdate < $autoPublishHour) {
                continue; // Skip this cycle
            }
    
            // Fetch RSS feed posts
            $feedResults = $this->getFeedLists($rssFeed->id, $rssFeed->category_id, $rssFeed->rss_name);
            $autoPublishPostCount = $rssFeed->auto_publish_post_count ?? 5;
    
            $publishedCount = 0;
    
            foreach ($feedResults as $row) {
                if (Blog::where('title', $row['title'])->exists()) {
                    continue;
                }
    
                if (!$row['title'] || !$row['description']) {
                    continue;
                }
    
                if ($publishedCount >= $autoPublishPostCount) {
                    break;
                }
    
                // Generate slug
                $slug = \Helpers::createSlug($row['title'], 'blog', 0, false);
    
                // Insert blog post
                $blogData = [
                    'slug' => $slug,
                    'type' => "post",
                    'title' => $row['title'],
                    'description' => "<p>{$row['description']}</p>",
                    'seo_title' => $row['title'],
                    'seo_description' => $row['description'],
                    'source_link' => $row['link'],
                    'source_name' => $row['source_name'],
                    'created_by' => Auth::id() ?? 1,
                    'schedule_date' => date("Y-m-d H:i:s", strtotime($row['pubDate'])),
                    'status' => 1,
                    'created_at' => now(),
                ];
    
                $blogId = Blog::insertGetId($blogData);
    
                if ($blogId) {
                    $publishedCount++;
    
                    // Update the `updated_at` timestamp to prevent duplicate publishing
                    $rssFeed->updated_at = now();
                    $rssFeed->save();
    
                    // Insert translations
                    $languages = Language::where('status', 1)->get();
                    foreach ($languages as $language) {
                        BlogTranslation::create([
                            'blog_id' => $blogId,
                            'language_code' => $language->code ?? setting('preferred_site_language'),
                            'title' => $row['title'],
                            'description' => $row['description'],
                            'created_at' => now(),
                        ]);
                    }
    
                    // Handle image upload
                    if (!empty($row['image'])) {
                        $uploadImage = \Helpers::uploadFilesThroughUrlAfterResizeCompress($row['image'], 'blog/');
                        if ($uploadImage['status']) {
                            BlogImage::create([
                                'image' => $uploadImage['file_name'],
                                'blog_id' => $blogId,
                                'created_at' => now(),
                            ]);
                        }
                    }
    
                    // Handle category associations
                    if (!empty($row['category_id'])) {
                        $category = Category::find($row['category_id']);
                        if ($category) {
                            BlogCategory::create([
                                'category_id' => $row['category_id'],
                                'type' => $category->parent_id == 0 ? 'category' : 'subcategory',
                                'blog_id' => $blogId,
                                'created_at' => now(),
                            ]);
    
                            if ($category->parent_id) {
                                BlogCategory::create([
                                    'category_id' => $category->parent_id,
                                    'type' => 'category',
                                    'blog_id' => $blogId,
                                    'created_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    
        return Command::SUCCESS;
    }

    
    // For fetch rss feed post
    public static function getFeedLists($rssId, $category_id, $rss_name)
    {
        try {
            $obj = new RssFeed();
            $data = [];
    
            if (isset($category_id) && $category_id != '') {
                $obj = $obj->where('category_id', $category_id);
            }
    
            if (isset($rssId) && $rssId != '') {
                $obj = $obj->where('id', $rssId);
            }
    
            if (isset($category_id) || isset($rssId)) {
                $data = $obj->where('status', 1)->latest('created_at')->get();
            }
    
            $items = [];
            if (count($data)) {
                foreach ($data as $row) {
                    $url = $row->rss_url;
                    $category_id = $row->category_id;
                    $rss = simplexml_load_file($url);
    
                    if (isset($rss->channel->item) && count($rss->channel->item)) {
                        foreach ($rss->channel->item as $item) {
                            $title = (string) $item->title;
                            $check_blog = Blog::where('title', $title)->first();
                            if (!$check_blog) {
                                $link = (string) $item->link;
                                $description = isset($item->content)
                                    ? html_entity_decode(strip_tags((string) $item->content))
                                    : html_entity_decode(strip_tags((string) $item->description));
                                $pubDate = (string) $item->pubDate;
    
                                $image = null;
                                if (isset($item->enclosure['url'])) {
                                    $image = (string) $item->enclosure['url'];
                                } elseif (isset($item->children('media', true)->content->attributes()['url'])) {
                                    $image = (string) $item->children('media', true)->content->attributes()['url'];
                                } elseif (isset($item->children('media', true)->thumbnail->attributes()['url'])) {
                                    $image = (string) $item->children('media', true)->thumbnail->attributes()['url'];
                                } elseif (isset($item->children('media', true)->group->content)) {
                                    foreach ($item->children('media', true)->group->content as $content) {
                                        if ($content->attributes()['medium'] == 'image') {
                                            $image = (string) $content->attributes()['url'];
                                            break;
                                        }
                                    }
                                }
    
                                $item_data = [
                                    'title' => $title,
                                    'link' => $link,
                                    'description' => $description,
                                    'pubDate' => $pubDate,
                                    'image' => $image,
                                    'category_id' => $category_id,
                                    'source_name' => $row->rss_name,
                                ];
    
                                array_push($items, $item_data);
                            }
                        }
                    } else {
                        foreach ($rss->entry as $entry) {
                            $title = (string) $entry->title;
                            $check_blog = Blog::where('title', $title)->first();
                            if (!$check_blog) {
                                $link = (string) $entry->link;
                                $description = $entry->content;
                                $pubDate = (string) $entry->published;
    
                                $image = null;
                                if (isset($entry->enclosure['url'])) {
                                    $image = (string) $entry->enclosure['url'];
                                } elseif (isset($entry->children('media', true)->content->attributes()['url'])) {
                                    $image = (string) $entry->children('media', true)->content->attributes()['url'];
                                } elseif (isset($entry->children('media', true)->thumbnail->attributes()['url'])) {
                                    $image = (string) $entry->children('media', true)->thumbnail->attributes()['url'];
                                }
    
                                $entry_data = [
                                    'title' => $title,
                                    'link' => $link,
                                    'description' => $description,
                                    'pubDate' => $pubDate,
                                    'image' => $image,
                                    'category_id' => $category_id,
                                    'source_name' => $row->rss_name,
                                ];
    
                                array_push($items, $entry_data);
                            }
                        }
                    }
                }
            }
            return $items;
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ];
        }
    }
    
}
