<?php
use App\Models\Blog;
use App\Models\Category; 
use App\Models\CmsContent; 
use App\Models\User;
use App\Models\BlogVisibility;
use App\Models\UserFeed;
use App\Models\BlogCategory;
use App\Models\BlogQuestion;
use App\Models\BlogQuestionOption;
use App\Models\Vote;
use App\Models\Language;
use App\Models\BlogImage;
use App\Models\BlogBookmark;
use App\Models\BlogAnalytic;
use App\Models\AdAnalytic;
use App\Models\ShortVideoAnalytic;
use App\Models\Setting;
use App\Models\AdminNotification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Pagination\LengthAwarePaginator;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Helpers {
    
    /**
     * Get user language code
    **/
    public static function returnUserLanguageCode() {
        // $language = setting('preferred_site_language');
        $language = "en";

        if (auth()->user() && auth()->user()->type == 'user') {
            if (auth()->user()->lang_code != '') {
                $language = auth()->user()->lang_code;
            }
        }else{
            if(isset($_COOKIE['lang_code']) && $_COOKIE['lang_code'] != '') {
                $language = $_COOKIE['lang_code'];
            }
        }
        return $language;
    }

    /**
     * Create slug 
    **/
    public static function createSlug($title,$in='blog',$whr=0,$alphaNum = false){
        if($alphaNum){
            $slug = Str::slug($title, '-');
        }else{
            $slug = Str::slug($title, '-');
        }
        if($in == 'blog'){            
            $slugExist = Blog::where(DB::raw('LOWER(slug)'),strtolower($slug))->where('id','!=',$whr)->get();
        }else if($in == 'category'){
            $slugExist = Category::where(DB::raw('LOWER(slug)'),strtolower($slug))->where('id','!=',$whr)->get();
        }else if($in == 'cms'){
            $slugExist = CmsContent::where(DB::raw('LOWER(page_title)'),strtolower($slug))->where('id','!=',$whr)->get();
        }
        if(count($slugExist)){
            $slug = Str::slug($title.'-'.Str::random(5).'-'.Str::random(5), '-');
            return $slug;
        }else{
            return $slug;
        }
    }
    
    
    /**
     * Upload video file
    **/
    public static function uploadVideoFiles($file, $folderName) {
        try {
            // Generate a unique file name with the original extension
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = time() . rand() . '.' . $fileExtension;
    
            // Move the uploaded file to the specified directory
            $file->move(public_path('uploads/' . $folderName), $fileName);
    
            return ['status' => true, 'message' => 'Video uploaded successfully!', 'file_name' => $fileName];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Upload file
    **/
    public static function uploadFiles($file, $folderName)
    {
        try {
            $fileName = time() . rand() . '.webp';
            $path = public_path('uploads/' . $folderName);

            // Create folder if it does not exist
            if (!\File::exists($path)) {
                \File::makeDirectory($path, 0755, true, true);
            }

            $image = \Image::make($file)->encode('webp', 75);
            $image->save($path . '/' . $fileName);

            return [
                'status' => true,
                'message' => config('constant.common.messages.success_image'),
                'file_name' => $fileName
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            ];
        }
    }


    /**
     * Upload base 64 file
    **/
    public static function uploadBase64Files($file)
    {
        try {
            $image = Image::make($file)->encode('webp', 75);
            $base64Image = 'data:image/webp;base64,' . base64_encode($image);

            return ['status' => true, 'message' => config('constant.common.messages.success_image'), 'file_name' => $base64Image];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()];
        }
    }


    /**
     * Upload Pdf file
    **/
    public static function uploadPDF($file,$folderName) {
        try {
            $fileName = time() . rand() .'.'.$file->extension();
            $file->move(public_path('uploads/'.$folderName.'/') , $fileName);
            return ['status' => true, 'message' => config('constant.common.messages.success_image'),'file_name'=>$fileName];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Upload file
    **/
    public static function uploadFilesAfterResizeCompress($file,$folderName) {
        try {
            $fileName = time() . rand() .'.'.$file->extension();
            $image = Image::make($file);
            $image->save(public_path('uploads/'.$folderName.'/original/'.$fileName));
            $image->resize(768, 428) // Resize the image to 768x428 pixels
                ->encode($file->extension(), 75) // Compress the image to 75% quality JPEG
                ->save(public_path('uploads/'.$folderName.'/768x428/'.$fileName));
            $imagesmall = Image::make($file);
            $imagesmall->resize(327, 250) // Resize the image to 327x250 pixels
                ->encode($file->extension(), 75) // Compress the image to 75% quality JPEG
                ->save(public_path('uploads/'.$folderName.'/327x250/'.$fileName));            
            $imageextrasmall = Image::make($file);
            $imageextrasmall->resize(80, 80) // Resize the image to 800x600 pixels
                    ->encode($file->extension(), 75) // Compress the image to 75% quality JPEG
                    ->save(public_path('uploads/'.$folderName.'/80x80/'.$fileName));
            return ['status' => true, 'message' => config('constant.common.messages.success_image'),'file_name'=>$fileName];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Upload file with original name
    **/
    public static function uploadFilesAfterResizeCompressOriginalName($file,$folderName) {
        try {
            $fileName = $file->getClientOriginalName();
            $imageName = "";
            if($fileName!=''){
                $finalName = "";
                $explodeFileName = explode(".",$fileName);
                if(isset($explodeFileName[0]) && $explodeFileName[0]!=''){
                    $finalName = Str::slug($explodeFileName[0],'-');
                    $checkExist = BlogImage::where('image',$finalName.'.webp')->first();
                    if($checkExist){
                        $finalName = Str::slug($explodeFileName[0].'-'.Str::random(5).'-'.Str::random(5), '-');
                    }
                }
                $imageName = $finalName.'.webp';
            }
            
            if($file->extension()==='gif'){
                $path1 = public_path('uploads/'.$folderName.'/original/');
                $image = $file;
                $image->move($path1, $imageName);

                $sourceFolder = public_path('uploads/'.$folderName.'/original/');
                $sourceFilePath = public_path('uploads/'.$folderName.'/original/'.$imageName);
                $destinationFolders = [
                    '768x428',
                    '327x250',
                    '80x45',
                ];

                foreach ($destinationFolders as $dir) {
                    $destinationFolder = public_path('uploads/'.$folderName.'/'.$dir.'/');
                    if (!file_exists($destinationFolder)) {
                        mkdir($destinationFolder, 0777, true);
                    }

                    $destinationFilePath = $destinationFolder . $imageName;
                    copy($sourceFilePath, $destinationFilePath);
                }                
            }else{
                $image = Image::make($file)->encode('webp', 75);
                $image->save(public_path('uploads/'.$folderName.'/original/'.$imageName));

                $croppedImage = Image::make($file)->fit(768, 428)->encode('webp', 75);
                $croppedImage->save(public_path('uploads/'.$folderName.'/768x428/'.$imageName));

                $imagesmall = Image::make($file)->fit(327, 250)->encode('webp', 75);
                $imagesmall->save(public_path('uploads/'.$folderName.'/327x250/'.$imageName));   

                $imageextrasmall = Image::make($file)->fit(80, 45)->encode('webp', 75);
                $imageextrasmall->save(public_path('uploads/'.$folderName.'/80x45/'.$imageName));
            }            
            return ['status' => true, 'message' => config('constant.common.messages.success_image'),'file_name'=>$imageName];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }
    
    
     /**
     * Upload file with original name
    **/
    public static function uploadFilesAfterResizeCompressOriginalNameQuote($file, $folderName)
    {
        try {
            $fileName = $file->getClientOriginalName();
            $imageName = '';
    
            if ($fileName != '') {
                $explodeFileName = explode('.', $fileName);
                $baseName = isset($explodeFileName[0]) ? $explodeFileName[0] : 'image';
    
                $finalName = Str::slug($baseName, '-');
                $checkExist = BlogImage::where('image', $finalName . '.webp')->first();
                if ($checkExist) {
                    $finalName = Str::slug($baseName . '-' . Str::random(5) . '-' . Str::random(5), '-');
                }
    
                $imageName = $finalName . '.webp';
            }
    
            $mainPath = public_path('uploads/' . $folderName);
            $folders = ['original', '768x428', '327x250', '80x45'];
    
            foreach ($folders as $dir) {
                $folderPath = $mainPath . '/' . $dir . '/';
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }
            }
    
            if ($file->extension() === 'gif') {
                $originalPath = $mainPath . '/original/' . $imageName;
                $file->move($mainPath . '/original/', $imageName);
    
                // Copy GIF to all other folders
                foreach (['768x428', '327x250', '80x45'] as $dir) {
                    copy($originalPath, $mainPath . '/' . $dir . '/' . $imageName);
                }
            } else {
                // Convert to webp (no resize) and save to all folders
                $image = Image::make($file)->encode('webp', 75);
                foreach ($folders as $dir) {
                    $image->save($mainPath . '/' . $dir . '/' . $imageName);
                }
            }
    
            return [
                'status' => true,
                'message' => config('constant.common.messages.success_image'),
                'file_name' => $imageName
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            ];
        }
    }

    
    
    /**
     * Upload file
    **/
    public static function uploadFilesThroughUrlAfterResizeCompress($file,$folderName) {
        try {
            // $fileName = time() . rand() .'.'.$file->extension();
            $ext = self::get_file_extension($file);
            $extention = array("jpg","jpeg","png","webp","gif");
            if(!in_array($ext,$extention)){
                $content = file_get_contents($file);

                // Store in the original folder.
                $imageName = time() . rand() .'.webp';
                $originalDir = public_path('uploads/' . $folderName . '/original/');
                $originalPath = $originalDir . $imageName;
                $fp = fopen($originalPath, "w");
                fwrite($fp, $content);
                fclose($fp);
                // $imgname = "my.jpg";

                $image = Image::make($originalPath)->encode('webp', 75);

                // Save in different folders with different sizes.
                $sizes = [
                    ['folder' => '768x428', 'width' => 768, 'height' => 428],
                    ['folder' => '327x250', 'width' => 327, 'height' => 250],
                    ['folder' => '80x45', 'width' => 80, 'height' => 45],
                ];

                foreach ($sizes as $sizeData) {
                    $folder = $sizeData['folder'];
                    $width = $sizeData['width'];
                    $height = $sizeData['height'];

                    $resizedImage = $image->fit($width, $height)->encode('webp', 75);
                    $resizedPath = public_path('uploads/' . $folderName . '/' . $folder . '/' . $imageName);
                    $resizedImage->save($resizedPath);
                }
            }else{
                $imageName = time() . rand() .'.webp';
                $image = Image::make($file)->encode('webp', 75);
                $image->save(public_path('uploads/'.$folderName.'/original/'.$imageName));

                $croppedImage = Image::make($file)->fit(768, 428)->encode('webp', 75);
                $croppedImage->save(public_path('uploads/'.$folderName.'/768x428/'.$imageName));

                $imagesmall = Image::make($file)->fit(327, 250)->encode('webp', 75);
                $imagesmall->save(public_path('uploads/'.$folderName.'/327x250/'.$imageName));   

                $imageextrasmall = Image::make($file)->fit(80, 45)->encode('webp', 75);
                $imageextrasmall->save(public_path('uploads/'.$folderName.'/80x45/'.$imageName));
            }
           
            return ['status' => true, 'message' => config('constant.common.messages.success_image'),'file_name'=>$imageName];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Upload file
    **/
    public static function uploadFilesThroughUrl($file,$folderName) {
        try {
            // $fileName = time() . rand() .'.'.$file->extension();
            $ext = self::get_file_extension($file);
            $fileName = time() . rand() .'.webp';
            $image = Image::make($file)->encode('webp', 75);
            $image->save(public_path('uploads/'.$folderName.'/'.$fileName));
            return ['status' => true, 'message' => config('constant.common.messages.success_image'),'file_name'=>$fileName];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    public static function translate($text, $targetLanguage)
    {
        $client = new Client();
        $url = 'https://api.openai.com/v1/completions';
        $apiKey = setting('chat_gpt_api_key');
        $params = [
            "model" => "text-davinci-003",
            'prompt' => "Translate this into " . $targetLanguage . " :\n\n\n" . $text . "\n\n\n",
            "temperature" => 0.3,
            "max_tokens" => 100,
        ];

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => $params,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $body = $response->getBody();
                $decodedBody = json_decode($body, true);
                $generatedText = $decodedBody['choices'][0]['text'];
                $response_data = array(
                    'status'=>true,
                    'data'=>$generatedText,
                    'message'=>__('lang.message_data_translated_successfully')
                );
                return $response_data;
            } else {
                $response_data = array(
                    'status'=>false,
                    'data'=>[],
                    'message'=>"Unexpected response status - " . $statusCode
                );
                // Handle non-200 response status here
                return $response_data;
            }
        } catch (RequestException $e) {
            // Handle request exceptions here (e.g., network errors, API errors)
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $body = $response->getBody();
                $error = json_decode($body, true);
                $response_data = array(
                    'status'=>false,
                    'data'=>[],
                    'message'=>$error['error']['message']
                );
                return $response_data;
            } else {
                $response_data = array(
                    'status'=>false,
                    'data'=>[],
                    'message'=>$e->getMessage()
                );
                return $response_data;
            }
        } catch (GuzzleException $e) {
            // Handle Guzzle exceptions here
            $response_data = array(
                'status'=>false,
                'data'=>[],
                'message'=>$e->getMessage()
            );
            return $response_data;
        }
    }

    public static function googleTranslation($text, $targetLanguage)
    {
        $apiKey = setting('google_translation_api_key');
    
        $apiUrl = "https://translation.googleapis.com/language/translate/v2?key=$apiKey";
    
        $data = array(
            "q" => [$text],
            "target" => $targetLanguage
        );
    
        $ch = curl_init($apiUrl);
    
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        curl_close($ch);
    
        $result = json_decode($response, true);
        if ($result && isset($result['data']['translations'][0]['translatedText'])) {
            $translation = $result['data']['translations'][0]['translatedText'];
            $response_data = array(
                'status'=>true,
                'data'=>$translation,
                'message'=>__('lang.message_data_translated_successfully')
            );
            return $response_data;
        } else {
            $response_data = array(
                'status'=>false,
                'data'=>[],
                'message'=>"Unexpected response status"
            );
            return $response_data;
        }
    
    }

    /**
     * Get Page Name on the basis of uri segment
    **/
    public static function getPageName($url) {
        // $language = setting('preferred_site_language');
        if ($url == 'site-setting') {
            $title = __('lang.site_settings') ;
        }else if ($url == 'app-setting') {
            $title = __('lang.app_settings');
        }else if ($url == 'global-setting') {
            $title = __('lang.global_settings');
        }else if ($url == 'push-notification-setting') {
            $title = __('lang.push_notification_settings');
        }else if ($url == 'email-setting') {
            $title = __('lang.email_settings');
        }else if ($url == 'maintainance-setting') {
            $title = __('lang.maintainance_settings');
        }else if ($url == 'news-setting') {
            $title = __('lang.news_settings');
        }else if ($url == 'admob-setting') {
            $title = __('lang.admob_settings');
        }else if ($url == 'fb-ads-setting') {
            $title = __('lang.fb_ads_settings');
        }else if ($url == 'social') {
            $title = trans("admin.social");
        }else{
            $title = trans("admin.no_setting");
        }
        return $title;
    }


    /**
     * Check Role has selected that permission
    **/
    public static function checkRoleHasPermission($role_id,$permission_id) {
        // $language = setting('preferred_site_language');
        $permission = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$role_id)
            ->where("role_has_permissions.permission_id",$permission_id)->count();
        if ($permission>0) {
           return 1;
        }else{
            return 0;
        }        
    }

    /**
     * get list of news from news api
    **/
    public static function getAllNewsLists($search)
    {
        $type = isset($search['type']) ? strtolower($search['type']) : '';
    
        if ($type === 'newsapi') {
            $data = [];
            $sources = [];
            $sourcesUrl = 'https://newsapi.org/v2/sources';
            $sourcesFields = [
                'apiKey' => setting('news_api_key'),
            ];
            $sourcesUrl = $sourcesUrl . '/?' . http_build_query($sourcesFields);
    
            $ch = curl_init($sourcesUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'PostmanRuntime/7.26.8',
            ]);
            $sourcesResult = curl_exec($ch);
            curl_close($ch);
    
            $sourcesResult = json_decode($sourcesResult, true);
            if ($sourcesResult['status'] === 'ok') {
                $sources = $sourcesResult['sources'];
            }
    
            // Fetching articles
            if (isset($search['keyword']) && $search['keyword'] !== '') {
                $articlesUrl = 'https://newsapi.org/v2/everything';
                $articlesFields = [
                    'q' => $search['keyword'],
                    'sortBy' => 'publishedAt',
                    'apiKey' => setting('news_api_key'),
                ];
    
                $optionalFields = ['sources', 'domains', 'language', 'from', 'to'];
                foreach ($optionalFields as $field) {
                    if (isset($_GET[$field]) && $_GET[$field] !== '') {
                        $articlesFields[$field] = $_GET[$field];
                    }
                }
    
                $articlesUrl = $articlesUrl . '/?' . http_build_query($articlesFields);
                $ch = curl_init($articlesUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERAGENT => 'PostmanRuntime/7.26.8',
                ]);
                $result = curl_exec($ch);
                curl_close($ch);
    
                $result = json_decode($result, true);
                if ($result['status'] === 'ok') {
                    $data = $result['articles'];
                }
            }
            $finalArray = array(
                'data'=>self::arrayPaginator($data,$search),
                'source'=>$sources,
                'news_api_language' =>config('constant.news_api_language')
            );
            return $finalArray;
        } elseif($type === 'mediastackapi') {
    
            $data = [];
            $uniqueSources = [];
    
            $apiKey = setting('mediastack_api_key');
            $baseUrl = 'http://api.mediastack.com/v1/news';
    
            /*
            |------------------------------------------------------
            | 1) Always fetch sources (small request)
            |------------------------------------------------------
            */
            $sourceParams = [
                'access_key' => $apiKey,
                'limit'      => 100,
                'languages'  => 'en'
            ];
    
            $sourceUrl = $baseUrl . '?' . http_build_query($sourceParams);
    
            $ch = curl_init($sourceUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT      => 'PostmanRuntime/7.26.8',
                CURLOPT_TIMEOUT        => 10,
            ]);
            $sourceResponse = curl_exec($ch);
            curl_close($ch);
    
            $sourceResponse = json_decode($sourceResponse, true);
    
            if (isset($sourceResponse['data'])) {
                foreach ($sourceResponse['data'] as $item) {
    
                    $sourceRaw = $item['source'] ?? null;
    
                    if (empty($sourceRaw) && !empty($item['url'])) {
                        $host = parse_url($item['url'], PHP_URL_HOST);
                        $sourceRaw = $host ? preg_replace('/^www\./', '', $host) : 'Unknown';
                    }
    
                    $sourceId = $sourceRaw ?? 'Unknown';
                    $sourceName = ucfirst($sourceId);
    
                    $uniqueSources[$sourceId] = [
                        'id' => $sourceId,
                        'name' => $sourceName
                    ];
                }
            }
    
            /*
            |------------------------------------------------------
            | 2) Only fetch actual articles WHEN user selected filter
            |------------------------------------------------------
            */
            $shouldFetch = false;
    
            if (!empty($search['keyword'])) $shouldFetch = true;
            if (!empty($search['sources'])) $shouldFetch = true;
    
            if ($shouldFetch) {
    
                $params = [
                    'access_key' => $apiKey,
                    'limit'      => 50,
                    'languages'  => 'en'
                ];
    
                if (!empty($search['keyword']))
                    $params['keywords'] = $search['keyword'];
    
                if (!empty($search['sources']))
                    $params['sources'] = $search['sources'];
    
                if (!empty($search['country']))
                    $params['countries'] = $search['country'];
    
                if (!empty($search['category']))
                    $params['categories'] = $search['category'];
    
                if (isset($_GET['from']) && $_GET['from'] != '')
                    $params['date'] = $_GET['from'];
    
                $url = $baseUrl . '?' . http_build_query($params);
    
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERAGENT      => 'PostmanRuntime/7.26.8',
                    CURLOPT_TIMEOUT        => 10,
                ]);
                $response = curl_exec($ch);
                curl_close($ch);
    
                $response = json_decode($response, true);
    
                if (isset($response['data'])) {
                    foreach ($response['data'] as $item) {
    
                        $sourceRaw = $item['source'] ?? null;
    
                        if (empty($sourceRaw) && !empty($item['url'])) {
                            $host = parse_url($item['url'], PHP_URL_HOST);
                            $sourceRaw = $host ? preg_replace('/^www\./', '', $host) : 'Unknown';
                        }
    
                        $sourceId = $sourceRaw ?? 'Unknown';
                        $sourceName = ucfirst($sourceId);
    
                        $obj = new \stdClass();
                        $obj->id = uniqid();
                        $obj->source = [
                            'id' => $sourceId,
                            'name' => $sourceName
                        ];
                        $obj->url = $item['url'] ?? '';
                        $obj->image = $item['image'] ?? null;
                        $obj->title = $item['title'] ?? '';
                        $obj->description = $item['description'] ?? '';
                        $obj->author = $item['author'] ?? '';
                        $obj->publishedAt = $item['published_at'] ?? '';
                        $obj->content = $item['content'] ?? '';
                        $obj->status = 1;
    
                        $data[] = $obj;
                    }
                }
            }
    
            return [
                'data' => self::arrayPaginator($data, $search),
                'source' => array_values($uniqueSources),
                'news_api_language' => config('constant.mediastack_languages')
            ];
        }
        else{
            $data = [];
            $sources = [];
            $sourcesUrl = '';
            $sourcesFields = [
                'apiKey' => '',
            ];
            $sourcesUrl = '';
    
            $ch = curl_init($sourcesUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'PostmanRuntime/7.26.8',
            ]);
            $sourcesResult = curl_exec($ch);
            curl_close($ch);
    
            $sourcesResult = json_decode($sourcesResult, true);
    
            $finalArray = array(
                'data'=>self::arrayPaginator($data,$search),
                'source'=> $sources,
                'news_api_language' =>config('constant.news_api_language')
            );
            return $finalArray;
        }
    
    }

    public static function arrayPaginator($array, $request)
    {
        $post = $request;
        $per_page_number = config('constant.pagination');
        $page = (isset($post['page']) && !empty($post['page'])) ? $post['page'] : 1;
        $perPage = (isset($post['perpage']))?$post['perpage']:$per_page_number;
        $offset = ($page * $perPage) - $perPage;
        $sliceArray = array_slice($array, $offset, $perPage, true);
        $finalArray = array();
        foreach ($sliceArray as $row) {
            array_push($finalArray, $row);
        }

        return new LengthAwarePaginator($finalArray, count($array), $perPage, $page,['path' => $request->url(), 'query' => $request->query()]);
    }

    public static function get_file_extension($file_name) {
        return substr(strrchr($file_name,'.'),1);
    }

    public static function generateApiToken(){
        mt_srand((double)microtime()*10000);
        $uuid = rand(1,99999).time();
        $salt = substr(sha1(uniqid(mt_rand(), true)), 0, 40);
        return substr(sha1($salt) . $salt,1,85).$uuid;
    }

    public static function validateAuthToken($token){
        $tokenExist  = User::where('api_token',$token)->first();
        if($tokenExist){
            return $tokenExist;
        }
        return false;
    }

    public static function getBlogsArrOnTheBasisOfCategory($category_id){
        $blog_arr = array();
        $getBlogs = BlogCategory::where('category_id',$category_id)->get();
        if(count($getBlogs)){
            foreach($getBlogs as $getBlogs_data){
                array_push($blog_arr,$getBlogs_data->blog_id);
            }
        }
        return $blog_arr;
    }

    public static function categoryIsInFeed($category_id,$user_id){
        $is_feed = false;
        $getFeed = UserFeed::where('category_id',$category_id)->where('user_id',$user_id)->first();
        if($getFeed){
            $is_feed = true;
        }
        return $is_feed;
    }

    public static function getVotes($blog_id,$user_id){
        $is_vote = 0;
        $getVote = Vote::where('blog_id',$blog_id)->where('user_id',$user_id)->first();
        if($getVote){
            $is_vote = $getVote->option_id;
        }
        return $is_vote;
    }

    public static function getBookmarks($blog_id,$user_id){
        $is_bookmark = 0;
        $getData = BlogBookmark::where('blog_id',$blog_id)->where('user_id',$user_id)->first();
        if($getData){
            $is_bookmark = 1;
        }
        return $is_bookmark;
    }

    public static function getViewed($blog_id,$user_id){
        $is_viewed = 0;
        $getData = BlogAnalytic::where('type','view')->where('blog_id',$blog_id)->where('user_id',$user_id)->first();
        if($getData){
            $is_viewed = 1;
        }
        return $is_viewed;
    }

    public static function getVisibilities($blog_id){
        $visibility_arr = array();
        $getVisibility = BlogVisibility::select('visibility_id')->where('blog_id',$blog_id)->get();
        if(count($getVisibility)){
            foreach($getVisibility as $getVisibility_data){
                array_push($visibility_arr,$getVisibility_data->visibility_id);
            }
        }
        return $visibility_arr;
    }

    public static function getQuestionsOptions($blog_id){
        $getQuestions = BlogQuestion::where('blog_id',$blog_id)->first();
        if($getQuestions){
            $getQuestions->options = BlogQuestionOption::where('blog_pool_question_id',$getQuestions->id)->get();            
            if (count($getQuestions->options)) {
                $totalVotes = Vote::where('blog_id', $blog_id)->count();        
                foreach ($getQuestions->options as $getOptions_data) {
                    $optionVotes = Vote::where('blog_id', $blog_id)->where('option_id', $getOptions_data->id)->count();                    
                    if ($totalVotes > 0) {
                        $getOptions_data->percentage = ($optionVotes / $totalVotes) * 100;
                    } else {
                        $getOptions_data->percentage = 0.0;
                    }
                }
            }          
        }
        return $getQuestions;
    }

    public static function getBlogImages($blog_id,$folderName){
        $blog_image_arr = array();
        $blog_images = BlogImage::where('blog_id',$blog_id)->get();
        if(count($blog_images)){
            foreach($blog_images as $images){
                $images->image = url('uploads/blog/'.$folderName.'/'.$images->image);
                array_push($blog_image_arr,$images->image);
            }
        }
        return $blog_image_arr;
    }

    public static function getAllLangList(){
        $list  = Language::where('status',1)->get();
        return $list;
    }
    
    // This is for send one signal notification
    public static function sendOneSignalNotification($title, $description, $image, $blog, $player_ids)
    {
        $buttons = [];
        $cleanedDescription = strip_tags($description);
        $cleanedDescription = mb_substr($cleanedDescription, 0, 100, 'UTF-8');
        $content = ["en" => $cleanedDescription];
        $headings = ["en" => $title];
    
        if ($blog) {
            if ($blog->type == 'post') {
                $buttons = [
                    ['id' => 'Share', 'text' => 'Share', 'icon' => 'share'],
                    ['id' => 'Bookmark', 'text' => 'Bookmark', 'icon' => 'bookmark']
                ];
            } else {
                $buttons = [['id' => 'Share', 'text' => 'Share', 'icon' => 'share']];
            }
        }
    
        $icon = url('uploads/setting/' . setting('app_logo'));
        $dataArr = $blog ? ["blog" => $blog->id, "title" => $title] : ["title" => $title];
    
        $fields = [
            'app_id' => setting('one_signal_app_id'),
            'include_player_ids' => $player_ids,
            'data' => $dataArr,
            'big_picture' => $image,
            'contents' => $content,
            'headings' => $headings,
            'buttons' => $buttons,
            'icon' => $icon,
            'image' => $image,
            'large_icon' => $image,
        ];
    
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . setting('one_signal_key')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        
        $httpStatus == 200;
    
        // Check for invalid player IDs
        // if (isset($responseData['errors']['invalid_player_ids'])) {
    
        //     // Filter invalid player IDs
        //     $validPlayerIds = self::filterValidPlayerIds($player_ids, $responseData['errors']['invalid_player_ids']);
    
        //     if (!empty($validPlayerIds)) {

        //         $fields = [
        //             'app_id' => setting('one_signal_app_id'),
        //             'include_player_ids' => $validPlayerIds,
        //             'data' => $dataArr,
        //             'big_picture' => $image,
        //             'contents' => $content,
        //             'headings' => $headings,
        //             'buttons' => $buttons,
        //             'icon' => $icon,
        //             'image' => $image,
        //             'large_icon' => $image,
        //         ];
    
        //         $fields = json_encode($fields);
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        //         curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //             'Content-Type: application/json; charset=utf-8',
        //             'Authorization: Basic ' . setting('one_signal_key')
        //         ]);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //         curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //         curl_setopt($ch, CURLOPT_POST, TRUE);
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //         $response = curl_exec($ch);
        //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         curl_close($ch);
        //         $responseData = json_decode($response, true);

        //         if (isset($responseData['errors']) && !empty($responseData['errors'])) {
        //             $errorMessage = $responseData['errors'][0];
        //             if (strpos($errorMessage, 'Access denied') === 0) {
        //               $httpStatus = 200;
        //             }
        //         }
        //     }
        // }
        
        return $httpStatus;
    }

    // This method filters out invalid player IDs
    public static function filterValidPlayerIds($player_ids, $invalid_player_ids)
    {
        // Return only the valid player IDs by removing the invalid ones
        return array_diff($player_ids, $invalid_player_ids);
    }



    // This is for send firebase notifications
    public static function sendFcmNotification($title, $description, $image, $blog, $fcm_tokens)
    {
        $fcm_tokens = array_unique($fcm_tokens);

        $cleanedDescription = strip_tags($description);
        $description = mb_substr($cleanedDescription, 0, 100, 'UTF-8');

        $projectId = setting('fcm_project_id');
        $credentialsFilePath = Storage::path('/json/firebase_credentials.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json',
        ];

        $anySuccess = false;
        $messageArr = [];

        foreach ($fcm_tokens as $fcm_token) {
            if(!in_array($fcm_token,$messageArr)){
                array_push($messageArr,$fcm_token);
                $data = [
                    "message" => [
                        "token" => $fcm_token,
                        // "notification" => [
                        //     "title" => $title,
                        //     "body" => $description,
                        //     "image" => $image // Global image field for all platforms
                        // ],
                        // Data for send params
                        "data" => [
                            "post_id" => (string)$blog->id,  // Cast post_id to string
                            "title" => $title,
                            "body" => $description,
                            "image" => $image
                        ],
                        // Android-specific notification with image
                        // "android" => [
                        //     "notification" => [
                        //         "image" => $image,
                        //     ]
                        // ],
                        // iOS-specific notification with image
                        "apns" => [
                            "payload" => [
                                "aps" => [
                                    "mutable-content" => 1,
                                    "imageUrl" => $image,
                                    "post_id" => (string)$blog->id,  // Cast post_id to string
                                    'image' => $image,
                                    'large_icon' => $image,
                                    'big_picture' => $image,
                                ],
                            ],
                            "fcm_options" => [
                                "image" => $image, // This is where iOS gets the image
                            ]
                        ],
                        // WebPush-specific notification with image
                        // "webpush" => [
                        //     "notification" => [
                        //         "image" => $image,
                        //     ]
                        // ]
                    ]
            ];
                $payload = json_encode($data);
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $response = curl_exec($ch);
    
                if (!curl_errno($ch)) {
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($httpCode === 200) {
                        $anySuccess = true;
                    }
                }
    
                    curl_close($ch);
            }
        }

        if ($anySuccess) {
            return 200;
        } else {
            return 500;
        }
    }
    
    
    // ******************************Manual*********************************************
    // This is for send one signal notification
    public static function sendOneSignalNotificationManually($title, $description, $image, $id, $player_ids)
    {
        $buttons = [];
        $cleanedDescription = strip_tags($description);
        $cleanedDescription = mb_substr($cleanedDescription, 0, 100, 'UTF-8');
        $content = ["en" => $cleanedDescription];
        $headings = ["en" => $title];
    
        $icon = url('uploads/setting/' . setting('app_logo'));
        $dataArr = ["title" => $title];
    
        $fields = [
            'app_id' => setting('one_signal_app_id'),
            'include_player_ids' => $player_ids,
            'data' => $dataArr,
            'big_picture' => $image,
            'contents' => $content,
            'headings' => $headings,
            'buttons' => $buttons,
            'icon' => $icon,
            'image' => $image,
            'large_icon' => $image,
        ];
    
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . setting('one_signal_key')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        
        $httpStatus == 200;
    
        // Check for invalid player IDs
        // if (isset($responseData['errors']['invalid_player_ids'])) {
    
        //     // Filter invalid player IDs
        //     $validPlayerIds = self::filterValidPlayerIds($player_ids, $responseData['errors']['invalid_player_ids']);
    
        //     if (!empty($validPlayerIds)) {

        //         $fields = [
        //             'app_id' => setting('one_signal_app_id'),
        //             'include_player_ids' => $validPlayerIds,
        //             'data' => $dataArr,
        //             'big_picture' => $image,
        //             'contents' => $content,
        //             'headings' => $headings,
        //             'buttons' => $buttons,
        //             'icon' => $icon,
        //             'image' => $image,
        //             'large_icon' => $image,
        //         ];
    
        //         $fields = json_encode($fields);
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        //         curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //             'Content-Type: application/json; charset=utf-8',
        //             'Authorization: Basic ' . setting('one_signal_key')
        //         ]);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //         curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //         curl_setopt($ch, CURLOPT_POST, TRUE);
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //         $response = curl_exec($ch);
        //         $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //         curl_close($ch);
        //         $responseData = json_decode($response, true);

        //         if (isset($responseData['errors']) && !empty($responseData['errors'])) {
        //             $errorMessage = $responseData['errors'][0];
        //             if (strpos($errorMessage, 'Access denied') === 0) {
        //               $httpStatus = 200;
        //             }
        //         }
        //     }
        // }
        return $httpStatus;
    }
    
    
    // This is for send firebase notifications
    public static function sendFcmNotificationManually($title, $description, $image, $id, $fcm_tokens)
    {
        $fcm_tokens = array_unique($fcm_tokens);

        $cleanedDescription = strip_tags($description);
        $description = mb_substr($cleanedDescription, 0, 100, 'UTF-8');

        $projectId = setting('fcm_project_id');
        $credentialsFilePath = Storage::path('/json/firebase_credentials.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json',
        ];

        $anySuccess = false;
        $messageArr = [];

        foreach ($fcm_tokens as $fcm_token) {
            if(!in_array($fcm_token,$messageArr)){
                array_push($messageArr,$fcm_token);
                $data = [
                    "message" => [
                        "token" => $fcm_token,
                        // "notification" => [
                        //     "title" => $title,
                        //     "body" => $description,
                        //     "image" => $image // Global image field for all platforms
                        // ],
                        // Data for send params
                        "data" => [
                            "post_id" => (string)$id,  // Cast post_id to string
                            "title" => $title,
                            "body" => $description,
                            "image" => $image
                        ],
                        // Android-specific notification with image
                        // "android" => [
                        //     "notification" => [
                        //         "image" => $image,
                        //     ]
                        // ],
                        // iOS-specific notification with image
                        "apns" => [
                            "payload" => [
                                "aps" => [
                                    "mutable-content" => 1,
                                    "imageUrl" => $image,
                                    "post_id" => (string)$id,  // Cast post_id to string
                                    'image' => $image,
                                    'large_icon' => $image,
                                    'big_picture' => $image,
                                ],
                            ],
                            "fcm_options" => [
                                "image" => $image, // This is where iOS gets the image
                            ]
                        ],
                        // WebPush-specific notification with image
                        // "webpush" => [
                        //     "notification" => [
                        //         "image" => $image,
                        //     ]
                        // ]
                    ]
            ];
                $payload = json_encode($data);
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $response = curl_exec($ch);
    
                if (!curl_errno($ch)) {
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($httpCode === 200) {
                        $anySuccess = true;
                    }
                }
    
                    curl_close($ch);
            }
        }

        if ($anySuccess) {
            return 200;
        } else {
            return 500;
        }
    }



    /**
     * function for send email
     */
    public static function sendEmail($template, $data, $toEmail, $toName, $subject, $fromName = '', $fromEmail = '',$attachment = '') {
        if ($fromEmail != '') {
            $fromEmail = setting('username');
        }else{
            $fromName = setting('from_name');
        }
        try {

            $data = \Mail::send($template, $data, function ($message) use($toEmail, $toName, $subject, $data, $fromName, $fromEmail, $attachment) {
                $message->to($toEmail, $toName);
                $message->subject($subject);
                if ($fromEmail != '' && $fromName != '') {
                    $message->from($fromEmail, $fromName);
                }
                if($attachment != ''){
                    $message->attach($attachment);
                }
            });
            return 1;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public static function getValueFromKey($key){
        $value = "";
        $setting = Setting::where('key',$key)->first();
        if($setting){
            $value = $setting->value;
        }
        return $value;
    }
    
    /**
     * function to get video id from youtube url
     */
    public static function getVideoIdFromYoutubeUrl($youtube_url) {
        // Regular expression pattern to extract video ID from URL
        $pattern = '/(?<=v=|v\/|embed\/|youtu.be\/|\/v\/|watch\?v=|ytscreeningroom\?v=|embed\/|watch\?feature=player_embedded&v=|%2Fvideos%2F|youtu.be%2F|v%2F)[^#\&\?]*/';
    
        // Use preg_match to find the video ID
        if (preg_match($pattern, $youtube_url, $matches)) {
            return $matches[0];
        } else {
            return null;
        }
    }

    // Get user data on the basis of signup date
    public static function getUserOnTheBasisOfDate()
    {
        $dates = [];
        $users = [];
        $startDate = null;
        $endDate = null;

        if (isset($_GET['date_range']) && $_GET['filter'] == 'custom') {
            $date = explode("to", $_GET['date_range']);
            $startDate = isset($date[0]) ? date("Y-m-d", strtotime(trim($date[0]))) : date("Y-m-01");
            $endDate = isset($date[1]) ? date("Y-m-d", strtotime(trim($date[1]))) : date("Y-m-d");
        } elseif (isset($_GET['filter'])) {
            // Handle predefined filters
            $filter = $_GET['filter'];
            switch ($filter) {
                case 'current_month':
                    $startDate = date("Y-m-01");  // First day of current month
                    $endDate = date("Y-m-d");     // Today
                    break;
                case 'today':
                    $startDate = $endDate = date("Y-m-d"); // Only today's date
                    break;
                case 'last_7_days':
                    $startDate = date("Y-m-d", strtotime("-6 days")); // Include today
                    $endDate = date("Y-m-d");
                    break;
                case 'last_month':
                    $startDate = date("Y-m-01", strtotime("first day of last month"));
                    $endDate = date("Y-m-t", strtotime("last day of last month"));
                    break;
                default:
                    $startDate = date("Y-m-01");
                    $endDate = date("Y-m-d");
                    break;
            }
        } else {
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-d");
        }

        // Generate dates array correctly
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        // Get user counts per date
        foreach ($dates as $date) {
            $userCount = User::where('type', 'user')->whereDate('created_at', $date)->count();
            $users[] = $userCount;
        }

        return [
            'dates' => $dates,
            'users' => $users,
            'highest_count' => count($users) ? max($users) : 0,
            'chart_start_date' => $startDate,
            'chart_end_date' => $endDate,
        ];
    }


    // Get user data on the basis of device type
    public static function getSignupOnTheBasisOfDeviceType() {
        $dates = ['android', 'ios'];
        $users = [];
        $totalUsers = User::where('type', 'user')->where('status', 1)->count();
        $totalCounts = [];

        foreach ($dates as $date) {
            $userCount = User::where('type', 'user')->where('device_type', $date)->count();
            $percentage = ($totalUsers > 0) ? ($userCount / $totalUsers) * 100 : 0;
            array_push($users, number_format($percentage, 2));
            array_push($totalCounts, $userCount);
        }

        return [
            'types' => $dates,
            'users' => $users,
            'totalCounts' => $totalCounts  // Include total user count for each device type
        ];
    }

    // Get user data on the basis of login form
    public static function getSignupOnTheBasisOfLoginForm() {
        $dates = ['google', 'email', 'apple'];
        $users = [];
        $totalCounts = [];
        $totalUsers = User::where('type', 'user')->where('status', 1)->count();

        foreach ($dates as $date) {
            $userCount = User::where('type', 'user')->where('login_from', $date)->count();
            $percentage = ($totalUsers > 0) ? ($userCount / $totalUsers) * 100 : 0;
            array_push($users, number_format($percentage, 2));
            array_push($totalCounts, $userCount);
        }

        return [
            'types' => $dates,
            'users' => $users,
            'totalCounts' => $totalCounts // Include total user count for each login method
        ];
    }


    // Get post data on the basis of date
    public static function getPostViewOnTheBasisOfDate()
    {
        $dates = [];
        $views = [];
        $startDate = null;
        $endDate = null;

        if (isset($_GET['date_range_view']) && $_GET['filter_view'] == 'custom') {
            $date = explode("to", $_GET['date_range_view']);
            $startDate = isset($date[0]) ? date("Y-m-d", strtotime(trim($date[0]))) : date("Y-m-01");
            $endDate = isset($date[1]) ? date("Y-m-d", strtotime(trim($date[1]))) : date("Y-m-d");
        } elseif (isset($_GET['filter_view'])) {
            // Handle predefined filters
            $filter = $_GET['filter_view'];
            switch ($filter) {
                case 'current_month':
                    $startDate = date("Y-m-01");  // First day of current month
                    $endDate = date("Y-m-d");     // Today
                    break;
                case 'today':
                    $startDate = $endDate = date("Y-m-d"); // Only today's date
                    break;
                case 'last_7_days':
                    $startDate = date("Y-m-d", strtotime("-6 days")); // Include today
                    $endDate = date("Y-m-d");
                    break;
                case 'last_month':
                    $startDate = date("Y-m-01", strtotime("first day of last month"));
                    $endDate = date("Y-m-t", strtotime("last day of last month"));
                    break;
                default:
                    $startDate = date("Y-m-01");
                    $endDate = date("Y-m-d");
                    break;
            }
        } else {
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-d");
        }

        // Generate dates array correctly
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        // Get user counts per date
        foreach ($dates as $date) {
            $userCount = BlogAnalytic::where('type', 'view')->whereDate('created_at', $date)->count();
            $views[] = $userCount;
        }

        return [
            'dates' => $dates,
            'views' => $views,
            'highest_count' => count($views) ? max($views) : 0,
            'chart_start_date' => $startDate,
            'chart_end_date' => $endDate,
        ];
    }


    // Get post data on the basis of date
    public static function getPostAnalyticsOnTheBasisOfDate()
    {
        $id = Request::segment(3);
        $filterType = request()->input('filter_type');
        $dateRangeType = request()->input('date_range_type');
        $analyticTypeParam = request()->input('analytic_type', 'view');
    
        // Set default date range (Current Month Start → Today)
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->toDateString();
    
        // Handle custom filters
        if ($filterType === 'custom' && !empty($dateRangeType)) {
            $dateParts = explode("to", trim($dateRangeType));
            if (count($dateParts) === 2) {
                $startDate = Carbon::parse(trim($dateParts[0]))->toDateString();
                $endDate = Carbon::parse(trim($dateParts[1]))->toDateString();
            }
        } elseif ($filterType) {
            switch ($filterType) {
                case 'current_month':
                    $startDate = now()->startOfMonth()->toDateString();
                    break;
                case 'today':
                    $startDate = $endDate = now()->toDateString();
                    break;
                case 'last_7_days':
                    $startDate = now()->subDays(6)->toDateString();
                    break;
                case 'last_month':
                    $startDate = now()->subMonth()->startOfMonth()->toDateString();
                    $endDate = now()->subMonth()->endOfMonth()->toDateString();
                    break;
            }
        }
    
        // 🔹 Ensure Dates Start from 1st and End at Current Date
        $dates = collect();
        $currentDate = Carbon::parse($startDate);
        while ($currentDate->lte(Carbon::parse($endDate))) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }
    
        // 🔹 Fetch analytics data based on type
        $query = ($analyticTypeParam === 'view' || $analyticTypeParam === 'share')
            ? BlogAnalytic::where('blog_id', $id)->where('type', $analyticTypeParam)
            : BlogBookmark::where('blog_id', $id);
    
        // 🔹 Ensure created_at is formatted correctly (Midnight to 11:59:59 PM)
        $analytics = $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
    
        // 🔹 Map data to ensure missing dates are filled with zero
        $analyticType = $dates->map(fn($date) => $analytics[$date] ?? 0)->toArray();
    
        // 🔹 Calculate total count
        $totalCount = array_sum($analyticType);
    
        // 🔹 Determine the highest count
        $highestCount = !empty($analyticType) ? max($analyticType) : 5;
    
        // 🔹 Dynamic Sidebar Counts Calculation
        if ($highestCount < 10) {
            $stepSize = 1;  
        }elseif ($highestCount < 100) {
            $stepSize = 10;  
        } elseif ($highestCount < 500) {
            $stepSize = 50;  
        } elseif ($highestCount < 1000) {
            $stepSize = 100; 
        } else {
            $stepSize = ceil($highestCount / 10); 
        }
    
        // 🔹 Generate Sidebar Counts
        $sidebarCounts = range(0, ceil($highestCount / $stepSize) * $stepSize, $stepSize);
    
        // return Final Data
        return [
            'dates' => $dates->toArray(),
            'analyticTypeParam' => $analyticTypeParam,
            'analyticType' => $analyticType,
            'highest_count' => $highestCount,
            'chart_start_date' => $startDate,
            'chart_end_date' => $endDate,
            'totalCount' => $totalCount,
            'sidebarCounts' => $sidebarCounts,
        ];
    }



    // Get post poll data on the basis of question
    public static function getPostPollAnalyticData() {
        $id = Request::segment(3);
        $blogQ = BlogQuestion::where('blog_id', $id)->with('options')->first();

        // Handle case when no BlogQuestion exists for the given blog_id
        if (!$blogQ) {
            return [
                'types' => [],
                'users' => [],
                'totalCounts' => []
            ];
        }

        $dates = $blogQ->options->pluck('id')->toArray();
        $types = $blogQ->options->pluck('option')->toArray();
        $users = [];
        $totalUsers = BlogAnalytic::where('blog_id', $id)->where('type', 'blog_poll_option')->count();
        $totalCounts = [];

        foreach ($dates as $date) {
            $userCount = BlogAnalytic::where('type', 'blog_poll_option')->where('blog_poll_option_id', $date)->count();
            $percentage = ($totalUsers > 0) ? ($userCount / $totalUsers) * 100 : 0;
            array_push($users, number_format($percentage, 2));
            array_push($totalCounts, $userCount);
        }

        return [
            'types' => $types,
            'users' => $users,
            'totalCounts' => $totalCounts
        ];
    }




    public static function isValidRssUrl($url) {
        try {
            $response = Http::get($url);
            // echo json_encode($response);exit;
            if ($response->successful()) {
                
                $rssContent = $response->body();
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($rssContent);

                if ($xml !== false) {
                    return true; // Valid XML data
                }
                
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getLimiteCMSdDescriptionAdmin($description){
        $maxCharacters = 200;
        if (strlen($description) > $maxCharacters) {
            $shortenedDescription = substr($description, 0, $maxCharacters - 3) . "...";
        } else {
            $shortenedDescription = $description;
        }

        return $shortenedDescription;
    }

    // Get footer content
    public static function getCmsForSite($limit = null)
    {
        $query = CmsContent::query();
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        return $query->get();
    }


    public static function getParticularUserViewCount($user_id,$ad_id){

        $getViewCount = AdAnalytic::where('user_id',$user_id)->where('ad_id',$ad_id)->where('type','view')->groupBy('user_id')->count();
        
        return $getViewCount;
    }
    
    public static function getParticularUserBlogViewCount($user_id,$blog_id){

        $getViewCount = BlogAnalytic::where('user_id',$user_id)->where('blog_id',$blog_id)->where('type','view')->count();
        
        return $getViewCount;
    }
    
    public static function getParticularUserAdViewCount($user_id,$ad_id){

        $getViewCount = AdAnalytic::where('user_id',$user_id)->where('ad_id',$ad_id)->where('type','view')->count();
        
        return $getViewCount;
    }
    
    public static function getParticularUserAdClickCount($user_id,$ad_id){

        $getClickCount = AdAnalytic::where('user_id',$user_id)->where('ad_id',$ad_id)->where('type','click')->count();
        
        return $getClickCount;
    }
    
    public static function getParticularUserShortVideoViewCount($user_id,$short_video_id){

        $getViewCount = ShortVideoAnalytic::where('user_id',$user_id)->where('short_video_id',$short_video_id)->where('type','view')->count();
        
        return $getViewCount;
    }

    
    public static function getParticularBlogPollQuestionOptions($blog_id) {
        $question = '';
        $BlogQuestionOptionArr = '';
        $getBlogQuestion = BlogQuestion::where('blog_id',$blog_id)->first();

        if($getBlogQuestion->question){

            $question_id = $getBlogQuestion->id;
            $BlogQuestionOption = BlogQuestionOption::where('blog_pool_question_id',$question_id)->get();
            if(count($BlogQuestionOption)){
                $BlogQuestionOptionArr = $BlogQuestionOption;
                return $BlogQuestionOptionArr;
            }else{
                  $BlogQuestionOptionArr = [];
            }

        }else{
             $question = [];
        }

        return $question;

    }

    public static function getParticularBlogQuestion($blog_id){
        $question = '';
        $getBlogQuestion = BlogQuestion::where('blog_id',$blog_id)->first();
        if($getBlogQuestion->question){
            $question = $getBlogQuestion->question;
        }else{
             $question = '--';
        }
        return $question;
    }
    
    
    // for convert key
    public static function maskApiKey($key) {
        if($key && strlen($key) >= 5){
            return str_repeat('*', strlen($key) - 5) . substr($key, -5);
        }else{
            return '';
        }
    }
    
    // for get version
     public static function getVersion($filePath)
    {
        return json_decode(file_get_contents($filePath), true)['version'];
    }
    
    // for get language direction
     public static function getLanguageDirection($langCode)
    {
        try{ 
             $lang = Language::where('code',$langCode)->first();
             if($lang){
                 $direction = $lang->position;
             }else{
                 $direction = 'ltr';
             }
             return $direction;
        }
        catch (\Exception $e) {
            return 'ltr';
        }
    }
    
    // =========================News=============================
    public static function getCategoryName($id)
    {
        $data = Category::where('id',$id)->first();
        if($data){
         $name = $data->name;
        }else{
         $name = '--';
        }
        return $name;
    }


    public static function getTableLimitedTitle($title,$maxCharacters){
        if (strlen($title) > $maxCharacters) {
            $shortenedTitle = substr($title, 0, $maxCharacters - 3) . "...";
        } else {
            $shortenedTitle = $title;
        }

        return $shortenedTitle;
    }


    // Admin
    public static function getNotification()
    {
        return AdminNotification::where('is_remove',0)->orderBy('id', 'desc')
            ->limit(200)
            ->get();
    }

    public static function getUnreadNotificationCount()
    {
        return AdminNotification::where('is_read',0)->where('is_remove',0)->count();
    }



}
?>