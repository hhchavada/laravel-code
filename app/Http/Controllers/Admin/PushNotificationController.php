<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\DeviceToken;
use App\Models\PushNotification;
use DB;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    /**
     * Display a listing of the blog.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        try{
            $data['result'] = PushNotification::getLists($request->all());
            return view('admin.push-notification.index',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }
    /**
     * Show the form for creating a new notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $data['emails'] = User::where('type','user')->where('status',1)->get();
            return view('admin/push-notification.create',$data);
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Send newly created notification to the users.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $player_id = array();
            $fcm_token = array();
            $user_id = array();
            $post = $request->all();

            if(isset($post['image']) && $post['image']!=''){
                $uploadImage = \Helpers::uploadFiles($post['image'],'push-notification/'); 
                if($uploadImage['status']==true){
                    $post['image'] = $uploadImage['file_name'];
                }
            }
            
            if(setting('enable_os_notifications') == 1){
                if(setting('one_signal_key')==''){
                    return redirect()->back()->with('error',__('lang.message_one_signal_key_not_found'));
                }else{
                    if($post['send_to']=='specific_user'){
                        if(!isset($post['email'])){
                           return redirect()->back()->with('error',__('lang.message_required_message')); 
                        }
                        $deviceToken = DeviceToken::whereIn('user_id',$post['email'])->get();
                        if(count($deviceToken)){
                            foreach($deviceToken as $deviceToken_data){
                                if($deviceToken_data->player_id!='' || $deviceToken_data->player_id!=null){
                                    if(!in_array($deviceToken_data->player_id,$player_id)){
                                        array_push($player_id,$deviceToken_data->player_id);
                                    }
                                }
                            }
                        }
                        $users = User::whereIn('id',$post['email'])->where('status',1)->get();
                        if(count($users)){
                            foreach($users as $users_data){
                                if(!in_array($users_data->id,$user_id)){
                                    array_push($user_id,$users_data->id);
                                }
                            }
                        }
                    }else{
                        if($post['send_to']=='only_guest'){
                            $deviceToken = DeviceToken::where('user_id',0)->get();
                            $users = User::where('status',1)->get(); 
                        }else if($post['send_to']=='all_user_without_guest'){
                            $deviceToken = DeviceToken::where('user_id','!=',0)->get();
                            $users = User::where('status',1)->get();
                        }else{
                            $deviceToken = DeviceToken::all();
                            $users = User::where('status',1)->get();                    
                        }
                        if(count($deviceToken)){
                            foreach($deviceToken as $deviceToken_data){
                                if($deviceToken_data->player_id!='' || $deviceToken_data->player_id!=null){
                                    if(!in_array($deviceToken_data->player_id,$player_id)){
                                        array_push($player_id,$deviceToken_data->player_id);
                                    }
                                }
                            }
                        }
                        if(count($users)){
                            foreach($users as $users_data){
                                if(!in_array($users_data->id,$user_id)){
                                    array_push($user_id,$users_data->id);
                                }
                            }
                        }
                    }
                    if($post['image']){
                        $image = url('/uploads/push-notification/'.$post['image']);
                    }elseif(file_exists(public_path()."/uploads/setting/".setting('app_logo')))
                    { 
                        $image = url('/uploads/setting/'.setting('app_logo'));
                    }else{
                        $image = url('uploads/no-image.png');
                    }
                    if(count($player_id)){
                        $push_notification = array(
                            'send_to'=>$post['send_to'], 
                            'title'=>$post['title'], 
                            'description'=>$post['description'],
                            'image' => $post['image'],
                            'created_at' =>date("Y-m-d H:i:s"),
                        );
                        if(count($user_id)){
                            $push_notification['user_id'] = implode(',',$user_id);
                        }
                        $id = PushNotification::insertGetId($push_notification);
                        $status = \Helpers::sendOneSignalNotificationManually($post['title'],$post['description'],$image,$id,$player_id);
                        if ($status === 200) {
                            return redirect()->back()->with('success', __('lang.message_notification_sent_successfully')); 
                        } else {
                            return redirect()->back()->with('success', __('lang.message_notification_sent_successfully'));
                        }
                    }else{
                        return redirect()->back()->with('error', __('lang.message_error_does_not_have_any_player_id'));
                    }
                }
            }else if(setting('enable_firebase_notifications') == 1){
                if(setting('fcm_project_id')==''){
                    return redirect()->back()->with('error',__('lang.message_fcm_project_id_not_found'));
                }else{
                    if($post['send_to']=='specific_user'){
                        $deviceToken = DeviceToken::whereIn('user_id',$post['email'])->get();
                        if(count($deviceToken)){
                            foreach($deviceToken as $deviceToken_data){
                                if($deviceToken_data->fcm_token!='' || $deviceToken_data->fcm_token!=null){
                                    if(!in_array($deviceToken_data->fcm_token,$fcm_token)){
                                        array_push($fcm_token,$deviceToken_data->fcm_token);
                                    }
                                }
                            }
                        }
                        $users = User::whereIn('id',$post['email'])->where('status',1)->get();
                        if(count($users)){
                            foreach($users as $users_data){
                                if(!in_array($users_data->id,$user_id)){
                                    array_push($user_id,$users_data->id);
                                }
                            }
                        }
                    }else{
                        if($post['send_to']=='only_guest'){
                            $deviceToken = DeviceToken::where('user_id',0)->get();
                            $users = User::where('status',1)->get(); 
                        }else if($post['send_to']=='all_user_without_guest'){
                            $deviceToken = DeviceToken::where('user_id','!=',0)->get();
                            $users = User::where('status',1)->get();
                        }else{
                            $deviceToken = DeviceToken::all();
                            $users = User::where('status',1)->get();                    
                        }
                        if(count($deviceToken)){
                            foreach($deviceToken as $deviceToken_data){
                                if($deviceToken_data->fcm_token!='' || $deviceToken_data->fcm_token!=null){
                                    if(!in_array($deviceToken_data->fcm_token,$fcm_token)){
                                        array_push($fcm_token,$deviceToken_data->fcm_token);
                                    }
                                }
                            }
                        }
                        if(count($users)){
                            foreach($users as $users_data){
                                if(!in_array($users_data->id,$user_id)){
                                    array_push($user_id,$users_data->id);
                                }
                            }
                        }
                    }
                    if($post['image']){
                        $image = url('/uploads/push-notification/'.$post['image']);
                    }elseif(file_exists(public_path()."/uploads/setting/".setting('app_logo')))
                    { 
                        $image = url('/uploads/setting/'.setting('app_logo'));
                    }else{
                        $image = url('uploads/no-image.png');
                    }
                    if(count($fcm_token)){
                        $push_notification = array(
                            'send_to'=>$post['send_to'], 
                            'title'=>$post['title'], 
                            'description'=>$post['description'],
                            'image' => $post['image'],
                            'created_at' =>date("Y-m-d H:i:s"),
                        );
                        if(count($user_id)){
                            $push_notification['user_id'] = implode(',',$user_id);
                        }
                        $id = PushNotification::insertGetId($push_notification);
                        $status = \Helpers::sendFcmNotificationManually($post['title'],$post['description'],$image,$id,$fcm_token);
                        if ($status === 200) {
                            return redirect()->back()->with('success', __('lang.message_notification_sent_successfully')); 
                        } else {
                            return redirect()->back()->with('success', __('lang.message_notification_sent_successfully'));
                        }
                    }else{
                        return redirect()->back()->with('error', __('lang.message_error_does_not_have_any_fcm_token'));
                    }
                }
            }else{
                return redirect()->back()->with('error', __('lang.please_enable_notification_toggle_first'));
            }
        }
        catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage() . ' '. $ex->getLine() . ' '. $ex->getFile()); 
        }
    }

    /**
     * Remove the specified resource.
     * @param  Request $request
     * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        try{
            $deleted = PushNotification::deleteRecord($id);
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

    public function deleteSelected(Request $request)
    {
        $selectedIdsString = $request->input('selectedIds');
        
        $selectedIds = explode(',', $selectedIdsString);

        // Delete
        PushNotification::whereIn('id', $selectedIds)->delete();

        return redirect()->back()->with('success', __('lang.message_bulk_push_notification_delete_successfully'));
    }
}
