<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShortVideo;
use App\Models\ShortVideoAnalytic;
use App\Models\ShortVideoTranslation;
use DB;
use App\Services\YoutubeService;
use App\Services\YoutubeApiClient;
use App\Services\WatchPage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;

class ShortVideoController extends Controller
{
    protected $youtubeClient;

    public function __construct(YoutubeApiClient $youtubeClient)
    {
        $this->youtubeClient = $youtubeClient;
    }

    // Method to fetch client HTTP data
    public function getClientHttp()
    {
        $videoId = 'dbeCNiBuv3Q';
        $client = $this->youtubeClient->getVideoData($videoId);
        $watchPage = $this->watch($videoId);
        $finalRes = $this->getPlayerResponse($videoId, $client, $watchPage);
        echo '<pre>';
        print_r($finalRes);
        echo '</pre>';
    }

    // Function to handle player response
    public function getPlayerResponse($videoId, $client, $watchPage)
    {
        $payload = $client['payload'];

        if (!isset($payload['context'])) {
            throw new \Exception('Client must contain a context');
        }

        if (!isset($payload['context']['client'])) {
            throw new \Exception('Client must contain a context.client');
        }

        $userAgent = $payload['context']['client']['userAgent'] ?? null;
        $ytCfg = $this->getYtCfg($watchPage->getBody());

        // Prepare the body for the POST request
        $body = array_merge($payload, [
            'videoId' => $videoId,
        ]);

        // Add playbackContext if STS exists in ytCfg
        if (isset($ytCfg['STS'])) {
            $body['playbackContext'] = [
                'contentPlaybackContext' => [
                    'html5Preference' => 'HTML5_PREF_WANTS',
                    'signatureTimestamp' => (string) $ytCfg['STS']
                ]
            ];
        }

        
        $watchPageCookies = $watchPage->getCookies();
        
        $formattedCookies = implode('; ', array_map(
            fn($key, $value) => $key . '=' . $value,
            array_keys($watchPageCookies),
            $watchPageCookies
        ));

        // Prepare the headers for the POST request
        $headers = [
            'User-Agent' => $userAgent ?? '',
            'X-Youtube-Client-Name' => $payload['context']['client']['clientName'],
            'X-Youtube-Client-Version' => $payload['context']['client']['clientVersion'],
            'X-Goog-Visitor-Id' => $ytCfg['INNERTUBE_CONTEXT']['client']['visitorData'],
            'Origin' => 'https://www.youtube.com',
            'Sec-Fetch-Mode' => 'navigate',
            'Content-Type' => 'application/json',
            'Cookie' => $formattedCookies,
        ];

        if (isset($client['headers'])) {
            $headers = array_merge($headers, $client['headers']);
        }

        // Make the POST request
        $response = Http::withHeaders($headers)->post($client['apiUrl'], $body);
        echo "<pre>";
        print_r($response->body());
        exit;
        if ($response->successful()) {
            return $response->body();
        } else {
            throw new \Exception('Failed to fetch player response');
        }
    }

    // Watch method for fetching watch page information
    public function watch($videoId)
    {
        $url = 'https://www.youtube.com/watch?v=' . $videoId . '&bpctr=9999999999&has_verified=1&hl=en';
        $defaultCookies = 'PREF=hl=en&tz=UTC; SOCS=CAI; GPS=1';
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.18 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-us,en;q=0.5',
            'Sec-Fetch-Mode' => 'navigate',
            'Cookie' => $defaultCookies,
        ];
    
