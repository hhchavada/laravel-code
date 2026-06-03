@if($row->key == 'enable_share_setting')
<div class="col-md-12 mb-3">
    <label class="switch switch-square">
        <input value="1" type="checkbox" class="switch-input" id="enable_share_setting" name="enable_share_setting" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_enable_share_blog_placeholder')}}</span>
    </label>
</div>
@endif

@if($row->key == 'android_schema')
<div class="col-md-6 mb-3">
    <label class="form-label" for="android_schema">{{__('lang.admin_android_schema')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_android_schema_placeholder')}}" name="android_schema"/>
</div>
@endif
@if($row->key == 'playstore_url')
<div class="col-md-6 mb-3">
    <label class="form-label" for="playstore_url">{{__('lang.admin_playstore_url')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_playstore_url_placeholder')}}" name="playstore_url"/>
</div>
@endif
@if($row->key == 'ios_schema')
<div class="col-md-6 mb-3">
    <label class="form-label" for="ios_schema">{{__('lang.admin_ios_schema')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_ios_schema_placeholder')}}" name="ios_schema"/>
</div>
@endif
@if($row->key == 'appstore_url')
<div class="col-md-6 mb-3">
    <label class="form-label" for="appstore_url">{{__('lang.admin_appstore_url')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_appstore_url_placeholder')}}" name="appstore_url"/>
</div>
@endif