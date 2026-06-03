<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Clear cache command
Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "Cache is cleared";
});

// Basic routes
Route::get('/uploads/setting', 'App\Http\Controllers\Controller@notFound');
Route::get('admin/youtube-video', 'App\Http\Controllers\Admin\ShortVideoController@getClientHttp');

// Route for Seeder call
Route::get('/arabic-seed', function() {
    Artisan::call('db:seed', ['--class' => 'TranslationsArabicSeeder']);
    return "Translations Arabic seeded successfully";
});

// Route for migrate
Route::get('/execute-queries', function() {
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    return "Migration run successfully";
});


//Route for to send pushnotification by cron
Route::get('/send-notification-cron', 'App\Http\Controllers\Admin\BlogController@sendNotificationCron');


//Route for to autopublish post by cron
Route::get('/cron/rss', 'App\Http\Controllers\CronController@runRssFeed');


Route::middleware('admin-language:web')->group(function () {    
    Route::get('/update-website','App\Http\Controllers\UpdateApplicationController@updateWebsite');
    Route::get('/admin-login','App\Http\Controllers\Auth\LoginController@getLoginView')->middleware(['check.app.installation','check.app.code_verified']);
    Route::get('/admin-forget-password','App\Http\Controllers\Auth\ForgotPasswordController@forgetPassword')->middleware(['check.app.installation','check.app.code_verified']);
    Route::get('/admin-reset-password','App\Http\Controllers\Auth\ResetPasswordController@resetPassword')->middleware(['check.app.installation','check.app.code_verified']);
    Route::post('do-login', 'App\Http\Controllers\Auth\LoginController@authenticate');
    Route::post('do-admin-forget-password', 'App\Http\Controllers\Auth\ForgotPasswordController@forgetPasswordPost');
    Route::post('do-admin-reset-password', 'App\Http\Controllers\Auth\ResetPasswordController@resetPasswordPost');
    Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout');

    Route::get('/', function () {
        return view('welcome');
    })->middleware(['check.app.installation','check.app.code_verified']);

    Route::get('/licenses-verify', 'App\Http\Controllers\LicenseController@index')->middleware('check.app.installation');

    Route::post('/licenses-verify', 'App\Http\Controllers\LicenseController@verify')->name('license.verify')->middleware('check.app.installation');

    Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'checkSubadminStatus']], 
    function () {
        Route::post('/notifications/mark-all-read','App\Http\Controllers\Admin\DashboardController@markAllAsRead')->name('notifications.markAllAsRead');
        Route::post('/notifications/remove','App\Http\Controllers\Admin\DashboardController@removeNotification')->name('notifications.remove');
        Route::get('/e-paper/download/{id}', 'App\Http\Controllers\Admin\EpaperController@downloadPdf')->name('admin.epaper.download');
    });

    Route::middleware(['permission','check.app.installation','check.app.code_verified'])->group(function () {
        Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'checkSubadminStatus']], function () {

            /************ Update Application Routing Starts Here ********************/

            Route::get('/check-update','App\Http\Controllers\UpdateApplicationController@index');

            /********** Update Application Routing Ends Here **************************/

            /************************* Dashboard Routing Starts Here **************************/

            Route::get('/dashboard','App\Http\Controllers\Admin\DashboardController@index');

            Route::get('/','App\Http\Controllers\Admin\DashboardController@index');
            Route::get('/profile', 'App\Http\Controllers\Admin\UserController@profile');
            Route::post('/update-profile', 'App\Http\Controllers\Admin\UserController@updateProfile');

            /************************* Dashboard Routing Ends Here **************************/

            /************************* Category Routing Starts Here **************************/

            Route::get('/category', 'App\Http\Controllers\Admin\CategoryController@index');
            Route::post('/category', 'App\Http\Controllers\Admin\CategoryController@index');
            Route::post('/add-category', 'App\Http\Controllers\Admin\CategoryController@store');
            Route::post('/update-category', 'App\Http\Controllers\Admin\CategoryController@update');
            Route::delete('/delete-category/{id}', 'App\Http\Controllers\Admin\CategoryController@destroy');
            Route::get('/update-category-column/{id}/{name}/{value}', 'App\Http\Controllers\Admin\CategoryController@updateColumn');
            Route::get('/translation-category/{id}', 'App\Http\Controllers\Admin\CategoryController@translation');
            Route::post('/translation-category/{id}', 'App\Http\Controllers\Admin\CategoryController@updateTranslation');
            Route::post('/translation-by-third-party', 'App\Http\Controllers\Admin\CategoryController@translateByThirdParty');
            Route::post('/translation-by-google', 'App\Http\Controllers\Admin\CategoryController@translateByGoogle');

            /************************* Category Routing Ends Here **************************/

            /************************* Quotes Routing Starts Here **************************/

            Route::get('/quotes', 'App\Http\Controllers\Admin\QuoteController@index');
            Route::post('/quotes', 'App\Http\Controllers\Admin\QuoteController@index');
            Route::post('/add-quote', 'App\Http\Controllers\Admin\QuoteController@store');
            Route::post('/update-quote', 'App\Http\Controllers\Admin\QuoteController@update');
            Route::delete('/delete-quote/{id}', 'App\Http\Controllers\Admin\QuoteController@destroy');
            Route::get('/update-quote-column/{id}/{name}/{value}', 'App\Http\Controllers\Admin\QuoteController@updateColumn');
            Route::get('/quote/translation/{id}', 'App\Http\Controllers\Admin\QuoteController@translation');
            Route::post('/quote/translation/{id}', 'App\Http\Controllers\Admin\QuoteController@updateTranslation');

            /************************* Quotes Routing Ends Here **************************/

            /************************* Live News Routing Starts Here **************************/

            Route::get('/live-news', 'App\Http\Controllers\Admin\LiveNewsController@index');
            Route::post('/live-news', 'App\Http\Controllers\Admin\LiveNewsController@index');
            Route::post('/add-live-news', 'App\Http\Controllers\Admin\LiveNewsController@store');
            Route::post('/update-live-news', 'App\Http\Controllers\Admin\LiveNewsController@update');
            Route::delete('/delete-live-news/{id}', 'App\Http\Controllers\Admin\LiveNewsController@destroy');
            Route::get('/detail-live-news/{id}', 'App\Http\Controllers\Admin\LiveNewsController@show');
            Route::get('/update-live-news-status/{id}/{value}', 'App\Http\Controllers\Admin\LiveNewsController@changeStatus');
            Route::get('/translation-live-news/{id}', 'App\Http\Controllers\Admin\LiveNewsController@translation');
            Route::post('/translation-live-news/{id}', 'App\Http\Controllers\Admin\LiveNewsController@updateTranslation');

            /************************* Live News Routing Ends Here **************************/

            /************************* E-Paper Routing Starts Here **************************/

            Route::get('/e-papers', 'App\Http\Controllers\Admin\EpaperController@index');
            Route::post('/e-papers', 'App\Http\Controllers\Admin\EpaperController@index');
            Route::post('/add-e-paper', 'App\Http\Controllers\Admin\EpaperController@store');
            Route::post('/update-e-paper', 'App\Http\Controllers\Admin\EpaperController@update');
            Route::delete('/delete-e-paper/{id}', 'App\Http\Controllers\Admin\EpaperController@destroy');
            Route::get('/detail-e-paper/{id}', 'App\Http\Controllers\Admin\EpaperController@show');
            Route::get('/update-e-paper-status/{id}/{value}', 'App\Http\Controllers\Admin\EpaperController@changeStatus');
            Route::get('/translation-e-paper/{id}', 'App\Http\Controllers\Admin\EpaperController@translation');
            Route::post('/translation-e-paper/{id}', 'App\Http\Controllers\Admin\EpaperController@updateTranslation');

            /************************* E-Paper Routing Ends Here **************************/

            /************************* CMS Routing Starts Here **************************/

            Route::get('/cms', 'App\Http\Controllers\Admin\CmsController@index');
            Route::post('/cms', 'App\Http\Controllers\Admin\CmsController@index');
            Route::get('/add-cms', 'App\Http\Controllers\Admin\CmsController@create');
            Route::post('/add-cms', 'App\Http\Controllers\Admin\CmsController@store');
            Route::get('/update-cms/{id}', 'App\Http\Controllers\Admin\CmsController@edit');
            Route::post('/update-cms', 'App\Http\Controllers\Admin\CmsController@update');
            Route::delete('/delete-cms/{id}', 'App\Http\Controllers\Admin\CmsController@destroy');
            Route::get('/detail-cms/{id}', 'App\Http\Controllers\Admin\CmsController@show');
            Route::get('/update-cms-status/{id}/{value}', 'App\Http\Controllers\Admin\CmsController@changeStatus');
            Route::get('/translation-cms/{id}', 'App\Http\Controllers\Admin\CmsController@translation');
            Route::post('/translation-cms/{id}', 'App\Http\Controllers\Admin\CmsController@updateTranslation');

            /************************* CMS Routing Ends Here **************************/

            /************************* Settings Routing Starts Here **************************/

            Route::get('/settings/{type}', 'App\Http\Controllers\Admin\SettingController@index');
            Route::post('/update-setting', 'App\Http\Controllers\Admin\SettingController@update');
            Route::get('/setlang', 'App\Http\Controllers\Admin\SettingController@setLanguage');

            /************************* Settings Routing Ends Here **************************/

            /***************** Social Media Settings Routing Starts Here *******************/

            Route::get('/social-media', 'App\Http\Controllers\Admin\SocialMediaLinkController@index');
            Route::post('/social-media', 'App\Http\Controllers\Admin\SocialMediaLinkController@index');
            Route::post('/add-social-media', 'App\Http\Controllers\Admin\SocialMediaLinkController@store');
            Route::post('/update-social-media', 'App\Http\Controllers\Admin\SocialMediaLinkController@update');
            Route::delete('/delete-social-media/{id}', 'App\Http\Controllers\Admin\SocialMediaLinkController@destroy');
            Route::get('/update-social-media-status/{id}/{value}', 'App\Http\Controllers\Admin\SocialMediaLinkController@updateColumn');

            /************************* Social Media Settings Ends Here **************************/

            /************************* User Settings Routing Starts Here **************************/

            Route::get('/user', 'App\Http\Controllers\Admin\UserController@index');
            Route::post('/user', 'App\Http\Controllers\Admin\UserController@index');
            // Route::post('/add-social-media', 'App\Http\Controllers\Admin\UserController@store');
            // Route::post('/update-social-media', 'App\Http\Controllers\Admin\UserController@update');
            Route::delete('/delete-user/{id}', 'App\Http\Controllers\Admin\UserController@destroy');
            Route::get('/update-user-status/{id}/{value}', 'App\Http\Controllers\Admin\UserController@updateColumn');
            Route::get('/personlization/{id}', 'App\Http\Controllers\Admin\UserController@personalization');

            /************************* User Settings Ends Here **************************/

            /************************* Subadmin Routing Starts Here **************************/

            Route::get('/sub-admin', 'App\Http\Controllers\Admin\SubAdminController@index');
            Route::post('/sub-admin', 'App\Http\Controllers\Admin\SubAdminController@index');
            Route::post('/add-sub-admin', 'App\Http\Controllers\Admin\SubAdminController@store');
            Route::post('/update-sub-admin', 'App\Http\Controllers\Admin\SubAdminController@update');
            Route::delete('/delete-sub-admin/{id}', 'App\Http\Controllers\Admin\SubAdminController@destroy');
            Route::get('/update-sub-admin-status/{id}/{value}', 'App\Http\Controllers\Admin\SubAdminController@updateColumn');

            /************************* Subadmin Routing Ends Here **************************/

            /************************* Role Routing Starts Here **************************/

            Route::get('/role', 'App\Http\Controllers\Admin\RoleController@index');
            Route::post('/role', 'App\Http\Controllers\Admin\RoleController@index');
            Route::post('/add-role', 'App\Http\Controllers\Admin\RoleController@store');
            Route::post('/update-role', 'App\Http\Controllers\Admin\RoleController@update');
            Route::delete('/delete-role/{id}', 'App\Http\Controllers\Admin\RoleController@destroy');
            Route::get('/update-role-status/{id}/{value}', 'App\Http\Controllers\Admin\RoleController@updateColumn');

            /************************* Role Starts Ends Here **************************/

            /************************* Blog Routing Starts Here **************************/

            Route::get('/post', 'App\Http\Controllers\Admin\BlogController@index');
            Route::post('/post', 'App\Http\Controllers\Admin\BlogController@index');
            Route::get('/add-post/{type}', 'App\Http\Controllers\Admin\BlogController@create');
            Route::get('/update-post/{type}/{id}', 'App\Http\Controllers\Admin\BlogController@edit');
            Route::post('/add-post', 'App\Http\Controllers\Admin\BlogController@store');
            Route::post('/add-quote', 'App\Http\Controllers\Admin\BlogController@storeQuote');
            Route::post('/add-ad', 'App\Http\Controllers\Admin\BlogController@storeAd');
            Route::post('/update-post', 'App\Http\Controllers\Admin\BlogController@update');
            Route::post('/publish-post', 'App\Http\Controllers\Admin\BlogController@update');
            Route::post('/update-quote', 'App\Http\Controllers\Admin\BlogController@updateQuote');
            Route::post('/update-ad', 'App\Http\Controllers\Admin\BlogController@updateAd');
            Route::delete('/delete-post/{id}', 'App\Http\Controllers\Admin\BlogController@destroy');
            Route::get('/update-post-status/{id}/{value}', 'App\Http\Controllers\Admin\BlogController@changeStatus');
            Route::get('/post/translation/{id}', 'App\Http\Controllers\Admin\BlogController@translation');
            Route::post('/post/translation/{id}', 'App\Http\Controllers\Admin\BlogController@updateTranslation');
            Route::get('/analytics/{id}', 'App\Http\Controllers\Admin\BlogController@analytics');
            Route::delete('/delete-selected-post', 'App\Http\Controllers\Admin\BlogController@deleteSelected')->name('deleteSelected');
            Route::post('blog/change-status', 'App\Http\Controllers\Admin\BlogController@changeStatusViaList')->name('admin.blog.changeStatusViaList');


            /************************* Blog Starts Ends Here **************************/

            /******************* Visibility Routing Starts Here *********************/

            Route::get('/visibility', 'App\Http\Controllers\Admin\VisibilityController@index');
            Route::post('/visibility', 'App\Http\Controllers\Admin\VisibilityController@index');
            Route::post('/add-visibility', 'App\Http\Controllers\Admin\VisibilityController@store');
            Route::post('/update-visibility', 'App\Http\Controllers\Admin\VisibilityController@update');
            Route::delete('/delete-visibility/{id}', 'App\Http\Controllers\Admin\VisibilityController@destroy');
            Route::get('/update-visibility-status/{id}/{value}', 'App\Http\Controllers\Admin\VisibilityController@changeStatus');
            Route::get('/translation-visibility/{id}', 'App\Http\Controllers\Admin\VisibilityController@translation');
            Route::post('/translation-visibility/{id}', 'App\Http\Controllers\Admin\VisibilityController@updateTranslation');

            /****************** Visibility Routing Ends Here ***********************/

            /********************** Ads Routing Starts Here ***********************/

            Route::get('/ads', 'App\Http\Controllers\Admin\AdController@index');
            Route::post('/ads', 'App\Http\Controllers\Admin\AdController@index');
            Route::get('/add-ad', 'App\Http\Controllers\Admin\AdController@create');
            Route::post('/add-ad', 'App\Http\Controllers\Admin\AdController@store');
            Route::get('/update-ad/{id}', 'App\Http\Controllers\Admin\AdController@edit');
            Route::post('/update-ad', 'App\Http\Controllers\Admin\AdController@update');
            Route::delete('/delete-ad/{id}', 'App\Http\Controllers\Admin\AdController@destroy');
            Route::get('/detail-ad/{id}', 'App\Http\Controllers\Admin\AdController@show');
            Route::get('/update-ad-status/{id}/{value}', 'App\Http\Controllers\Admin\AdController@changeStatus');
            Route::post('/ads-sortable','App\Http\Controllers\Admin\AdController@sorting');
            Route::get('/ad-analytics/{id}', 'App\Http\Controllers\Admin\AdController@analytics');
            Route::delete('/delete-selected-ad', 'App\Http\Controllers\Admin\AdController@deleteSelected')->name('ad.deleteSelected');

            /************************* Ads Routing Ends Here ************************/

            /******************* News API Routing Starts Here ***********************/

            Route::get('/news-api', 'App\Http\Controllers\Admin\NewsApiController@index');
            Route::post('/news-api', 'App\Http\Controllers\Admin\NewsApiController@index');
            Route::post('/store-post', 'App\Http\Controllers\Admin\NewsApiController@store');

            /************************* News API Routing Ends Here **************************/

            /************************* Languages Routing Starts Here **************************/

            Route::get('/languages', 'App\Http\Controllers\Admin\LanguageController@index');
            Route::post('/languages', 'App\Http\Controllers\Admin\LanguageController@index');
            Route::post('/add-language', 'App\Http\Controllers\Admin\LanguageController@store');
            Route::post('/update-language', 'App\Http\Controllers\Admin\LanguageController@update');
            Route::delete('/delete-language/{id}', 'App\Http\Controllers\Admin\LanguageController@destroy');
            Route::get('/update-language-status/{id}/{value}', 'App\Http\Controllers\Admin\LanguageController@changeStatus');

            /************************* Languages Routing Ends Here **************************/

            /********************** Translation Routing Starts Here ************************/

            Route::get('/translation', 'App\Http\Controllers\Admin\TranslationController@index');
            Route::post('/translation', 'App\Http\Controllers\Admin\TranslationController@index');
            Route::post('/add-translation', 'App\Http\Controllers\Admin\TranslationController@store');
            Route::get('/update-translation/{id}', 'App\Http\Controllers\Admin\TranslationController@edit');
            Route::post('/update-translation', 'App\Http\Controllers\Admin\TranslationController@update');

            /****************** Translation Routing Ends Here *********************/

            /************** Push Notification Routing Starts Here ****************/

            Route::get('/push-notification', 'App\Http\Controllers\Admin\PushNotificationController@index');
            Route::post('/push-notification', 'App\Http\Controllers\Admin\PushNotificationController@index');
            Route::get('/send-push-notification', 'App\Http\Controllers\Admin\PushNotificationController@create');
            Route::post('/send-push-notification', 'App\Http\Controllers\Admin\PushNotificationController@store');
            Route::delete('/delete-push-notification/{id}', 'App\Http\Controllers\Admin\PushNotificationController@destroy');

            /****************** Push Notification Routings Ends Here ****************/

            /***************** Search Log Routing Starts Here ********************/

            Route::get('/search-log', 'App\Http\Controllers\Admin\SearchLogController@index');
            Route::post('/search-log', 'App\Http\Controllers\Admin\SearchLogController@index');

            /***************** Search Log Routings Ends Here *********************/

            /****************** Rss Feeds Routing Starts Here ********************/

            Route::get('/rss-feeds', 'App\Http\Controllers\Admin\RssFeedController@index');
            Route::post('/rss-feeds', 'App\Http\Controllers\Admin\RssFeedController@index');
            Route::post('/add-rss-feeds', 'App\Http\Controllers\Admin\RssFeedController@store');
            Route::post('/update-rss-feeds', 'App\Http\Controllers\Admin\RssFeedController@update');
            Route::delete('/delete-rss-feeds/{id}', 'App\Http\Controllers\Admin\RssFeedController@destroy');
            Route::get('/update-rss-feeds-status/{id}/{value}', 'App\Http\Controllers\Admin\RssFeedController@updateColumn');  
            Route::get('/check-rss/{id}', 'App\Http\Controllers\Admin\RssFeedController@checkFeedItems');
            Route::get('/update-rss-autopublish-status/{id}/{value}', 'App\Http\Controllers\Admin\RssFeedController@updateAutoPublishColumn');       

            /**************** Rss Feeds Routing Ends Here *************************/

            /***************** Rss Feed Items Routing Starts Here ******************/

            Route::get('/rss-feed-items', 'App\Http\Controllers\Admin\RssFeedItemController@index');
            Route::post('/rss-feed-items', 'App\Http\Controllers\Admin\RssFeedItemController@index');
            Route::post('/store-rss-item', 'App\Http\Controllers\Admin\RssFeedItemController@store');

            /**************** Rss Feed Items Routing Ends Here *******************/
            
            /******************* Shorts Routing Starts Here *********************/
            Route::get('/short-video', 'App\Http\Controllers\Admin\ShortVideoController@index');
            Route::get('/create-short-video', 'App\Http\Controllers\Admin\ShortVideoController@create');
            Route::post('/add-short-video', 'App\Http\Controllers\Admin\ShortVideoController@store');
            Route::get('/edit-short-video/{id}', 'App\Http\Controllers\Admin\ShortVideoController@edit');
            Route::post('/update-short-video', 'App\Http\Controllers\Admin\ShortVideoController@update');
            Route::delete('/delete-short-video/{id}', 'App\Http\Controllers\Admin\ShortVideoController@destroy');
            Route::get('/update-short-video-status/{id}/{value}', 'App\Http\Controllers\Admin\ShortVideoController@updateColumn');
            Route::get('/short-video/translation/{id}', 'App\Http\Controllers\Admin\ShortVideoController@translation');
            Route::post('/short-video/translation/{id}', 'App\Http\Controllers\Admin\ShortVideoController@updateTranslation');
            Route::get('/short-video-analytics/{id}', 'App\Http\Controllers\Admin\ShortVideoController@analytics');
            Route::delete('/delete-selected-short-video', 'App\Http\Controllers\Admin\ShortVideoController@deleteSelected')->name('short-video.deleteSelected');
            Route::post('short-video/change-status', 'App\Http\Controllers\Admin\ShortVideoController@changeStatusViaList')->name('admin.shortVideo.changeStatusViaList');
             /***************** Shorts Routing Ends Here ***********************/
            
        });
        Auth::routes();
    });
    Route::middleware(['check.app.installation','check.app.code_verified'])->group(function () {
        Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'checkSubadminStatus']], function () {
            Route::post('/get-subcategory', 'App\Http\Controllers\Admin\BlogController@getSubcategories');
            Route::post('/store-image', 'App\Http\Controllers\Admin\BlogController@storeImage');
            Route::post('/remove-image', 'App\Http\Controllers\Admin\BlogController@removeImage');
            Route::post('/remove-image-by-name', 'App\Http\Controllers\Admin\BlogController@removeImageByName');
            Route::post('/post-image-sortable','App\Http\Controllers\Admin\BlogController@sorting');
            Route::post('/send-notification-to-users', [BlogController::class, 'sendNotification'])->name('send-notification-to-users');
            Route::post('/get-source', 'App\Http\Controllers\Admin\RssFeedItemController@getSource');
            Route::post('admin/get-sources-by-category', 'App\Http\Controllers\Admin\RssFeedItemController@getSourcesByCategory')->name('admin.get-sources-by-category');
        });
    });
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/share-blog', 'App\Http\Controllers\ShareController@shareBlog');
Route::get('/blog-share', 'App\Http\Controllers\ShareController@shareBlog');
Route::get('/e-news/{id}', 'App\Http\Controllers\ShareController@shareENews');
Route::get('/live-news/{id}', 'App\Http\Controllers\ShareController@shareLiveNews');
Route::get('/shorts/{id}', 'App\Http\Controllers\ShareController@shareShorts');
Route::get('/unauthorized', function () {
    return view('unauthorized');
})->name('unauthorized');

/************************* Site Routes Starts Here **************************/
Route::get('/{page_name}','App\Http\Controllers\Site\CmsController@index');
