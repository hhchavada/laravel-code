@if($row->key == 'enable_unity_ads')
<div class="col-md-12 mb-3 display-inline-block mr-10">
    <label class="switch switch-square">
        <input value="1" type="checkbox" class="switch-input" id="enable_unity_ads" name="enable_unity_ads" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_enable_unity_ads_placeholder')}}</span>
    </label>
</div>
@endif

@if($row->key == 'unity_ads_frequency')
<div class="col-md-4 mb-3 display-inline-block">
    <label class="form-label" for="unity_ads_frequency">{{__('lang.admin_unity_ads_frequency')}}</label>
    <input type="text" class="form-control" name="unity_ads_frequency" placeholder="{{__('lang.admin_unity_ads_frequency_placeholder')}}" value="{{$row->value}}">
</div>
@endif
@if($row->key == 'unity_ads_banner_id_android')
<div class="col-md-4 mb-3 display-inline-block">
    <label class="form-label" for="unity_ads_banner_id_android">{{__('lang.admin_unity_ads_banner_id_android')}}</label>
    <input type="text" class="form-control" name="unity_ads_banner_id_android" placeholder="{{__('lang.admin_unity_ads_banner_id_android_placeholder')}}" value="{{$row->value}}">
</div>
@endif
@if($row->key == 'unity_ads_banner_id_ios')
<div class="col-md-4 mb-3 display-inline-block">
    <label class="form-label" for="unity_ads_banner_id_ios">{{__('lang.admin_unity_ads_banner_id_ios')}}</label>
    <input type="text" class="form-control" name="unity_ads_banner_id_ios" placeholder="{{__('lang.admin_unity_ads_banner_id_ios_placeholder')}}" value="{{$row->value}}">
</div>
@endif
@if($row->key == 'unity_android_game_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="unity_android_game_id">{{__('lang.admin_unity_android_game_id')}}</label>
    <input type="text" class="form-control" name="unity_android_game_id" placeholder="{{__('lang.admin_unity_android_game_id_placeholder')}}" value="{{$row->value}}" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">
</div>
@endif
@if($row->key == 'unity_ios_game_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="unity_ios_game_id">{{__('lang.admin_unity_ios_game_id')}}</label>
    <input type="text" class="form-control" name="unity_ios_game_id" placeholder="{{__('lang.admin_unity_ios_game_id_placeholder')}}" value="{{$row->value}}">
</div>
@endif
@if($row->key == 'unity_placement_android_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="unity_placement_android_id">{{__('lang.admin_unity_placement_android_id')}}</label>
    <input type="text" class="form-control" name="unity_placement_android_id" placeholder="{{__('lang.admin_unity_placement_android_id_placeholder')}}" value="{{$row->value}}">
</div>
@endif
@if($row->key == 'unity_placement_ios_id')
<div class="col-md-6 mb-3 display-inline-block">
    <label class="form-label" for="unity_placement_ios_id">{{__('lang.admin_unity_placement_ios_id')}}</label>
    <input type="text" class="form-control" name="unity_placement_ios_id" placeholder="{{__('lang.admin_unity_placement_ios_id_placeholder')}}" value="{{$row->value}}">
</div>
@endif