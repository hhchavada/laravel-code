<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;
use Validator;
use App\Models\AdAnalytic;
use App\Models\Blog;
use App\Models\Category;
use App\Models\BlogCategory;
use App\Models\SearchLog;
use App\Models\Vote;
use App\Models\BlogTranslation;
use App\Models\Ad;
use App\Models\User;
use App\Models\BlogAnalytic;
use App\Models\BlogBookmark;
use App\Models\DeviceToken;
use App\Models\CategoryTranslation;
use App\Models\ShortVideo;
use App\Models\ShortVideoAnalytic;
use App\Models\AdminNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BlogAPIController extends Controller
{
    private $language;
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
        $this->language = $request->header('language-code') && $request->header('language-code') != '' ? $request->header('language-code') : 'en';
        app()->setLocale($this->language);
    }

    function getList(Request $request)
    {
        try{
            $pagination_no = config('constant.api_pagination');
            if(isset($search['per_page']) && !empty($search['per_page'])){
                $pagination_no = $search['per_page'];
            }
            $categories = Category::select('id','parent_id','name','image','color','is_featured','created_at','updated_at','deleted_at')->where('parent_id',0)->where('status',1)->get();
            if(count($categories)){
                foreach($categories as $category_data){
                    if($category_data->image!=''){
                      $category_data->image = url('uploads/category/'.$category_data->image);  
                    }
                    $categoryTranslate = CategoryTranslation::where('category_id',$category_data->id)->where('language_code',$this->language)->first();
                    if ($categoryTranslate) {
                        $category_data->name = $categoryTranslate->name;
                    }
                }
            }
            if(count($categories)){
                foreach($categories as $row){
                    $row->is_feed = false;
                    $row->api_token = $request->header('api-token');
                    if($request->header('api-token')!=''){
                        $user = User::where('api_token',$request->header('api-token'))->first();
                        if($user){
                            $row->is_feed = \Helpers::categoryIsInFeed($row->id,$user->id);
                        }                
                    }
                    $blog_arr = array();
                    $blog_arr = \Helpers::getBlogsArrOnTheBasisOfCategory($row->id);
                    $blogs = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image','is_featured')->where('status',1)->whereIn('id',$blog_arr)->where('schedule_date',"<=",date("Y-m-d H:i:s"))->with('blog_sub_category')->orderBy('schedule_date','DESC')->paginate($pagination_no)->appends('per_page', $pagination_no);
                    if(count($blogs)){
                        foreach($blogs as $blog){
                            $blogTranslate = BlogTranslation::where('blog_id',$blog->id)->where('language_code',$this->language)->first();
                            if ($blogTranslate) {
                                $blog->title = $blogTranslate->title;
                                $blog->description = $blogTranslate->description;
                            }
                            $blog->voice = setting('blog_voice');
                            $blog->accent_code = setting('blog_accent');
                            $blog->is_feed = false;
                            $blog->is_vote = 0;
                            $blog->is_bookmark = 0;
                            $blog->is_user_viewed = 0; 
                            if($request->header('api-token')!=''){
                                $user = User::where('api_token',$request->header('api-token'))->first();
                                if($user){
                                    $blog->is_feed = \Helpers::categoryIsInFeed($row->id,$user->id);
                                    $blog->is_vote = \Helpers::getVotes($blog->id,$user->id);              
                                    $blog->is_bookmark = \Helpers::getBookmarks($blog->id,$user->id);              
                                    $blog->is_user_viewed = \Helpers::getViewed($blog->id,$user->id);              
                                }  
                            } 
                                                    
                            $blog->visibilities = \Helpers::getVisibilities($blog->id);
                            $blog->question = \Helpers::getQuestionsOptions($blog->id);
                            $blog->images = \Helpers::getBlogImages($blog->id,'768x428');
                            if($blog->background_image!=''){
                                $blog->background_image = url('uploads/blog/'.$blog->background_image);
                            }
                            if(count($blog->blog_sub_category)){
                                foreach($blog->blog_sub_category as $blog_sub_category){
                                    if($blog_sub_category->category!=''){
                                        if($blog_sub_category->category->image!=''){
                                            $blog_sub_category->category->image = url('uploads/category/'.$blog_sub_category->category->image);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $row->blogs = $blogs;
                    
                }
            }
            $response = $this->sendResponse($categories, __('lang.message_data_retrived_successfully'));  
            return $response;
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }


    function getDetail(Request $request,$id)
    {
        try{
            $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image','is_featured')->where('status',1)->where('id',$id)->with('blog_category')->with('blog_sub_category')->first();
            if($blog){
                $blog->images = \Helpers::getBlogImages($blog->id,'768x428');
                if($blog->background_image!=''){
                    $blog->background_image = url('uploads/blog/'.$blog->background_image);
                }
                $blog->voice = setting('blog_voice');
                $blog->accent_code = setting('blog_accent');
            }
            return $this->sendResponse($blog, __('lang.message_data_retrived_successfully')); 

        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }

    
    function search(Request $request)
    {
        try {
            if ($request->userAuthData) {
                $keyword = trim($request->input('keyword'));
                $languageCode = $this->language;

                $blogs = Blog::select('id', 'type', 'title', 'description', 'source_name', 'source_link', 'video_url', 'is_voting_enable', 'schedule_date', 'created_at', 'updated_at', 'background_image', 'is_featured')
                    ->where(function ($query) use ($keyword, $languageCode) {
                        $query->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('tags', 'like', '%' . $keyword . '%')
                            ->orWhereHas('translations', function ($query) use ($keyword, $languageCode) {
                                $query->where('language_code', $languageCode)
                                    ->where('title', 'like', '%' . $keyword . '%')
                                    ->orWhere('description', 'like', '%' . $keyword . '%');
                            });
                    })
                    ->where('status', 1)
                    ->where('schedule_date', '<=', date("Y-m-d H:i:s"))
                    ->with('blog_category')
                    ->get();

                if (count($blogs)) {
                    foreach ($blogs as $blog) {
                        $blogTranslate = BlogTranslation::where('blog_id', $blog->id)->where('language_code', $this->language)->first();
                        if ($blogTranslate) {
                            $blog->title = $blogTranslate->title;
                            $blog->description = $blogTranslate->description;
                        }
                        $blog->voice = setting('blog_voice');
                        $blog->accent_code = setting('blog_accent');
                        $blog->is_feed = false;
                        $blog->is_vote = 0;
                        $blog->is_bookmark = 0;
                        $blog->is_user_viewed = 0;

                        if ($request->header('api-token') != '') {
                            $user = User::where('api_token', $request->header('api-token'))->first();
                            if ($user) {
                                $blog->is_feed = \Helpers::categoryIsInFeed($blog->id, $user->id);
                                $blog->is_vote = \Helpers::getVotes($blog->id, $user->id);
                                $blog->is_bookmark = \Helpers::getBookmarks($blog->id, $user->id);
                                $blog->is_user_viewed = \Helpers::getViewed($blog->id, $user->id);
                            }
                        }

                        $blog->visibilities = \Helpers::getVisibilities($blog->id);
                        $blog->question = \Helpers::getQuestionsOptions($blog->id);
                        $blog->images = \Helpers::getBlogImages($blog->id, '768x428');
                        if ($blog->background_image != '') {
                            $blog->background_image = url('uploads/blog/' . $blog->background_image);
                        }
                        if (count($blog->blog_sub_category)) {
                            foreach ($blog->blog_sub_category as $blog_sub_category) {
                                if ($blog_sub_category->category != '') {
                                    if ($blog_sub_category->category->image != '') {
                                        $categoryTranslate = CategoryTranslation::where('category_id', $blog_sub_category->category->id)->where('language_code', $this->language)->first();
                                        if ($categoryTranslate) {
                                            $blog_sub_category->category->name = $categoryTranslate->name;
                                        }
                                        $blog_sub_category->category->image = url('uploads/category/' . $blog_sub_category->category->image);
                                    }
                                }
                            }
                        }
                        if (count($blog->blog_category)) {
                            foreach ($blog->blog_category as $blog_category) {
                                if ($blog_category->category != '') {
                                    if ($blog_category->category->image != '') {
                                        $categoryTranslate = CategoryTranslation::where('category_id', $blog_category->category->id)->where('language_code', $this->language)->first();
                                        if ($categoryTranslate) {
                                            $blog_category->category->name = $categoryTranslate->name;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $search = array(
                    'user_id' => $request->userAuthData->id,
                    'keyword' => $request->input('keyword'),
                    'count' => count($blogs),
                    'created_at' => date('Y-m-d h:i:s')
                );
                SearchLog::insert($search);

                return $this->sendResponse($blogs, __('lang.message_data_retrived_successfully'));
            } else {
                return $this->sendError(__('lang.message_user_not_found'));
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }


    public function doVoteForOption(Request $request)
    {
        try{
            if($request->userAuthData){
                $validate = [
                    'blog_id' => 'required',
                    'option_id' => 'required'
                ];
                $validator = Validator::make($request->all(), $validate);
                if ($validator->fails()) {
                    return $this->sendError(__('lang.message_required_message'),$validator->errors());
                }                
                $checkVote = Vote::where('blog_id', $request->input('blog_id'))->where('user_id',$request->userAuthData->id)->first();
                // if ($checkVote) {
                //     return $this->sendError(__('lang.message_vote_already_exist'));
                // }
                
                $postData = array(
                    'blog_id'=>$request->input('blog_id'),
                    'option_id'=>$request->input('option_id'),
                    'user_id'=>$request->userAuthData->id,
                    'created_at' => date("Y-m-d H:i:s")
                );
                Vote::insert($postData);
                
                $analyticsArr = array(
                    'type'=> 'blog_poll_option',
                    'user_id'=> $request->userAuthData->id,
                    'blog_id' => $request->input('blog_id'),
                    'blog_poll_option_id' => $request->input('option_id'),
                    'created_at'=> date('Y-m-d H:i:s'),
                );   
                
                BlogAnalytic::insert($analyticsArr);
                
                $data = \Helpers::getQuestionsOptions($request->input('blog_id'));
                return $this->sendResponse($data, __('lang.message_vote_added_successfully'));
            }
            return $this->sendError(__('lang.message_user_not_found'));
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }

    public function addAnalytics(Request $request)
    {
        try{
            $post = $request->all();
            $user_id = 0;
            if($request->header('api-token')!=''){
                $user = User::where('api_token',$request->header('api-token'))->first();
                if($user){
                    $user_id = $user->id;
                }                
            }
            
            $fcm_token = '';
            if($request->header('fcm-token')!=''){
                $fcm_token = $request->header('fcm-token');          
            }
            
            if(isset($post) && !empty($post)){
                foreach($post as $post_data){  

                    if(isset($post_data['data_type']) && $post_data['data_type']=='ads'){
                        if($post_data['type']=='view' || $post_data['type']=='click'){
                            for($i=0;$i<count($post_data['ads_ids']);$i++){
                                    $analyticsArr = array(
                                        'type'=>$post_data['type'],
                                        'user_id'=>$user_id,
                                        'fcm_token' => $fcm_token,
                                        'ad_id' => $post_data['ads_ids'][$i],
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );                                
                                    AdAnalytic::insert($analyticsArr);
                            }
                        }    
                    } elseif(isset($post_data['data_type']) && $post_data['data_type']=='shorts'){
                        
                        if($post_data['type']=='view' || $post_data['type']=='share' ){
                            for($i=0;$i<count($post_data['shorts_ids']);$i++){
                                    $analyticsArr = array(
                                        'type'=>$post_data['type'],
                                        'user_id'=>$user_id,
                                        'fcm_token' => $fcm_token,
                                        'short_video_id' => $post_data['shorts_ids'][$i],
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );                                
                                    ShortVideoAnalytic::insert($analyticsArr);
                            }
                        }
                        
                        if($post_data['type']=='like'){
                            for($i=0;$i<count($post_data['shorts_ids']);$i++){
                                $checkAnalytics = ShortVideoAnalytic::where('type',$post_data['type'])->where('user_id',$user_id)->where('short_video_id',$post_data['shorts_ids'][$i])->first();
                                if(!$checkAnalytics){
                                    $analyticsArr = array(
                                        'type'=>$post_data['type'],
                                        'user_id'=>$user_id,
                                        'fcm_token' => $fcm_token,
                                        'short_video_id' => $post_data['shorts_ids'][$i],
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );                                
                                    ShortVideoAnalytic::insert($analyticsArr);
                                }else{
                                    $checkAnalytics->delete();
                                }
                            }
                        }
                        
                    } else{         
                        if($post_data['type']=='bookmark'){
                            for($i=0;$i<count($post_data['blog_ids']);$i++){
                                $checkBookmark = BlogBookmark::where('user_id',$user_id)->where('blog_id',$post_data['blog_ids'][$i])->first();
                                if(!$checkBookmark){
                                    $analyticsArr = array(
                                        'user_id'=>$user_id,
                                        'fcm_token' => $fcm_token,
                                        'blog_id'=>$post_data['blog_ids'][$i],
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );
                                    BlogBookmark::insert($analyticsArr);

                                    $user = User::select('id', 'name')->find($user_id);
                                    $blog = Blog::select('id', 'type', 'title', 'created_by')->find($post_data['blog_ids'][$i]);

                                    if ($blog && $blog->created_by != $user_id) {

                                        AdminNotification::create([
                                            'title'   => ucfirst($blog->type) . ' Bookmark',
                                            'message' => $user->name ?? '--' . ' bookmarked your ' . $blog->type,
                                            'is_read' => 0,
                                            'type'    => 'bookmark',
                                            'user_id' => $user_id,
                                            'blog_id' => $blog->id,
                                        ]);
                                    }

                                }                            
                            }                        
                        } 
                        if($post_data['type']=='view' || $post_data['type']=='share' || $post_data['type']=='poll_share'){
                            for($i=0;$i<count($post_data['blog_ids']);$i++){
                                    $analyticsArr = array(
                                        'type'=>$post_data['type'],
                                        'user_id'=>$user_id,
                                        'fcm_token' => $fcm_token,
                                        'blog_id' => $post_data['blog_ids'][$i],
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );                                
                                    BlogAnalytic::insert($analyticsArr);
                            }    
                        }
                        if($post_data['type']=='blog_time_spent' || $post_data['type']=='tts'){
                            if(isset($post_data['blogs']) && count($post_data['blogs'])){
                                foreach($post_data['blogs'] as $blog_time_spent){
                                    $analyticsArr = array(
                                        'type'=>$post_data['type'],
                                        'user_id'=>$user_id,
                                        'fcm_token' => $fcm_token,
                                        'blog_id'=>$blog_time_spent['id'],
                                        'start_date_time'=>date("Y-m-d H:i:s",strtotime($blog_time_spent['start_time'])),
                                        'end_date_time'=>date("Y-m-d H:i:s",strtotime($blog_time_spent['end_time'])),
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );
                                    BlogAnalytic::insert($analyticsArr);                                
                                }                            
                            }
                        }
                        if($post_data['type']=='app_time_spent'){
                            $analyticsArr = array(
                                'type'=>$post_data['type'],
                                'user_id'=>$user_id,
                                'fcm_token' => $fcm_token,
                                'start_date_time'=>date("Y-m-d H:i:s",strtotime($post_data['start_time'])),
                                'end_date_time'=>date("Y-m-d H:i:s",strtotime($post_data['end_time'])),
                                'created_at'=>date('Y-m-d H:i:s'),
                            );
                            BlogAnalytic::insert($analyticsArr);
                        } 
                        if($post_data['type']=='social_media_signin' || $post_data['type']=='sign_in' || $post_data['type']=='social_media_signup' || $post_data['type']=='sign_up'){
                            $checkAnalytics = BlogAnalytic::where('type',$post_data['type'])->where('user_id',$user_id)->first();
                            if(!$checkAnalytics){
                                $analyticsArr = array(
                                    'type'=>$post_data['type'],
                                    'user_id'=>$user_id,
                                    'fcm_token' => $fcm_token,
                                    'created_at'=>date('Y-m-d H:i:s'),
                                );
                                if(isset($post_data['start_time']) && $post_data['start_time']!=''){
                                    $analyticsArr['start_date_time'] = date("Y-m-d H:i:s",strtotime($post_data['start_time']));
                                }
                                if(isset($post_data['end_time']) && $post_data['end_time']!=''){
                                    $analyticsArr['end_date_time'] = date("Y-m-d H:i:s",strtotime($post_data['end_time']));
                                }
                                if(isset($post_data['action']) && $post_data['action']!=''){
                                    $analyticsArr['action'] = $post_data['action'];
                                }
                                BlogAnalytic::insert($analyticsArr);
                            }else{
                                $analyticsArr = array(
                                    'updated_at'=>date('Y-m-d H:i:s'),
                                );
                                if(isset($post_data['start_time']) && $post_data['start_time']!=''){
                                    $analyticsArr['start_date_time'] = date("Y-m-d H:i:s",strtotime($post_data['start_time']));
                                }
                                if(isset($post_data['end_time']) && $post_data['end_time']!=''){
                                    $analyticsArr['end_date_time'] = date("Y-m-d H:i:s",strtotime($post_data['end_time']));
                                }
                                if(isset($post_data['action']) && $post_data['action']!=''){
                                    $analyticsArr['action'] = $post_data['action'];
                                }
                                BlogAnalytic::where('id',$checkAnalytics->id)->update($analyticsArr);
                            }
                        }
                        if($post_data['type']=='remove_bookmark'){
                            for($i=0;$i<count($post_data['blog_ids']);$i++){
                                $checkBookmark = BlogBookmark::where('user_id',$user_id)->where('blog_id',$post_data['blog_ids'][$i])->first();
                                if($checkBookmark){
                                    BlogBookmark::where('user_id',$user_id)->where('blog_id',$post_data['blog_ids'][$i])->delete();
                                }                            
                            } 
                        }
                        if($post_data['type']=='logout'){
                            $checkToken = DeviceToken::where('user_id',$user_id)->update(['fcm_token'=>null]);
                            $analyticsArr = array(
                                'type'=>$post_data['type'],
                                'user_id'=>$user_id,
                                'fcm_token' => $fcm_token,
                                'created_at'=>date('Y-m-d H:i:s'),
                            );
                            BlogAnalytic::insert($analyticsArr);
                        }
                        
                        BlogAnalytic::where('type',null)->orWhere('type','')->delete();
                    }
                }
            }
            return $this->sendResponse([],__('lang.message_data_retrived_successfully'));
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }

    function doGetBookmarks(Request $request)
    {
        try{
            if($request->userAuthData){
                $pagination_no = config('constant.api_pagination');
                if(isset($search['per_page']) && !empty($search['per_page'])){
                    $pagination_no = $search['per_page'];
                }
                $blog_id_arr = array();
                $bookmarks = BlogBookmark::where('user_id',$request->userAuthData->id)->get();
                if(count($bookmarks)){
                    foreach($bookmarks as $bookmarks_data){
                        if(!in_array($bookmarks_data->blog_id,$blog_id_arr)){
                            array_push($blog_id_arr,$bookmarks_data->blog_id);
                        }
                    }
                }
                $blogs = array();
                if(count($blog_id_arr)){
                    $blogs = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image','is_featured')->where('status',1)->whereIn('id',$blog_id_arr)->where('schedule_date',"<=",date("Y-m-d H:i:s"))->with('blog_category')->with('blog_sub_category')->orderBy('schedule_date','DESC')->paginate($pagination_no)->appends('per_page', $pagination_no);
                    if(count($blogs)){
                        foreach($blogs as $blog){
                            $blogTranslate = BlogTranslation::where('blog_id',$blog->id)->where('language_code',$this->language)->first();
                            if ($blogTranslate) {
                                $blog->title = $blogTranslate->title;
                                $blog->description = $blogTranslate->description;
                            }
                            $blog->voice = setting('blog_voice');
                            $blog->accent_code = setting('blog_accent');
                            $blog->is_feed = false;
                            $blog->is_vote = 0;
                            $blog->is_bookmark = 0;
                            $blog->is_user_viewed = 0; 
                            if($request->header('api-token')!=''){
                                $user = User::where('api_token',$request->header('api-token'))->first();
                                if($user){
                                    $blog->is_feed = \Helpers::categoryIsInFeed($blog->id,$user->id);
                                    $blog->is_vote = \Helpers::getVotes($blog->id,$user->id);              
                                    $blog->is_bookmark = \Helpers::getBookmarks($blog->id,$user->id);              
                                    $blog->is_user_viewed = \Helpers::getViewed($blog->id,$user->id);              
                                }  
                            } 
                                                       
                            $blog->visibilities = \Helpers::getVisibilities($blog->id);
                            $blog->question = \Helpers::getQuestionsOptions($blog->id);
                            $blog->images = \Helpers::getBlogImages($blog->id,'768x428');
                            if($blog->background_image!=''){
                                $blog->background_image = url('uploads/blog/'.$blog->background_image);
                            }
                            if(count($blog->blog_sub_category)){
                                foreach($blog->blog_sub_category as $blog_sub_category){
                                    if($blog_sub_category->category!=''){
                                        if($blog_sub_category->category->image!=''){
                                            $blog_sub_category->category->image = url('uploads/category/'.$blog_sub_category->category->image);
                                            $categoryTranslate = CategoryTranslation::where('category_id',$blog_sub_category->category->id)->where('language_code',$this->language)->first();
                                            if ($categoryTranslate) {
                                                $blog_sub_category->category->name = $categoryTranslate->name;
                                            }
                                        }
                                    }
                                }
                            }
                            if(count($blog->blog_category)){
                                foreach($blog->blog_category as $blog_category){
                                    if($blog_category->category!=''){
                                        if($blog_category->category->image!=''){
                                            $categoryTranslate = CategoryTranslation::where('category_id',$blog_category->category->id)->where('language_code',$this->language)->first();
                                            if ($categoryTranslate) {
                                                $blog_category->category->name = $categoryTranslate->name;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return $this->sendResponse($blogs, __('lang.message_data_retrived_successfully'));
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }
    
    
    public function getShortVideoLists(Request $request){
        try {
            $obj = ShortVideo::where('title', '!=', null);
        
            $search = $request->all();
            
            // Check if 'post_id' is in the request
            if (isset($search['post_id'])) {
                $postId = $search['post_id'];
                // Prioritize the post with this 'post_id' by ordering it first
                $obj = $obj->orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END", [$postId]);
            }
            
            // Pagination settings
            $pagination = (isset($search['perpage'])) ? $search['perpage'] : config('constant.pagination');
            
            // Filter by title
            if (isset($search['title']) && !empty($search['title'])) {
                $obj = $obj->where('title', 'like', '%' . trim($search['title']) . '%');
            }
    
            // Filter by status
            if (isset($search['status']) && $search['status'] != '') {
                $obj = $obj->where('status', $search['status']);
            }
    
            // Filter by date range
            if ((isset($search['from_date']) && $search['from_date'] != '') && 
                (isset($search['to_date']) && $search['to_date'] != '')) {
                $obj = $obj->whereBetween('schedule_date', [$search['from_date'], $search['to_date']]);
            } else if (isset($search['from_date']) && $search['from_date'] != '') {
                $obj = $obj->where('schedule_date', '>=', $search['from_date']);
            } else if (isset($search['to_date']) && $search['to_date'] != '') {
                $obj = $obj->where('schedule_date', '<=', $search['to_date']);
            }
    
            // Apply the default sorting by schedule_date after the post_id prioritization
            $data = $obj->orderBy('schedule_date', 'DESC')->paginate($pagination)->appends('perpage', $pagination);
            
            return $this->sendResponse($data, __('lang.message_comment_deleted_successfully'));
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()];
        }
    }


    function viewAllPost(Request $request)
    {
        try {
            $pagination_no = isset($request->count) ? $request->count : 10;
    
            if (isset($search['per_page']) && !empty($search['per_page'])) {
                $pagination_no = $search['per_page'];
            }
    
            $blogs = Blog::select(
                    'id', 'type', 'title', 'description', 'source_name', 'source_link',
                    'video_url', 'is_voting_enable', 'schedule_date', 'created_at', 'updated_at',
                    'background_image', 'is_featured'
                )
                ->where('status', 1)
                ->where('schedule_date', '<=', now())
                ->with(['blog_category.category', 'blog_sub_category.category']);
                
                
                if (isset($request->type) && $request->type == 'post') {
                    $blogs = $blogs->whereHas('questions');
                }else {
                    $blogs = $blogs->where('type', 'quote');
                }
                
                $blogs = $blogs->orderBy('schedule_date', 'DESC')
                    ->paginate($pagination_no)
                    ->appends(['per_page' => $pagination_no]);
    
            if (count($blogs)) {
                foreach ($blogs as $blog) {
                    // Translation
                    $blogTranslate = BlogTranslation::where('blog_id', $blog->id)
                        ->where('language_code', $this->language)
                        ->first();
                    if ($blogTranslate) {
                        $blog->title = $blogTranslate->title;
                        $blog->description = $blogTranslate->description;
                    }
    
                    // Defaults
                    $blog->voice = setting('blog_voice');
                    $blog->accent_code = setting('blog_accent');
                    $blog->is_feed = false;
                    $blog->is_vote = 0;
                    $blog->is_bookmark = 0;
                    $blog->is_user_viewed = 0;
    
                    if ($request->header('api-token') != '') {
                        $user = User::where('api_token', $request->header('api-token'))->first();
                        if ($user) {
                            $blog->is_feed = \Helpers::categoryIsInFeed($blog->id, $user->id);
                            $blog->is_vote = \Helpers::getVotes($blog->id, $user->id);
                            $blog->is_bookmark = \Helpers::getBookmarks($blog->id, $user->id);
                            $blog->is_user_viewed = \Helpers::getViewed($blog->id, $user->id);
                        }
                    }
    
                    // Helpers
                    $blog->visibilities = \Helpers::getVisibilities($blog->id);
                    $blog->question = \Helpers::getQuestionsOptions($blog->id);
                    $blog->images = \Helpers::getBlogImages($blog->id, '768x428');
    
                    // Background Image
                    if ($blog->background_image != '') {
                        $blog->background_image = url('uploads/blog/' . $blog->background_image);
                    }
    
                    // Add category_id, name and image
                    $category = null;
                    if (count($blog->blog_category)) {
                        foreach ($blog->blog_category as $cat) {
                            if ($cat->category) {
                                $category = $cat->category;
                                $translate = CategoryTranslation::where('category_id', $category->id)
                                    ->where('language_code', $this->language)
                                    ->first();
                                if ($translate) {
                                    $category->name = $translate->name;
                                }
                                if ($category->image != '') {
                                    $category->image = url('uploads/category/' . $category->image);
                                }
                                break; // Use the first category
                            }
                        }
                    }
    
                    // Attach category info
                    $blog->category_id = $category->id ?? null;
                    $blog->category_name = $category->name ?? null;
                    $blog->category_image = $category->image ?? null;
                    $blog->category_color = $category->color ?? null;
    
                    // Format sub-categories
                    if (count($blog->blog_sub_category)) {
                        foreach ($blog->blog_sub_category as $subCat) {
                            if ($subCat->category) {
                                $translate = CategoryTranslation::where('category_id', $subCat->category->id)
                                    ->where('language_code', $this->language)
                                    ->first();
                                if ($translate) {
                                    $subCat->category->name = $translate->name;
                                }
                                if ($subCat->category->image != '') {
                                    $subCat->category->image = url('uploads/category/' . $subCat->category->image);
                                }
                            }
                        }
                    }
                }
            }
    
            return $this->sendResponse($blogs, __('lang.message_data_retrived_successfully'));
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage());
        }
    }

}
