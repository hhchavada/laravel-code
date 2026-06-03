@foreach($result as $row)

@if($row->key == 'enable_os_notifications')
<div class="col-md-12 mb-3 display-inline-block mr-10">
    <label class="switch switch-square">
        <input value="1" type="checkbox" class="switch-input" id="enable_os_notifications" name="enable_os_notifications" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_enable_os_notifications_placeholder')}}</span>
    </label>
</div>
@endif

@if($row->key == 'one_signal_key')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="one_signal_key">{{__('lang.admin_one_signal_key')}}</label>
    <input type="text" class="form-control" value="{{ \Helpers::maskApiKey($row->value) }}" name="one_signal_key" placeholder="{{__('lang.admin_one_signal_key_placeholder')}}">
</div>
@endif
@if($row->key == 'one_signal_app_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="one_signal_app_id">{{__('lang.admin_one_signal_app_id')}}</label>
    <input type="text" class="form-control" value="{{ \Helpers::maskApiKey($row->value) }}" name="one_signal_app_id" placeholder="{{__('lang.admin_one_signal_app_id_placeholder')}}">
</div>
@endif
@if($row->key == 'enable_firebase_notifications')
<div class="col-md-12 mb-3 display-inline-block mr-10">
    <label class="switch switch-square">
        <input value="1" type="checkbox" class="switch-input" id="enable_firebase_notifications" name="enable_firebase_notifications" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_enable_firebase_notifications_placeholder')}}</span>
    </label>
</div>
@endif
@if($row->key == 'fcm_project_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="fcm_project_id">{{__('lang.admin_fcm_project_id')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" name="fcm_project_id" placeholder="{{__('lang.admin_fcm_project_id_placeholder')}}">
</div>
@endif
@if($row->key == 'fcm_project_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label style="float:none;" class="form-label" for="upload-json">{{__('lang.admin_firebase_credential_json')}}( Required for notification)</label>
    <div class="mb-1">
        <div class="col-12 d-flex px-0">
            <label class="me-75 mb-0" for="upload-json">
                <input type="file" name="firebase_json_file" id="upload-json" class="form-control" accept=".json">
                @if(Storage::exists('json/firebase_credentials.json'))
                    <p>File is uploaded : Yes(firebase_credentials.json)</p>
                @else
                <p>File is uploaded : NO</p>
                @endif
            </label>
        </div>
    </div>
</div>
@endif

@endforeach

<div class="mt-3"></div>
<hr>

@foreach($result as $row)
    @if($row->key == 'enable_send_push_notification_on_post_published')
    <div class="col-md-12 mb-3 display-inline-block mr-10">
        <label class="switch switch-square">
            <input value="1" type="checkbox" class="switch-input" id="enable_send_push_notification_on_post_published" name="enable_send_push_notification_on_post_published" @if($row->value == 1) checked @endif>
            <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
            </span>
            <span class="switch-label">{{__('lang.admin_enable_send_push_notification_on_post_published_placeholder')}}</span>
        </label>
    </div>
    @endif
@endforeach