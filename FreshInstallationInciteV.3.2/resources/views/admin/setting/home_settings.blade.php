@if($row->key == 'meta_title')
<div class="col-md-3 mb-3 display-inline width-32-percent mr-10">
    <label class="form-label" for="meta_title">{{__('lang.admin_meta_title')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_meta_title_placeholder')}}" name="meta_title"/>
</div>
@endif

@if($row->key == 'meta_description')
<div class="col-md-3 mb-3 display-inline width-32-percent mr-10">
    <label class="form-label" for="meta_description">{{__('lang.admin_meta_description')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_meta_description_placeholder')}}" name="meta_description"/>
</div>
@endif

@if($row->key == 'meta_tag')
<div class="col-md-3 mb-3 display-inline width-32-percent mr-10">
    <label class="form-label" for="meta_tag">{{__('lang.admin_meta_tag')}}</label>
    <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_meta_tag_placeholder')}}" name="meta_tag"/>
</div>
@endif

@if($row->key == 'home_banner')
<div class="col-md-12 mb-3 display-inline-block mr-10">
    <div class="d-flex-p">
    <img src="{{url('uploads/setting/'.$row->value)}}" class="rounded me-50 image-preview-cls" id="home-banner-preview" alt="home_banner" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
    <div class="img-footer">
        <label class="btn btn-primary me-75 mb-0" for="change-home-banner">
        <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
        <input class="form-control" type="file" name="home_banner" id="change-home-banner" hidden accept="image/*" name="home_banner" onclick="showImagePreview('change-home-banner','home-banner-preview',512,512);"/>
        <span class="d-block d-sm-none">
            <i class="me-0" data-feather="edit"></i>
        </span>
        </label>
        <p class="img-label">{{__('lang.admin_home_banner')}}</p>
        <p class="img-resolution">{{__('lang.admin_home_banner_resolution')}}</p>
    </div>
    </div>
</div>
@endif