        try {
    
            $httpClient = new \GuzzleHttp\Client();
            $response = $httpClient->get($url, [
                'headers' => $headers,
            ]);
    
            $cookieHeader = $response->getHeader('set-cookie');
            $cookies = [];
    
            foreach ($cookieHeader as $header) {
                preg_match_all('/(?:^|,)(\w.+?)=(.*?);/', $header, $matches);
                foreach ($matches[1] as $index => $cookieName) {
                    $cookies[$cookieName] = $matches[2][$index];
                }
            }
    
            // Add default cookies
            $cookies = array_merge($cookies, [
                'PREF' => 'hl=en',
                'SOCS' => 'CAI',
                'GPS' => '1',
            ]);
    
            // Parse the response body
            $result = WatchPage::parse((string) $response->getBody(), $videoId, $cookies);
    
            if (!$result->isOk) {
                throw new \Exception('Video watch page is broken.');
            }
    
            if (!$result->isVideoAvailable) {
                throw new \Exception('Video is unavailable: ' . $videoId);
            }
    
            return $result;
        } catch (RequestException $e) {
            Log::error('Error fetching video: ' . $e->getMessage());
            throw new \Exception('Failed to fetch video data.');
        }
    }
    
    public function getYtCfg($root)
    {
        preg_match('/ytcfg\.set\(\s*({.+?})\s*\)\s*;/i', $root, $matches);
        return json_decode($matches[1], true);
    }




    /**
     * Display a listing of the blog.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $data['result'] = ShortVideo::getLists($request->all());

        return view('admin.short_video.index',$data);
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('admin.short_video.create');
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
        $added = ShortVideo::addUpdate($request->all());
        if($added['status']==true){
            return redirect('admin/short-video')->with('success', $added['message']); 
        }
        else{
            return redirect()->back()->with('error', $added['message']);
        } 
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  type $type, id  $id 
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {        
        try {
            $data['row'] = ShortVideo::getDetail($id);
           
            return view('admin.short_video.edit',$data);
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $updated = ShortVideo::addUpdate($request->all(),$request->input('id'));
            if($updated['status']==true){
                return redirect('admin/short-video')->with('success', $updated['message']);
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
            $deleted = ShortVideo::deleteRecord($id);
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
    public function updateColumn($id,$status)
    {
        try{
            $updated = ShortVideo::changeStatus($status,$id);
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
            $data['detail'] = ShortVideo::getDetail($id);
            $data['languages'] = ShortVideo::getTranslation($id);

            return view('admin/short_video.translation',$data);
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
    public function updateTranslation(Request $request,$id)
    {
        $translationUpdated = ShortVideo::updateTranslation($request->all(),$id);
        if($translationUpdated['status']==true){
            return redirect('admin/short-video')->with('success', $translationUpdated['message']); 
        }
        else{
            return redirect()->back()->with('error', $translationUpdated['message']);
        } 
    }
    
    
    // Analytics
    public function analytics(Request $request,$id)
    {
        try{
            $pagination = (isset($search['perpage']))?$search['perpage']:config('constant.pagination');
            $data['shares'] = ShortVideoAnalytic::where('type','share')->where('short_video_id',$id)->with('user')->paginate($pagination)->appends('perpage', $pagination);
            $data['totalShortVideoViewsCount'] = ShortVideoAnalytic::where('type','view')->where('short_video_id',$id)->count();
            $data['totalGuestShortVideoViewsCount'] = ShortVideoAnalytic::where('type','view')->where('short_video_id',$id)->where('user_id',0)->count();
            
            // Fetch views and remove duplicates based on user_id or player_id
            $data['uniqueViewsCount'] = ShortVideoAnalytic::where('type', 'view')
                ->where('short_video_id', $id)
                ->where('user_id','!=',0)
                ->with('user')
                ->get()
                ->unique(function ($item) {
                    return $item['user_id'] == 0 ? $item['fcm_token'] : $item['user_id'];
                });
                
            
            // New one for view analytics
            $checkFcmTokenArr = [];
            $checkUserIdArr = [];
            $finalIds = [];
            $views = ShortVideoAnalytic::where('type','view')->where('short_video_id',$id)->where('user_id','!=',0)->orderBy('id','DESC')->get();
            if(count($views) > 0){
                foreach($views as $view){
                    if(in_array($view->fcm_token, $checkFcmTokenArr)){
                        continue;
                    }
    
                    if(in_array($view->user_id, $checkUserIdArr)){
                        continue;
                    }
    
                    if($view->user_id == 0 && $view->fcm_token !=''){
                        array_push($checkFcmTokenArr, $view->fcm_token);
                    }
    
                    if($view->user_id != 0){
                        array_push($checkUserIdArr, $view->user_id);
                    }
                    
                    array_push($finalIds,$view->id);
                }
            }
            
            $data['views'] = ShortVideoAnalytic::where('type','view')->where('short_video_id',$id)->whereIn('id',$finalIds)->with('user')->orderBy('id','DESC')->paginate($pagination)->appends('perpage', $pagination);
            
            return view('admin.short_video.analytics',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }


    public function deleteSelected(Request $request)
    {
        $selectedIdsString = $request->input('selectedIds');
        
        $selectedIds = explode(',', $selectedIdsString);

        // Delete
        ShortVideo::whereIn('id', $selectedIds)->delete();

        return redirect()->back()->with('success', __('lang.message_bulk_short_video_delete_successfully'));
    }


    public function changeStatusViaList(Request $request)
    {
        $video = ShortVideo::findOrFail($request->id);

        // Schedule → no action
        if ($video->status == 4) {
            return response()->json([
                'status' => false,
                'message' => __('lang.admin_scheduled')
            ]);
        }

        switch ($video->status) {
            case 2: // draft → submit
                $video->status = 3;
                break;

            case 3: // submit → publish
                $video->status = 1;
                break;

            case 1: // publish → unpublish
                $video->status = 0;
                break;

            case 0: // unpublish → publish
                $video->status = 1;
                break;
        }

        $video->save();

        return response()->json([
            'status' => true,
            'message' => __('lang.admin_status_updated')
        ]);
    }


}
