@foreach($result as $row)

    @if($row->key == 'app_name')
    <div class="col-md-6 mb-3">
        <label class="form-label" for="app_name">{{__('lang.admin_app_name')}}</label>
        <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_app_name_placeholder')}}" name="app_name"/>
    </div>
    @endif
    
    @if($row->key == 'support_email')
    <div class="col-md-6 mb-3">
        <label class="form-label" for="app_name">{{__('lang.admin_support_email')}}</label>
        <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_support_email_placeholder')}}" name="support_email"/>
    </div>
    @endif

@endforeach
    

@foreach($result as $row)
    
    @if($row->key == 'primary_color')
    <div class="col-md-6 mb-3">
        <label class="form-label" for="primary_color">{{__('lang.admin_primary_color')}}</label>
        <input type="color" id="basic-icon-default-uname" class="form-control dt-uname" name="primary_color" value="{{$row->value}}">
    </div>
    @endif
    @if($row->key == 'secondary_color')
    <div class="col-md-6 mb-3">
        <label class="form-label" for="secondary_color">{{__('lang.admin_secondary_color')}}</label>
        <input type="color" id="basic-icon-default-uname" class="form-control dt-uname" name="secondary_color" value="{{$row->value}}">
    </div>
    @endif
    @if($row->key == 'app_logo')
    <div class="col-md-4 mb-3">
        <div class="d-flex-p">
        <img src="{{url('uploads/setting/'.$row->value)}}" class="rounded me-50 image-preview-cls" id="app-logo-preview" alt="app_logo" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
        <div class="img-footer">
            <label class="btn btn-primary me-75 mb-0" for="change-app-logo">
            <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
            <input class="form-control" type="file" name="app_logo" id="change-app-logo" hidden accept="image/*" name="app_logo" onclick="showImagePreview('change-app-logo','app-logo-preview',512,512);"/>
            <span class="d-block d-sm-none">
                <i class="me-0" data-feather="edit"></i>
            </span>
            </label>
            <p class="img-label">{{__('lang.admin_app_logo')}}</p>
            <p class="img-resolution">{{__('lang.admin_app_logo_resolution')}}</p>
        </div>
        </div>
    </div>
    @endif
    @if($row->key == 'rectangualr_app_logo')
    <div class="col-md-4 mb-3">
        <div class="d-flex-p">
        <img src="{{url('uploads/setting/'.$row->value)}}" class="rounded me-50 image-preview-cls" id="app-rectangular-logo-preview" alt="rectangualr_app_logo" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
        <div class="img-footer">
            <label class="btn btn-primary me-75 mb-0" for="change-rectangular-logo">
            <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
            <input class="form-control" type="file" name="rectangualr_app_logo" id="change-rectangular-logo" hidden accept="image/*" name="rectangualr_app_logo" onclick="showImagePreview('change-rectangular-logo','app-rectangular-logo-preview',379,128);"/>
            <span class="d-block d-sm-none">
                <i class="me-0" data-feather="edit"></i>
            </span>
            </label>
            <p class="img-label">{{__('lang.admin_rectangualr_app_logo')}}</p>
            <p class="img-resolution">{{__('lang.admin_rectangualr_app_logo_resolution')}}</p>
        </div>
        </div>
    </div>
    @endif
    @if($row->key == 'app_splash_screen')
    <div class="col-md-4 mb-3">
        <div class="d-flex-p">
        <img src="{{url('uploads/setting/'.$row->value)}}" class="rounded me-50 image-preview-cls" id="app-splash-screen-preview" alt="app_splash_screen" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
        <div class="img-footer">
            <label class="btn btn-primary me-75 mb-0" for="change-app-splash-screen">
            <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
            <input class="form-control" type="file" name="app_splash_screen" id="change-app-splash-screen" hidden accept="image/*" name="app_splash_screen" onclick="showImagePreview('change-app-splash-screen','app-splash-screen-preview',1000,1000);"/>
            <span class="d-block d-sm-none">
                <i class="me-0" data-feather="edit"></i>
            </span>
            </label>
            <p class="img-label">{{__('lang.admin_app_splash_screen')}}</p>
            <p class="img-resolution">{{__('lang.admin_app_splash_screen_resolution')}}</p>
        </div>
        </div>
    </div>
    @endif
    @if($row->key == 'is_short_video_enable')
    <div class="col-md-12 mb-3 mt-3">
        <label class="switch switch-square">
            <input value="1" type="checkbox" class="switch-input" id="is_short_video_enable" name="is_short_video_enable" @if($row->value == 1) checked @endif>
            <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
            </span>
            <span class="switch-label">{{__('lang.admin_is_short_video_enable_placeholder')}}</span>
        </label>
    </div>
    @endif
@endforeach

<div class="mt-3"></div>
<hr>

@foreach($result as $row)

    @if($row->key == 'category_icon_shape')
    <div class="col-md-6 mb-3">
        <label class="form-label">{{__('lang.admin_category_icon_shape')}}</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="category_icon_shape" id="iconShapeSquare" value="square" 
                       @if($row->value === 'square') checked @endif>
                <label class="form-check-label" for="iconShapeSquare">{{__('lang.admin_square')}}</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="category_icon_shape" id="iconShapeCircle" value="circle" 
                       @if($row->value === 'circle') checked @endif>
                <label class="form-check-label" for="iconShapeCircle">{{__('lang.admin_circle')}}</label>
            </div>
        </div>
    </div>
    @endif
    
    
    @if($row->key == 'category_icon_column')
    <div class="col-md-6 mb-3">
        <label class="form-label">{{__('lang.admin_category_icon_column')}}</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="category_icon_column" id="iconColumn5" value="5" 
                       @if($row->value === '5') checked @endif>
                <label class="form-check-label" for="iconColumn5">5</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="category_icon_column" id="iconColumn4" value="4" 
                       @if($row->value === '4') checked @endif>
                <label class="form-check-label" for="iconColumn4">4</label>
            </div>
        </div>
    </div>
    @endif
    
@endforeach
