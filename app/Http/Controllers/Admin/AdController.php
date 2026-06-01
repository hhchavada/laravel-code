<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\Language;
use App\Models\AdAnalytic;
use App\Http\Requests\Ad\StoreAdRequest;
use App\Http\Requests\Ad\UpdateAdRequest;
use App\Http\Requests\Ad\UpdateAdTranslationRequest;

class AdController extends Controller
{
    /**
     * Display a listing of the entry.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        try{
            $data['result'] = Ad::getLists($request->all());
            return view('admin.ad.index',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Show the form for creating a new entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('admin.ad.create');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAdRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdRequest $request)
    {
        try{
            $validated = $request->validated();
            $added = Ad::addUpdate($request->all());
            if($added['status']==true){
                return redirect('admin/ads')->with('success', $added['message']); 
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
     * @param  id  $id 
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $data['row'] = Ad::getDetail($id);
            return view('admin.ad.edit',$data);
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdRequest $request)
    {
        try{
            $validated = $request->validated();
            $updated = Ad::addUpdate($request->all(),$request->input('id'));
            if($updated['status']==true){
                return redirect('admin/ads')->with('success', $updated['message']); 
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
            $deleted = Ad::deleteRecord($id);
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
            $updated = Ad::changeStatus($status,$id);
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
     * Update order storage.
     *
     * @param  \App\Http\Requests\UpdateAdRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function sorting(UpdateAdRequest $request)
    {
        try{
            $posts = Ad::all();
            foreach ($posts as $post) {
                foreach ($request->order as $order) {
                    if ($order['id'] == $post->id) {
                        $c = Ad::where('id',$post->id)->update(['order' => $order['position']]);                        
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
     * Display a listing of the ads analytics.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function analytics(Request $request,$id)
    {
        try{
            $pagination = (isset($search['perpage']))?$search['perpage']:config('constant.pagination');
            $data['totalAdsViewsCount'] = AdAnalytic::where('type','view')->where('ad_id',$id)->count();
            $data['totalGuestAdsViewsCount'] = AdAnalytic::where('type','view')->where('ad_id',$id)->where('user_id',0)->count();
            $data['totalAdsClicksCount'] = AdAnalytic::where('type','click')->where('ad_id',$id)->count();
            $data['totalGuestAdsClicksCount'] = AdAnalytic::where('type','click')->where('ad_id',$id)->where('user_id',0)->count();
            
            // Fetch views and remove duplicates based on user_id or fcm_token
            $data['uniqueViewsCount'] = AdAnalytic::where('type', 'view')
                ->where('ad_id', $id)
                ->where('user_id','!=',0)
                ->with('user')
                ->get()
                ->unique(function ($item) {
                    return $item['user_id'] == 0 ? $item['fcm_token'] : $item['user_id'];
                });
    
            // Fetch clicks and remove duplicates based on user_id or fcm_token
            $data['uniqueClicksCount'] = AdAnalytic::where('type', 'click')
                ->where('ad_id', $id)
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
            $views = AdAnalytic::where('type','view')->where('ad_id',$id)->where('user_id','!=',0)->orderBy('id','DESC')->get();
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
            
            $data['views'] = AdAnalytic::where('type','view')->where('ad_id',$id)->whereIn('id',$finalIds)->with('user')->orderBy('id','DESC')->paginate($pagination)->appends('perpage', $pagination);
            
            
            // New one for click analytics
            $checkFcmTokenArrClick = [];
            $checkUserIdArrClick = [];
            $finalIdsClick = [];
            $clicks = AdAnalytic::where('type','click')->where('ad_id',$id)->where('user_id','!=',0)->orderBy('id','DESC')->get();
            if(count($clicks) > 0){
                foreach($clicks as $click){
                    if(in_array($click->fcm_token, $checkFcmTokenArrClick)){
                        continue;
                    }
    
                    if(in_array($click->user_id, $checkUserIdArrClick)){
                        continue;
                    }
    
                    if($click->user_id == 0 && $click->fcm_token !=''){
                        array_push($checkFcmTokenArrClick, $click->fcm_token);
                    }
    
                    if($click->user_id != 0){
                        array_push($checkUserIdArrClick, $click->user_id);
                    }
                    
                    array_push($finalIdsClick,$click->id);
                }
            }
            
            $data['clicks'] = AdAnalytic::where('type','click')->where('ad_id',$id)->whereIn('id',$finalIdsClick)->with('user')->orderBy('id','DESC')->paginate($pagination)->appends('perpage', $pagination);
            
            return view('admin.ad.analytics',$data);
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
        Ad::whereIn('id', $selectedIds)->delete();

        return redirect()->back()->with('success', __('lang.message_bulk_ads_delete_successfully'));
    }
}
