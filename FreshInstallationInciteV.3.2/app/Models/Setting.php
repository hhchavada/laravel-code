<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use anlutro\LaravelSettings\Facade as ContentSetting;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class Setting extends Model
{
    use HasFactory;
    protected $table = "settings";

    /**
     * update
    **/
    public static function updateContent($data)
    {        
        try 
        {
            $obj = new self;
            unset($data['_token']);
            $page_name = $data['page_name'];
            unset($data['page_name']);
            
            if($page_name=='push-notification-setting'){
                if (isset($data['enable_os_notifications'])) {
                    if($data['enable_os_notifications']=='on'){
                    $data['enable_os_notifications'] = 1;
                    }else{
                    $data['enable_os_notifications'] = $data['enable_os_notifications'];
                    }
                }else{
                    $data['enable_os_notifications'] = 0;
                }

                if (isset($data['enable_firebase_notifications'])) {
                    if($data['enable_firebase_notifications']=='on'){
                    $data['enable_firebase_notifications'] = 1;
                    }else{
                    $data['enable_firebase_notifications'] = $data['enable_firebase_notifications'];
                    }
                }else{
                    $data['enable_firebase_notifications'] = 0;
                }
                
                
                if (isset($data['enable_send_push_notification_on_post_published'])) {
                    if($data['enable_send_push_notification_on_post_published']=='on'){
                    $data['enable_send_push_notification_on_post_published'] = 1;
                    }else{
                    $data['enable_send_push_notification_on_post_published'] = $data['enable_send_push_notification_on_post_published'];
                    }
                }else{
                    $data['enable_send_push_notification_on_post_published'] = 0;
                }

                $push_data = $obj::where('key', 'one_signal_key')->first();
                $one_signal_key = $push_data->value;
                
                // Check if the last 5 characters match
                if (substr($one_signal_key, -5) == substr($data['one_signal_key'], -5)) {
                    $data['one_signal_key'] = $one_signal_key;
                }
                $push_data = $obj::where('key', 'one_signal_app_id')->first();
                $one_signal_app_id = $push_data->value;
                
                // Check if the last 5 characters match
                if (substr($one_signal_app_id, -5) == substr($data['one_signal_app_id'], -5)) {
                    $data['one_signal_app_id'] = $one_signal_app_id;
                }

                if(!empty($data['firebase_json_file'])) {
                    $request = new \Illuminate\Http\Request($data);
                    self::save_firebase_setting($request);
                }
                
            }
            if($page_name=='admob-setting'){
                if (isset($data['enable_ads'])) {
                    if($data['enable_ads']=='on'){
                    $data['enable_ads'] = 1;
                    }else{
                    $data['enable_ads'] = $data['enable_ads'];
                    }
                }else{
                    $data['enable_ads'] = 0;
                }
            }
            if($page_name=='fb-ads-setting'){
                if (isset($data['enable_fb_ads'])) {
                    if($data['enable_fb_ads']=='on'){
                    $data['enable_fb_ads'] = 1;
                    }else{
                    $data['enable_fb_ads'] = $data['enable_fb_ads'];
                    }
                }else{
                    $data['enable_fb_ads'] = 0;
                }
            }
            if($page_name=='unity-ads-setting'){
                if (isset($data['enable_unity_ads'])) {
                    if($data['enable_unity_ads']=='on'){
                    $data['enable_unity_ads'] = 1;
                    }else{
                    $data['enable_unity_ads'] = $data['enable_unity_ads'];
                    }
                }else{
                    $data['enable_unity_ads'] = 0;
                }
            }
            if($page_name=='share-setting'){
                if (isset($data['enable_share_setting'])) {
                    if($data['enable_share_setting']=='on'){
                    $data['enable_share_setting'] = 1;
                    }else{
                    $data['enable_share_setting'] = $data['enable_share_setting'];
                    }
                }else{
                    $data['enable_share_setting'] = 0;
                }
            }
            if($page_name=='app-update-setting'){
                if (isset($data['is_android_app_force_update'])) {
                    if($data['is_android_app_force_update']=='on'){
                    $data['is_android_app_force_update'] = 1;
                    }else{
                    $data['is_android_app_force_update'] = $data['is_android_app_force_update'];
                    }
                }else{
                    $data['is_android_app_force_update'] = 0;
                }
                
                if (isset($data['is_ios_app_force_update'])) {
                    if($data['is_ios_app_force_update']=='on'){
                    $data['is_ios_app_force_update'] = 1;
                    }else{
                    $data['is_ios_app_force_update'] = $data['is_ios_app_force_update'];
                    }
                }else{
                    $data['is_ios_app_force_update'] = 0;
                }
            }
            if($page_name=='app-setting'){
                if (isset($data['is_short_video_enable'])) {
                    if($data['is_short_video_enable']=='on'){
                    $data['is_short_video_enable'] = 1;
                    }else{
                    $data['is_short_video_enable'] = $data['is_short_video_enable'];
                    }
                }else{
                    $data['is_short_video_enable'] = 0;
                }
            }
            if($page_name=='news-setting'){
                if (isset($data['live_news_status'])) {
                    if($data['live_news_status']=='on'){
                    $data['live_news_status'] = 1;
                    }else{
                    $data['live_news_status'] = $data['live_news_status'];
                    }
                }else{
                    $data['live_news_status'] = 0;
                }
                if (isset($data['e_paper_status'])) {
                    if($data['e_paper_status']=='on'){
                    $data['e_paper_status'] = 1;
                    }else{
                    $data['e_paper_status'] = $data['e_paper_status'];
                    }
                }else{
                    $data['e_paper_status'] = 0;
                }
            }
            if($page_name=='global-setting'){
                if (isset($data['is_voice_enabled'])) {
                    if($data['is_voice_enabled']=='on'){
                    $data['is_voice_enabled'] = 1;
                    }else{
                    $data['is_voice_enabled'] = $data['is_voice_enabled'];
                    }
                }else{
                    $data['is_voice_enabled'] = 0;
                }
            }
            if($page_name=='maintainance-setting'){
                if (isset($data['enable_maintainance_mode'])) {
                    if($data['enable_maintainance_mode']=='on'){
                        $data['enable_maintainance_mode'] = 1;
                    }else{
                        $data['enable_maintainance_mode'] = $data['enable_maintainance_mode'];
                    }
                }else{
                    $data['enable_maintainance_mode'] = 0;
                }
            }
            if($page_name=='global-blog-setting'){
                if (isset($data['is_voice_enabled'])) {
                    if($data['is_voice_enabled']=='on'){
                        $data['is_voice_enabled'] = 1;
                    }else{
                        $data['is_voice_enabled'] = $data['is_voice_enabled'];
                    }
                }else{
                    $data['is_voice_enabled'] = 0;
                }
            }
            if($page_name=='home-setting'){
               if(isset($data['home_banner']) && $data['home_banner']!=''){
                    $uploadImage = \Helpers::uploadFiles($data['home_banner'],'setting/');
                    if($uploadImage['status']==true){
                        $data['home_banner'] = $uploadImage['file_name'];
                    }
                } 
            }
            if(isset($data['site_logo']) && $data['site_logo']!=''){
                $uploadImage = \Helpers::uploadFiles($data['site_logo'],'setting/');
                if($uploadImage['status']==true){
                    $data['site_logo'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['website_admin_logo']) && $data['website_admin_logo']!=''){
                $uploadImage = \Helpers::uploadFiles($data['website_admin_logo'],'setting/');
                if($uploadImage['status']==true){
                    $data['website_admin_logo'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['site_favicon']) && $data['site_favicon']!=''){
                $uploadImage = \Helpers::uploadFiles($data['site_favicon'],'setting/');
                if($uploadImage['status']==true){
                    $data['site_favicon'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['app_logo']) && $data['app_logo']!=''){
                $uploadImage = \Helpers::uploadFiles($data['app_logo'],'setting/');
                if($uploadImage['status']==true){
                    $data['app_logo'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['app_splash_screen']) && $data['app_splash_screen']!=''){
                $uploadImage = \Helpers::uploadFiles($data['app_splash_screen'],'setting/');
                if($uploadImage['status']==true){
                    $data['app_splash_screen'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['live_news_logo']) && $data['live_news_logo']!=''){
                $uploadImage = \Helpers::uploadFiles($data['live_news_logo'],'setting/');
                if($uploadImage['status']==true){
                    $data['live_news_logo'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['e_paper_logo']) && $data['e_paper_logo']!=''){
                $uploadImage = \Helpers::uploadFiles($data['e_paper_logo'],'setting/');
                if($uploadImage['status']==true){
                    $data['e_paper_logo'] = $uploadImage['file_name'];
                }
            }
            if(isset($data['rectangualr_app_logo']) && $data['rectangualr_app_logo']!=''){
                $uploadImage = \Helpers::uploadFiles($data['rectangualr_app_logo'],'setting/');
                if($uploadImage['status']==true){
                    $data['rectangualr_app_logo'] = $uploadImage['file_name'];
                }
            }
            foreach ($data as $key => $value) {
                
                $exist = $obj->where('key',$key)->first();
                if ($exist) {
                    $id = $obj->where('id',$exist->id)->update(array('value'=>$value));
                }else{
                    $obj->insert(array('key'=>$key,'value'=>$value));
                }
            }

            $settingsc = $obj->all();
            foreach ($settingsc as $row) {
                ContentSetting::set($row->key, $row->value);
            }
            ContentSetting::save();
            $envFilePath = base_path('.env');
            $replacementPairs = array();
            if(isset($data['google_client_id']) && $data['google_client_id']!=''){
                $replacementPairs['GOOGLE_CLIENT_ID'] = $data['google_client_id'];
            }
            if(isset($data['google_client_secret']) && $data['google_client_secret']!=''){
                $replacementPairs['GOOGLE_CLIENT_SECRET'] = $data['google_client_secret'];
            }
            if(isset($data['mailer']) && $data['mailer']!=''){
                $replacementPairs['MAIL_MAILER'] = $data['mailer'];
            }
            if(isset($data['host']) && $data['host']!=''){
                $replacementPairs['MAIL_HOST'] = $data['host'];
            }
            if(isset($data['port']) && $data['port']!=''){
                $replacementPairs['MAIL_PORT'] = $data['port'];
            }
            if(isset($data['username']) && $data['username']!=''){
                $replacementPairs['MAIL_USERNAME'] = $data['username'];
            }
            if(isset($data['password']) && $data['password']!=''){
                $replacementPairs['MAIL_PASSWORD'] = $data['password'];
            }
            if(isset($data['encryption']) && $data['encryption']!=''){
                $replacementPairs['MAIL_ENCRYPTION'] = $data['encryption'];
            }
            if(isset($data['from_name']) && $data['from_name']!=''){
                $replacementPairs['MAIL_FROM_NAME'] = $data['from_name'];
            } 
            $envContents = file_get_contents($envFilePath);
            if(count($replacementPairs)>0){
                foreach ($replacementPairs as $key => $value) {
                    $search = "{$key}=";
                    $replacement = "{$key}={$value}";
                    $envContents = preg_replace("/^{$key}=.*/m", $replacement, $envContents);
                }
                
                file_put_contents($envFilePath, $envContents);
            }        

            if (isset($post['blog_language'])) {
                Session::put('locale', $post['blog_language']);             
            }

            return ['status' => true, 'message' => __('lang.message_success_update')];
        } 
        catch (\Exception $e) 
        {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    public static function save_firebase_setting($request){
        $rules = [
            'firebase_json_file' => 'file|mimetypes:application/json,text/plain,application/octet-stream|max:2048',
        ];
        $validator = Validator::make($request->all(), $rules, []);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $file = $request['firebase_json_file'];
        if ($file) {
            $path_storage = storage_path('app/json');
            if (!is_dir($path_storage)) {
                mkdir($path_storage, 0777, true);
            }
            $file_name = 'firebase_credentials.json';
            if (file_exists($path_storage . '/' . $file_name)) {
                unlink($path_storage . '/' . $file_name);
            }
            $file->move($path_storage, $file_name);
        }
        return true;
    }
}
