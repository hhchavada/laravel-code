<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use anlutro\LaravelSettings\Facade as ContentSetting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $tempArr = array(
            'homepage_theme' => 'home_2',
            'layout' => 'index_1',
            'site_name' => 'Incite',
            'site_admin_name'=>'Admin Incite',
            'site_phone' => '+91-7987162771',
            'from_email' => 'info@gmail.com',
            'footer_about' => 'The tag defines a footer for a document or section. A element typically contains: authorship information. copyright information. contact information',
            'powered_by' => 'All Rights Reserved Powered by Incite.',
            'site_seo_title' => 'Incite Flutter App with Admin Panel - Inshort Clone',
            'site_seo_description' => 'Yönetici Paneli ile Flutter Uygulamasını Teşvik Et - Inshort Clone',
            'site_seo_keyword' => 'Flutter Uygulamasını Teşvik Et - Inshort Clone',
            'site_seo_tag' => 'Flutter, Laravel, Web Uygulaması, Mobil Uygulama',
            'preferred_site_language' => 'en',
            'news_api_key' => '',
            'mediastack_api_key' => '',
            'chat_gpt_api_key'=>'',
            'google_translation_api_key'=>'',
            'google_analytics_code' => '',
            'site_logo' => '',
            'website_admin_logo' => '',
            'site_favicon'=>'',
            'app_name' => 'Incite',
            'support_email' => 'support@gmail.com',
            'is_short_video_enable' => 0,
            'bundle_id_android'=>'',
            'bundle_id_ios'=>'',
            'signing_key_android' => '',
            'key_property_android'=>'',            
            'is_android_app_force_update'=> 0,
            'is_ios_app_force_update' => 0,
            'primary_color'=>'',
            'secondary_color'=>'',         
            'app_logo' => '',
            'rectangualr_app_logo'=>'',
            'app_splash_screen' => '',
            'site_title' => 'Incite',    
            'enable_os_notifications' => 0,
            'one_signal_key'=>'',
            'one_signal_app_id'=>'',
            'enable_firebase_notifications' => 0,
            'fcm_project_id' => '',
            'mailer' => '',
            'host' => '',
            'port' => '',
            'username'=>'',
            'password' => '',
            'encryption'=>'',
            'bundle_id_ios'=>'',
            'from_name'=>'',         
            'enable_maintainance_mode'=>0,
            'maintainance_title'=>'Comming Soon!',
            'maintainance_short_text'=>'We are preparing something better & amazing for you.' ,             
            'push_notification_enabled' =>0,
            'date_format' => 'd-m-Y h:i A',
            'timezone' => 'Asia/Kolkata',
            'blog_language'=>'',
            'blog_accent'=>'',
            'blog_voice'=>'',
            'blog_accent_code' => 'uk-UA',            
            'google_api_key'=>'',
            'voice_type'=>'',
            'is_voice_enabled'=>0,
            'live_news_logo' => '',
            'live_news_status' => 0,
            'e_paper_logo' => '',
            'e_paper_status' => 0,            
            'enable_ads' => 0,
            'admob_banner_id_android' => '',
            'admob_interstitial_id_android' => '',
            'admob_banner_id_ios' => '',
            'admob_interstitial_id_ios' => '',
            'admob_frequency' => 0,
            'enable_fb_ads' => 0,
            'fb_ads_placement_id_android' => '',
            'fb_ads_placement_id_ios' => '',
            'fb_ads_app_token' => '',
            'fb_ads_frequency' => 0,
            'fb_ads_interstitial_id_android' => '',
            'fb_ads_interstitial_id_ios' => '',
            'enable_share_setting' => '0',
            'android_schema'=>'',
            'playstore_url'=>'',
            'ios_schema'=>'',
            'appstore_url'=>'',
            'website_updates' => false,
            'meta_title' => '',
            'meta_description' => '',
            'meta_tag' => '',
            'home_banner' => '',
            'enable_send_push_notification_on_post_published' => '0',
            'category_icon_shape' => 'circle',
            'category_icon_column' => '4',
            'ads_in_shorts' => 'no',
            'enable_unity_ads' => 0,
            'unity_ads_frequency' => '',
            'unity_ads_banner_id_android' => '',
            'unity_ads_banner_id_ios' => '',
            'unity_android_game_id' => '',
            'unity_ios_game_id' => '',
            'unity_placement_android_id' => '',
            'unity_placement_ios_id' => '',
        );
         
        foreach ($tempArr as $key => $value) {
            $check = Setting::where('key',$key)->first();
            if(!$check){
                Setting::insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }
        $settingsc = Setting::all();
        foreach ($settingsc as $row) {
            ContentSetting::set($row->key, $row->value);
        }
        ContentSetting::save();
    }
}