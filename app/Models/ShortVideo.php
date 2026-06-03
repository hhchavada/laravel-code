<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;   
use Illuminate\Support\Facades\Session;
use DB;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;


class ShortVideo extends Model
{
    use HasFactory;
    use Sortable;

    public $sortable = [
        'title',
        'schedule_date',
    ];


    public function analytics()
    {
        return $this->hasMany(ShortVideoAnalytic::class, 'short_video_id', 'id');
    }


    /**
     * Fetch list of categories from here
    **/
    public static function getLists($search)
    {
        try {
            $obj = new self;

            $pagination = $search['perpage'] ?? config('constant.pagination');

            if (!empty($search['title'])) {
                $obj = $obj->where('title', 'like', '%' . trim($search['title']) . '%');
            }

            if (isset($search['status']) && $search['status'] !== '') {
                $obj = $obj->where('status', $search['status']);
            }

            if (!empty($search['from_date']) && !empty($search['to_date'])) {
                $obj = $obj->whereBetween('schedule_date', [
                    $search['from_date'],
                    $search['to_date']
                ]);
            } elseif (!empty($search['from_date'])) {
                $obj = $obj->where('schedule_date', '>=', $search['from_date']);
            } elseif (!empty($search['to_date'])) {
                $obj = $obj->where('schedule_date', '<=', $search['to_date']);
            }

            // ADD VIEW COUNT ONLY
            $data = $obj->withCount([
                    'analytics as views_count' => function ($q) {
                        $q->where('type', 'view');
                    }
                ])
                ->sortable(['schedule_date' => 'DESC'])
                ->paginate($pagination)
                ->appends('perpage', $pagination);

            return $data;

        } catch (\Exception $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            ];
        }
    }


    /**
     * Add or update data
    **/
    public static function addUpdate($data, $id=0)
    {

        try {
            $obj = new self;
            unset($data['_token']);
            $random = Str::random(5);
            $button_name = '';
            $setting = Setting::where('key','default_storage')->first();

            // Handle video upload and URL setting
            $videoName = '';
            if (isset($data['video_type']) && $data['video_type'] !== '') {
                if ($data['video_type'] == 'video') {
                    if (isset($data['video_file']) && $data['video_file'] !== '') {
                        $videoFile = $data['video_file'];
                        if ($videoFile->isValid()) {

                            if($setting == 'local_storage'){
                                $videoExtension = $videoFile->getClientOriginalExtension();
                                $videoName = 'Video_' . $random . '.' . $videoExtension;
                                $videoPath = 'uploads/short_video/videos/' . $videoName;

                                // Upload to local with public visibility
                                $data['video_file'] = $videoPath;
                            }else{
                                $videoExtension = $videoFile->getClientOriginalExtension();
                                $videoName = 'Video_' . $random . '.' . $videoExtension;
                                $videoPath = 'Uploads/blogit/' . $videoName;

                                // Upload to S3 with public visibility
                                Storage::disk('s3')->put($videoPath, file_get_contents($videoFile), 'public');
                                $data['video_file'] = Storage::disk('s3')->url($videoPath);
                            }
                        } else {
                            return ['status' => false, 'message' => __('lang.message_upload_file_not_valid')];
                        }
                    }
                    $data['video_url'] = null;
                } else {
                    $data['video_file'] = null;
                }
                unset($data['video_type']);
            }else {
                unset($data['video_type']);
            }

            
            if(isset($data['background_image']) && $data['background_image']!=''){
                $uploadImage = \Helpers::uploadFiles($data['background_image'],'short_video/');
                if($uploadImage['status']==true){
                    $data['background_image'] = $uploadImage['file_name'];
                }
            }

            if(isset($data['description'])){
                $data['description'] = $data['description'];
            }else{
               $data['description'] ='.';
            }

            if($id==0)
            {                
                if(isset($data['button_name']) && $data['button_name']!=''){
                    $button_name = $data['button_name'];
                    if($button_name=='Draft'){
                        $data['status'] = 2;
                    }else if($button_name=='Submit'){
                        $data['status'] = 3;
                    }else if($button_name=='Publish'){
                        $data['status'] = 1;
                    }
                    unset($data['button_name']);
                }else{
                    unset($data['button_name']);
                } 
                if(isset($data['schedule_date']) && $data['schedule_date']!=''){
                    if(date("Y-m-d H:i:s",strtotime($data['schedule_date'])) > date("Y-m-d H:i:s")){
                        $data['status'] = 4;
                    }
                    $data['schedule_date'] = date("Y-m-d H:i:s",strtotime($data['schedule_date']));
                }else{
                    $data['schedule_date'] = date("Y-m-d H:i:s");
                }     
  
                $data['created_by'] = Auth::user()->id;
                $data['created_at'] = date('Y-m-d H:i:s'); 
                $slug = \Helpers::createSlug($data['title'],'blog',$id,false);
                $data['slug'] = $slug;
                $entry_id = $obj->insertGetId($data);

                $languages = Language::where('status',1)->get();
                foreach ($languages as $language) 
                {
                    $translation = array(
                        'short_video_id' =>$entry_id,
                        'language_code' =>$language->code,
                        'title' =>$data['title'],
                        'description' =>$data['description'],
                        'created_at' =>date("Y-m-d H:i:s"),
                    );
                    ShortVideoTranslation::insert($translation);
                }
                
                return ['status' => true, 'message' => __('lang.message_success_add')];
            }
            else
            {
                $button_name = "";
      
                if(isset($data['button_name']) && $data['button_name']!=''){
                    $button_name = $data['button_name'];
                    if($button_name=='Submit'){
                        $data['status'] = 3;
                    }else if($button_name=='Publish'){
                        $data['status'] = 1;
                    }
                    unset($data['button_name']);
                }else{
                    unset($data['button_name']);
                } 
                
                if(isset($data['schedule_date']) && $data['schedule_date']!=''){
                    if(date("Y-m-d H:i:s",strtotime($data['schedule_date'])) > date("Y-m-d H:i:s")){
                        $data['status'] = 4;
                    }else if(date("Y-m-d H:i:s",strtotime($data['schedule_date'])) < date("Y-m-d H:i:s")){
                        $blogData = $obj->where('id',$id)->first();
                        if($blogData->status == 4){
                            $data['status'] = 1;
                        }
                    }
                    $data['schedule_date'] = date("Y-m-d H:i:s",strtotime($data['schedule_date']));
                }else{
                    $data['schedule_date'] = date("Y-m-d H:i:s");
                }  

                if(isset($data['created_at']) && $data['created_at']!=''){
                    $data['created_at'] = date("Y-m-d H:i:s",strtotime($data['created_at']));
                }

                $obj->where('id',$id)->update($data);


                $languages = Language::where('status',1)->get();
                foreach ($languages as $language) 
                {
                    $translate = ShortVideoTranslation::where('language_code',$language->code)->where('short_video_id',$id)->first();
                    $translation = array(
                        'short_video_id' =>$id,
                        'language_code' =>$language->code,
                        'title' =>$data['title'],
                        'description' =>$data['description'],
                        'created_at' =>date("Y-m-d H:i:s"),
                    );
                    if($translate){
                        $translation['updated_at'] = date("Y-m-d H:i:s");
                        ShortVideoTranslation::where('id',$translate->id)->update($translation);
                    }else{
                        $translation['created_at'] = date("Y-m-d H:i:s");
                        ShortVideoTranslation::insert($translation);
                    }
                }

                return ['status' => true, 'message' => __('lang.message_success_update')];
            }
        } 
        catch (\Exception $e) 
        {
            dd($e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile());
        }
    }

    /**
     * Fetch particular detail
    **/
    public static function getDetail($id)
    {
        try 
        {
            $obj = new self;
            $data = $obj->where('id',$id)->firstOrFail();
            return $data;
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Delete particular epaper
    **/
    public static function deleteRecord($id) 
    {
        try 
        {
            $obj = new self;  

            // Retrieve the record
            $record = $obj->find($id);
            if (!$record) {
                return ['status' => false, 'message' => __('lang.message_data_not_found')];
            }

            // Get the video file URL
            $videoUrl = $record->video_file;

            // Delete the video file from S3
            if ($videoUrl) {
                // Extract the S3 path from the full URL
                $s3Path = str_replace(Storage::disk('s3')->url(''), '', $videoUrl);
                Storage::disk('s3')->delete($s3Path);
            }

            // Delete the record from the database
            $obj->where('id', $id)->delete();

            $obj->where('id',$id)->delete();   
            return ['status' => true, 'message' => __('lang.message_success_delete')];
        }
        catch (\Exception $e) 
        {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }
    
    /**
     * Update Columns 
    **/
    public static function changeStatus($value, $id)
    {
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
     * Fetch languages trans
    **/
    public static function getTranslation($id){
        try {
            $rowObj = new self;
            $rowObj = $rowObj->where('id',$id)->first();
            $data = Language::where('status',1)->get();
            if($rowObj){
                foreach ($data as $row) {
                    $row->details = ShortVideoTranslation::where('short_video_id',$id)->where('language_code',$row->code)->first();
                    if(!$row->details){
                        $postData = array(
                            'short_video_id' => $id,
                            'language_code' =>$row->code,
                            'title' =>$row->title,
                            'description' =>$row->description,
                            'created_at' => date("Y-m-d H:i:s")
                        );
                        ShortVideoTranslation::insert($postData);
                        $row->details = ShortVideoTranslation::where('short_video_id',$id)->where('language_code',$row->code)->first();
                    }
                }
            }
            return $data;
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }

    /**
     * Add or update trans
    **/
    public static function updateTranslation($data,$id=0) {
        try {
            $obj = new self;            
            for ($i=0; $i < count($data['language_code']); $i++) { 
                if($data['language_code'][$i] == 'en'){
                    $updateData = array(
                        'title' =>$data['title'][$i],
                        'description' =>$data['description'][$i],
                    );
                    $obj->where('id',$id)->update($updateData);
                }
                $postData = array(
                    'language_code' => $data['language_code'][$i],
                    'title' =>$data['title'][$i],
                    'description' =>$data['description'][$i],
                    'updated_at' => date("Y-m-d H:i:s")
                );

                ShortVideoTranslation::where('id',$data['translation_id'][$i])->update($postData);
            }
            return ['status' => true, 'message' => __('lang.message_success_update')];
        }
        catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage() . ' '. $e->getLine() . ' '. $e->getFile()];
        }
    }
}
