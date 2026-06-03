<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use SimplePie;
use SimpleXMLElement;

class RssFeed extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "rss_feeds";

    public function category(){
        return $this->hasOne('App\Models\Category',"id","category_id");
    }
    public function language(){
        return $this->hasOne('App\Models\Language',"id","language_id");
    }

    /**
     * Fetch list of data from here
    **/
    public static function getLists($search){
        try {
            $obj = new self;
            $pagination = (isset($search['perpage']))?$search['perpage']:config('constant.pagination');
            if(isset($search['category_id']) && $search['category_id']!=''){
                $obj = $obj->where('category_id',$search['category_id']);
            }
            if(isset($search['language_id']) && $search['language_id']!=''){
                $obj = $obj->where('language_id',$search['language_id']);
            }
            if(isset($search['name']) && !empty($search['name'])){
                $obj = $obj->where('rss_name', 'like', '%'.trim($search['name']).'%');
            }      
            if(isset($search['status']) && $search['status']!=''){
                $obj = $obj->where('status',$search['status']);
            }
            $data = $obj->latest('created_at')->paginate($pagination)->appends('perpage', $pagination);
            if(count($data)){
                foreach($data as $row){
                    $row->blog_count = BlogCategory::where('category_id',$row->id)->count();
                }
            }
            return $data;
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }
    /**
     * Add or update data
    **/
    public static function addUpdate($data,$id=0) {
        try {
            $obj = new self;
            unset($data['_token']);

            if (isset($data['post_auto_publish'])) {
                if($data['post_auto_publish']=='on'){
                $data['post_auto_publish'] = 1;
                }else{
                $data['post_auto_publish'] = $data['post_auto_publish'];
                }
            }else{
                $data['post_auto_publish'] = 0;
            }

            if($id==0){
                $data['created_at'] = date('Y-m-d H:i:s');
                $enter_id= $obj->insertGetId($data);
                return ['status' => true, 'message' => __('lang.message_success_add')];
            }
            else{
                $data['updated_at'] = date('Y-m-d H:i:s');
                $obj->where('id',$id)->update($data);
                return ['status' => true, 'message' => __('lang.message_success_update')];
            }  
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }
    /**
     * Delete particular category
    **/
    public static function deleteRecord($id) {
        try {
            $obj = new self;    
            $obj->where('id',$id)->delete();   
            return ['status' => true, 'message' => __('lang.message_success_delete')];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Update Columns 
    **/
    public static function updateColumn($id,$value){
        try {
            $obj = new self;
            $data['status'] = $value;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $obj->where('id',$id)->update($data);
            return ['status' => true, 'message' => __('lang.message_status_change_success')];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }


    /**
     * Update Columns 
    **/
    public static function updateAutoPublishColumn($id,$value){
        try {
            $obj = new self;
            $data['post_auto_publish'] = $value;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $obj->where('id',$id)->update($data);
            return ['status' => true, 'message' => __('lang.message_status_change_success')];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    public static function getFeedLists($search)
    {
        try {
            $obj = new self;
            $data = [];
    
            // Apply filters only if at least one is provided
            if (!empty($search['category_id']) || !empty($search['source_id'])) {
                if (!empty($search['category_id'])) {
                    $obj = $obj->where('category_id', $search['category_id']);
                }
    
                if (!empty($search['source_id'])) {
                    $obj = $obj->where('id', $search['source_id']);
                }
    
                $data = $obj->where('status', 1)->latest('created_at')->get();
            } else {
                // No filter applied, return empty paginated result
                return \Helpers::arrayPaginator([], $search);
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
                                    'source_name' => $row->rss_name
                                ];
    
                                $items[] = $item_data;
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
                                    'source_name' => $row->rss_name
                                ];
    
                                $items[] = $entry_data;
                            }
                        }
                    }
                }
            }
    
            return \Helpers::arrayPaginator($items, $search);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()];
        }
    }
    
    // when error
    public static function getFeedListsWhenError($search){
        try {
            $pagination = (isset($search['perpage']))?$search['perpage']:config('constant.pagination');
            
            $obj = new self;
            
            $data = $obj->where('id',0)->latest('created_at')->paginate($pagination)->appends('perpage', $pagination);

            return $data;
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    
}
