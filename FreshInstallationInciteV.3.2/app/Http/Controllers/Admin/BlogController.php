<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Visibility;
use App\Models\Language;
use App\Models\BlogImage;
use App\Models\BlogCategory;
use App\Models\UserFeed;
use App\Models\DeviceToken;
use App\Models\BlogBookmark;
use App\Models\LanguageCode;
use App\Models\BlogAnalytic;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Http\Requests\Blog\UpdateBlogTranslationRequest;
use App\Http\Requests\Blog\StoreQuoteRequest;
use App\Http\Requests\Blog\UpdateQuoteRequest;
use App\Http\Requests\Blog\UpdateQuoteTranslationRequest;
use Illuminate\Support\Facades\Session;
use Kyslik\ColumnSortable\Sortable;
use DB;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class BlogController extends Controller
{

    /**
     * Display a listing of the blog.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        Session::regenerate();   
        $data['result'] = Blog::getLists($request->all());
            $data['category'] = Category::where('status',1)->where('parent_id',0)->get();
            $data['visibility'] = Visibility::where('status',1)->get();
            return view('admin.blog.index',$data);
    }

    /**
     * Show the form for creating a new blog.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        try {
                     
            $data['images'] = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
            $data['categories'] = Category::where('parent_id',0)->orderBy('name','ASC')->get();
            $data['visibility'] = Visibility::latest('created_at')->get();
            return view('admin.blog.create_'.$type.'',$data);
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       
            $added = Blog::addUpdate($request->all());
            if($added['status']==true){
                return redirect('admin/post')->with('success', $added['message']); 
            }
            else{
                return redirect()->back()->with('error', $added['message']);
            } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeQuote(StoreQuoteRequest $request)
    {        
        try{
            $validated = $request->validated();
            
            $added = Blog::addUpdateQuote($request->all());
            if($added['status']==true){
                return redirect('admin/post')->with('success', $added['message']); 
            }
            else{
                return redirect()->back()->with('error', $added['message']);
            }
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  type $type, id  $id 
     * @return \Illuminate\Http\Response
     */
    public function edit($type,$id)
    {        
        try {
            $data['images'] = BlogImage::where('blog_id',$id)->orderBy('order','ASC')->get();
            $data['categories'] = Category::where('parent_id',0)->orderBy('name','ASC')->get();
            $data['subcategory'] = array();
            $data['visibility'] = Visibility::latest('created_at')->get();
            $data['voice_accent'] = config('constant.voice_accent');
            $data['speech_voice'] = config('constant.speech_voice');
            $data['row'] = Blog::getDetail($id);
            if($data['row']!=''){
                if(isset($data['row']->categoryArr) && count($data['row']->categoryArr)){
                    $data['subcategory'] = Category::whereIn('parent_id',$data['row']->categoryArr)->get();
                }
            }
            return view('admin.blog.edit_'.$type.'',$data);
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{

            $updated = Blog::addUpdate($request->all(),$request->input('id'));
            if($updated['status']==true){
                return redirect('admin/post')->with('success', $updated['message']); 
            }
            else{
                return redirect()->back()->with('error', $updated['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateQuote(UpdateQuoteRequest $request)
    {        
        try{
            $validated = $request->validated();
            $updated = Blog::addUpdateQuote($request->all(),$request->input('id'));
            if($updated['status']==true){
                return redirect('admin/post')->with('success', $updated['message']); 
            }
            else{
                return redirect()->back()->with('error', $updated['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Remove the specified resource from Blog.
     * @param  Request $request
     * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        try{
            $deleted = Blog::deleteRecord($id);
            if($deleted['status']==true){
                return redirect()->back()->with('success', $deleted['message']); 
            }
            else{
                return redirect()->back()->with('error', $deleted['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Remove the specified category from storage.
     *
     * @param  id  $id
     * @return \Illuminate\Http\Response
    **/
    public function changeStatus($id,$status)
    {
        try{
            $updated = Blog::changeStatus($status,$id);
            if($updated['status']){
                return redirect()->back()->with('success', $updated['message']); 
            }
            else{
                return redirect()->back()->with('error', $updated['message']);
            } 
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Get translations of specified category from storage.
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
    **/
    public function translation($id)
    {
        try{
            $data['detail'] = Blog::getDetail($id);
            $data['languages'] = Blog::getTranslation($id);
            return view('admin/blog.translation',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Update the translation of specified category in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogTranslationRequest  $request
     * @param  id  $id
     * @return \Illuminate\Http\Response
    **/
    public function updateTranslation(UpdateBlogTranslationRequest $request,$id)
    {
        $validated = $request->validated();
        $translationUpdated = Blog::updateTranslation($request->all(),$id);
        if($translationUpdated['status']==true){
            return redirect('admin/post')->with('success', $translationUpdated['message']); 
        }
        else{
            return redirect()->back()->with('error', $translationUpdated['message']);
        } 
    }
    /**
     * Update the translation of specified category in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
    **/
    public function getSubcategories(Request $request)
    {
        $post = $request->all();
        $subcategory = Category::whereIn('parent_id',$post['category_id'])->get();
        $sendArr = array(
            'subcategory'=>$subcategory,
        );
        if(isset($post['sub_category_id']) && count($post['sub_category_id'])){
            $sendArr['sub_cat_id'] = $post['sub_category_id']; 
        }

        $data['html'] = view('admin.blog.partials.subcategory')->with($sendArr)->render();
        $response = [
            'status' => true,
            'message' => __('lang.message_data_retrived_successfully'),
            'data' => $data
        ];
        return response($response);
    }

    public function storeImage(Request $request)
    {   
        $files = $request->file('file'); 
        $uploadImage = \Helpers::uploadFilesAfterResizeCompressOriginalName($files,'blog');
        
        if($uploadImage['status']==true){
            $postArr = array(
                'session_id'=>Session::get('session_id'),
                'image'=>$uploadImage['file_name'],
                'created_at'=>date('Y-m-d H:i:s')
            );
            if(isset($request->blog_id) && $request->blog_id!=0){
                if($request->button_val == 'replace'){
                    $countOfImage = BlogImage::where('blog_id',$request->blog_id)->count();
                    if($countOfImage == 1){
                        BlogImage::where('blog_id',$request->blog_id)->delete();
                    }
                }
                $postArr['blog_id'] = $request->blog_id;
            }else{
                $postArr['session_id'] = Session::get('session_id');
            }
            $image = BlogImage::insertGetId($postArr);
        } 
        if(isset($request->blog_id) && $request->blog_id!=0){
            $blog_images = BlogImage::where('blog_id',$request->blog_id)->orderBy('order','ASC')->get();
        }else{
            $blog_images = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
        }
        $data['uploadImage'] = $uploadImage;
        $data['session_id'] = Session::get('session_id');
        $data['blog_images'] = $blog_images;
        $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
        $response = $this->sendResponse($data,__('lang.message_data_retrived_successfully'));
        return $response;
    }

    public function removeImage(Request $request)
    {     
        $files = $request->all();
        $item_id = 0;
        $image = BlogImage::where('id',$request->image_id)->first();
        if($image!=''){
            BlogImage::where('id',$request->image_id)->delete();
            $blog_images = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
            if($image->blog_id!=0){
                $blog_images = BlogImage::where('blog_id',$image->blog_id)->orderBy('order','ASC')->get();
            }   
            // echo json_encode($blog_images);exit;  
            $data['blog_images'] = $blog_images;
            $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
            $response = $this->sendResponse($data,__('lang.message_data_retrived_successfully'));
            return $response;            
        }
        return $this->sendError(__('lang.message_something_went_wrong'));  
    }

    public function removeImageByName(Request $request)
    {     
        $files = $request->all();
        $item_id = 0;
        if(isset($request->blog_id) && $request->blog_id!=0){
            $image = BlogImage::where('image',$request->filename)->where('blog_id',$request->blog_id)->orderBy('id','DESC')->first();
        }else{
            $image = BlogImage::where('image',$request->filename)->where('session_id',Session::get('session_id'))->orderBy('id','DESC')->first();
        }     
        if($image!=''){
            BlogImage::where('id',$image->id)->delete();
            $blog_images = BlogImage::where('session_id',Session::get('session_id'))->orderBy('order','ASC')->get();
            if($image->blog_id!=0){
                $blog_images = BlogImage::where('blog_id',$image->blog_id)->orderBy('order','ASC')->get();
            }   
            $data['blog_images'] = $blog_images;
            $data['html'] = view('admin.blog.partials.image_preview')->with(array('images'=>$blog_images))->render();
            $response = $this->sendResponse($data,__('lang.message_data_retrived_successfully'));
            return $response;            
        }
        return $this->sendError(__('lang.message_something_went_wrong'));  
    }

    /**
     * Update order storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sorting(Request $request)
    {
        try{
            $input = $request->all();
            if(isset($input['blog_id']) && $input['blog_id']!=0){
                $posts = BlogImage::where('blog_id',$input['blog_id'])->get();
            }else{
                $posts = BlogImage::where('session_id',Session::get('session_id'))->get();
            }          
            foreach ($posts as $post) {
                foreach ($request->order as $order) {
                    if ($order['id'] == $post->id) {
                        $c = BlogImage::where('id',$post->id)->update(['order' => $order['position']]);                        
                    }
                }
            }
            $response = [
                'status' => true,
                'message' => __('lang.message_data_retrived_successfully'),
                'data' => []
            ];
            return response($response);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Send Notification to users.
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
    **/
    public function sendNotification(Request $request)
    { 
        $responseArr = [];
        $id = $request->id;
        if(setting('enable_os_notifications') == 1){
           if(setting('one_signal_key')==''){
            return response()->json([
                'statusCode' => 422,
                'type' => 'error',
                'message' => __('lang.message_one_signal_key_not_found')
            ], 422);

            }else{
                $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','voice', 'accent_code','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image')->where('id',$id)->where('status',1)->with('blog_category')->with('blog_sub_category')->first();
                $image = url('uploads/setting/'.setting('app_logo'));
                if($blog){
                    $blog->images = \Helpers::getBlogImages($blog->id,'327x250');
                    if($blog->type=='post'){
                        if(count($blog->images)){
                            $image = $blog->images[0];
                        }
                    }else{
                        $image = url('uploads/blog/'.$blog->background_image);
                    }                
                    if($blog->background_image!=''){
                        $blog->background_image = url('uploads/blog/'.$blog->background_image);
                    }
                }
                $player_id = array();
                if($request->recipient == 'preferred_users'){
                $blogCatIds = BlogCategory::where('blog_id',$id)->pluck('category_id')->toArray();
                $userIds =  UserFeed::whereIn('category_id',$blogCatIds)->pluck('user_id')->toArray();
                $token = DeviceToken::select('player_id', DB::raw('MAX(id) as max_id,is_notification_enabled'))->where('is_notification_enabled',1)->where('player_id','!=',null)->whereIn('user_id',$userIds)->groupBy('player_id')->orderBy('max_id', 'DESC')->get();
                }else{
                $token = DeviceToken::select('player_id', DB::raw('MAX(id) as max_id,is_notification_enabled'))->where('is_notification_enabled',1)->where('player_id','!=',null)->groupBy('player_id')->orderBy('max_id', 'DESC')->get();    
                }
                
                if(count($token) > 0){
                    foreach($token as $detail){
                        if($detail->player_id!='' || $detail->player_id!=null || $detail->player_id!="null"){
                                if(!in_array($detail->player_id,$player_id)){
                                    array_push($player_id,$detail->player_id);
                                }
                        }                   
                    }
                }else{
                    return response()->json([
                        'statusCode' => 422,
                        'type' => 'error',
                        'message' => __('lang.message_no_device_token_found')
                    ], 422);
                }
                $status = \Helpers::sendOneSignalNotification($blog->title,$blog->description,$image,$blog,$player_id);
                if ($status === 200) {
                    return response()->json([
                        'statusCode' => 200,
                        'type' => 'success',
                        'message' => __('lang.message_notification_sent_successfully')
                    ], 200);
                } else {
                    return response()->json([
                        'statusCode' => 422,
                        'type' => 'error',
                        'message' => __('lang.message_error_while_sending')
                    ], 422);
                }   
            }
        }else if(setting('enable_firebase_notifications') == 1){
            if(setting('fcm_project_id')==''){
            return response()->json([
                'statusCode' => 422,
                'type' => 'error',
                'message' => __('lang.message_fcm_project_id_not_found')
            ], 422);
            }else{
                $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','voice', 'accent_code','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image')->where('id',$id)->where('status',1)->with('blog_category')->with('blog_sub_category')->first();
                $image = url('uploads/setting/'.setting('app_logo'));
                if($blog){
                    $blog->images = \Helpers::getBlogImages($blog->id,'327x250');
                    if($blog->type=='post'){
                        if(count($blog->images)){
                            $image = $blog->images[0];
                        }
                    }else{
                        $image = url('uploads/blog/'.$blog->background_image);
                    }                
                    if($blog->background_image!=''){
                        $blog->background_image = url('uploads/blog/'.$blog->background_image);
                    }
                }
                $fcm_token = array();
                if($request->recipient == 'preferred_users'){
                $blogCatIds = BlogCategory::where('blog_id',$id)->pluck('category_id')->toArray();
                $userIds =  UserFeed::whereIn('category_id',$blogCatIds)->pluck('user_id')->toArray();
                $token = DeviceToken::select('fcm_token', DB::raw('MAX(id) as max_id,is_notification_enabled'))->where('is_notification_enabled',1)->where('fcm_token','!=',null)->whereIn('user_id',$userIds)->groupBy('fcm_token')->orderBy('max_id', 'DESC')->get();
                }else{
                $token = DeviceToken::select('fcm_token', DB::raw('MAX(id) as max_id,is_notification_enabled'))->where('is_notification_enabled',1)->where('fcm_token','!=',null)->groupBy('fcm_token')->orderBy('max_id', 'DESC')->get();    
                }
                
                if(count($token) > 0){
                    foreach($token as $detail){
                        if($detail->fcm_token!='' || $detail->fcm_token!=null || $detail->fcm_token!="null"){
                                if(!in_array($detail->fcm_token,$fcm_token)){
                                    array_push($fcm_token,$detail->fcm_token);
                                }
                        }                   
                    }
                }else{
                    return response()->json([
                        'statusCode' => 422,
                        'type' => 'error',
                        'message' => __('lang.message_no_device_token_found')
                    ], 422);
                }

                $status = \Helpers::sendFcmNotification($blog->title,$blog->description,$image,$blog,$fcm_token);
                if ($status === 200) {
                    return response()->json([
                        'statusCode' => 200,
                        'type' => 'success',
                        'message' => __('lang.message_notification_sent_successfully')
                    ], 200);
                } else {
                    return response()->json([
                        'statusCode' => 422,
                        'type' => 'error',
                        'message' => __('lang.message_error_while_sending')
                    ], 422);
                }   
            }
        }else{
            return response()->json([
                'statusCode' => 422,
                'type' => 'error',
                'message' => __('lang.please_enable_notification_toggle_first')
            ], 422);
        }    
    }

    /**
     * Display a listing of the blog.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function analytics(Request $request,$id)
    {
        try{
            $pagination = (isset($search['perpage']))?$search['perpage']:config('constant.pagination');
            
            $data['blog_polls'] = BlogAnalytic::where('type','blog_poll_option')->where('blog_id',$id)->with('user')->groupBy('blog_id')->paginate($pagination)->appends('perpage', $pagination);
            
            
            $data['blog_pollsCount'] = BlogAnalytic::where('type','blog_poll_option')->where('blog_id',$id)->with('user')->groupBy('blog_id')->count();
            
            
            return view('admin.blog.analytics',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Send Notification from storage.
     *
     * @param  id $id
     * @return \Illuminate\Http\Response
    **/
    public function sendNotificationCron()
    {   
        if(setting('enable_os_notifications') == 1){
            if(setting('one_signal_key')==''){
            return redirect()->back()->with('error',__('lang.message_one_signal_key_not_found'));
            }else{
                $player_id = array();
                $token = DeviceToken::select('player_id', DB::raw('MAX(id) as max_id,is_notification_enabled'))->where('player_id','!=',null)->groupBy('player_id')->orderBy('max_id', 'DESC')->get();
                if(count($token)){
                    foreach($token as $detail){
                        if($detail->player_id!='' || $detail->player_id!=null || $detail->player_id!="null"){
                            if($detail->is_notification_enabled==1){
                                if(!in_array($detail->player_id,$player_id)){
                                    array_push($player_id,$detail->player_id);
                                }
                            }
                        }                   
                    }
                }
                $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','voice', 'accent_code','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image')->where('schedule_date',date('Y-m-d H:i:00'))->where('status',2)->with('blog_category')->with('blog_sub_category')->get();
                $image = url('uploads/setting/'.setting('app_logo'));
                if(count($blog)){
                    foreach($blog as $row){
                        $row->images = \Helpers::getBlogImages($row->id,'327x250');
                        if($row->type=='post'){
                            if(count($row->images)){
                                $image = $row->images[0];
                            }
                        }else{
                            $image = url('uploads/blog/'.$row->background_image);
                        }                
                        if($row->background_image!=''){
                            $row->background_image = url('uploads/blog/'.$row->background_image);
                        }
                        $status = \Helpers::sendOneSignalNotification($row->title,$row->description,$image,$row,$player_id);
                        if ($status === 200) {
                            Blog::where('id',$row->id)->update(['status'=>1]);
                        }
                    }
        }
            }
        }else if(setting('enable_firebase_notifications') == 1){
            if(setting('fcm_project_id')==''){
            return redirect()->back()->with('error',__('lang.message_fcm_project_id_not_found'));
            }else{
                $fcm_token = array();
                $token = DeviceToken::select('fcm_token', DB::raw('MAX(id) as max_id,is_notification_enabled'))->where('fcm_token','!=',null)->groupBy('fcm_token')->orderBy('max_id', 'DESC')->get();
                if(count($token)){
                    foreach($token as $detail){
                        if($detail->fcm_token!='' || $detail->fcm_token!=null || $detail->fcm_token!="null"){
                            if($detail->is_notification_enabled==1){
                                if(!in_array($detail->fcm_token,$fcm_token)){
                                    array_push($fcm_token,$detail->fcm_token);
                                }
                            }
                        }                   
                    }
                }
                $blog = Blog::select('id', 'type', 'title','description', 'source_name', 'source_link','voice', 'accent_code','video_url', 'is_voting_enable', 'schedule_date','created_at', 'updated_at', 'background_image')->where('schedule_date',date('Y-m-d H:i:00'))->where('status',2)->with('blog_category')->with('blog_sub_category')->get();
                $image = url('uploads/setting/'.setting('app_logo'));
                if(count($blog)){
                    foreach($blog as $row){
                        $row->images = \Helpers::getBlogImages($row->id,'327x250');
                        if($row->type=='post'){
                            if(count($row->images)){
                                $image = $row->images[0];
                            }
                        }else{
                            $image = url('uploads/blog/'.$row->background_image);
                        }                
                        if($row->background_image!=''){
                            $row->background_image = url('uploads/blog/'.$row->background_image);
                        }
                        $status = \Helpers::sendFcmNotification($row->title,$row->description,$image,$row,$fcm_token);
                        if ($status === 200) {
                            Blog::where('id',$row->id)->update(['status'=>1]);
                        }
                    }
                }
            }
        }else{
            return redirect()->back()->with('error', __('lang.please_enable_notification_toggle_first'));
        }
        return __('lang.message_notification_sent_successfully');      
    }


    public function deleteSelected(Request $request)
    {
        $selectedIdsString = $request->input('selectedIds');
        
        $selectedIds = explode(',', $selectedIdsString);

        // Delete
        Blog::whereIn('id', $selectedIds)->delete();

        return redirect()->back()->with('success', __('lang.message_bulk_blog_delete_successfully'));
    }


    public function changeStatusViaList(Request $request)
    {
        $blog = Blog::findOrFail($request->id);

        // Schedule → no action
        if ($blog->status == 4) {
            return response()->json([
                'status' => false,
                'message' => __('lang.admin_scheduled')
            ]);
        }

        switch ($blog->status) {
            case 2: // draft → submit
                $blog->status = 3;
                break;

            case 3: // submit → publish
                $blog->status = 1;
                break;

            case 1: // publish → unpublish
                $blog->status = 0;
                break;

            case 0: // unpublish → publish
                $blog->status = 1;
                break;
        }

        $blog->save();

        return response()->json([
            'status' => true,
            'message' => __('lang.admin_status_updated')
        ]);
    }



}
