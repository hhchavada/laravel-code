@if($row->key == 'is_android_app_force_update')
<div class="col-md-6 mb-3">
    <label class="switch switch-square">
        <input value="1" type="checkbox" class="switch-input" id="is_android_app_force_update" name="is_android_app_force_update" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_is_android_app_force_update_placeholder')}}</span>
    </label>
</div>
@endif

@if($row->key == 'is_ios_app_force_update')
<div class="col-md-6 mb-3">
    <label class="switch switch-square">
        <input value="1" type="checkbox" class="switch-input" id="is_ios_app_force_update" name="is_ios_app_force_update" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_is_ios_app_force_update_placeholder')}}</span>
    </label>
</div>
@endif
