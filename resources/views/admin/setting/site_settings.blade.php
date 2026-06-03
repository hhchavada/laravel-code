@foreach($result as $row)
    @if($row->key == 'site_name')
    <div class="col-md-3 mb-3 display-inline-block width-32-percent mr-10">
        <label class="form-label" for="site_name">{{__('lang.admin_website_name')}}</label>
        <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_website_name_placeholder')}}"  name="site_name"/>
    </div>
    @endif
    @if($row->key == 'site_admin_name')
    <div class="col-md-3 mb-3 display-inline-block width-32-percent mr-10">
        <label class="form-label" for="site_admin_name">{{__('lang.admin_website_admin_name')}}</label>
        <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_website_admin_name_placeholder')}}"  name="site_admin_name"/>
    </div>
    @endif
    @if($row->key == 'from_email')
    <div class="col-md-3 mb-3 display-inline-block width-32-percent mr-10">
        <label class="form-label" for="from_email">{{__('lang.admin_email_from')}}</label>
        <input type="email" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_email_from_placeholder')}}"  name="from_email"/>
    </div>
    @endif
    @if($row->key == 'preferred_site_language')
    <div class="col-md-3 mb-3 display-inline-block width-32-percent mr-10">
        <label class="form-label" for="preferred_site_language">{{__('lang.admin_preferred_site_language')}}</label>
        <select name="preferred_site_language" class="form-control">
            <option value="">{{__('lang.admin_preferred_site_language_placeholder')}}</option>
            @foreach($languages as $language)
                <option @if($row->value == $language->code) selected  @endif value="{{$language->code}}">{{$language->name}}</option>
            @endforeach
        </select>
    </div>
    @endif
    @if($row->key == 'news_api_key')
    <div class="col-md-3 mb-3 display-inline-block width-32-percent mr-10">
        <label class="form-label" for="news_api_key">{{__('lang.admin_news_api_key')}}</label>
        <input type="text" class="form-control" name="news_api_key" value="{{$row->value}}" placeholder="{{__('lang.admin_news_api_key_placeholder')}}">
    </div>
    @endif
    @if($row->key == 'mediastack_api_key')
    <div class="col-md-3 mb-3 display-inline-block width-32-percent mr-10">
        <label class="form-label" for="mediastack_api_key">{{__('lang.admin_mediastack_api_key')}}</label>
        <input type="text" class="form-control" name="mediastack_api_key" value="{{$row->value}}" placeholder="{{__('lang.admin_mediastack_api_key')}}">
    </div>
    @endif
@endforeach
@foreach($result as $row)
    @if($row->key == 'powered_by')
    <div class="col-md-12 mb-3">
        <label class="form-label" for="powered_by">{{__('lang.admin_powered_by')}}</label>
        <input type="text" class="form-control" value="{{$row->value}}" placeholder="{{__('lang.admin_powered_by_placeholder')}}"  name="powered_by"/>
    </div>
    @endif
    @if($row->key == 'google_analytics_code')
    <div class="col-md-12 mb-3">
        <label class="form-label" for="google_analytics_code">{{__('lang.admin_google_analytics_code')}}</label>
        <textarea type="text" class="form-control" name="google_analytics_code" value="{{$row->value}}" placeholder="{{__('lang.admin_google_analytics_code_placeholder')}}">{{$row->value}}</textarea>
    </div>
    @endif
@endforeach
@foreach($result as $row)
@if($row->key == 'site_logo')
<div class="col-md-4 mb-3">
    <div class="d-flex-p">
    <img src="{{url('uploads/setting/'.$row->value)}}"  class="rounded me-50 image-preview-cls" id="site-logo-preview" alt="site_logo" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
    <div class="img-footer">
        <label class="btn btn-primary me-75 mb-0" for="change-site-logo">
        <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
        <input class="form-control" type="file" name="site_logo" id="change-site-logo" hidden accept="image/*" name="site_logo" onclick="showImagePreview('change-site-logo','site-logo-preview',408,115);"/>
        <span class="d-block d-sm-none">
            <i class="me-0" data-feather="edit"></i>
        </span>
        </label>
        <p class="img-label">{{__('lang.admin_website_logo')}}</p>
        <p class="img-resolution">{{__('lang.admin_upload_website_logo_resolution')}}</p>
    </div>
    </div>
</div>
@endif
@if($row->key == 'website_admin_logo')
<div class="col-md-4 mb-3">
    <div class="d-flex-p">
    <img src="{{url('uploads/setting/'.$row->value)}}"  class="rounded me-50 image-preview-cls" id="website-admin-logo-preview" alt="website_admin_logo" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
    <div class="img-footer">
        <label class="btn btn-primary me-75 mb-0" for="change-website-admin-logo">
        <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
        <input class="form-control" type="file" name="website_admin_logo" id="change-website-admin-logo" hidden accept="image/*" name="website_admin_logo" onclick="showImagePreview('change-website-admin-logo','website-admin-logo-preview',512,512);"/>
        <span class="d-block d-sm-none">
            <i class="me-0" data-feather="edit"></i>
        </span>
        </label>
        <p class="img-label">{{__('lang.admin_website_admin_logo')}}</p>
        <p class="img-resolution">{{__('lang.admin_website_admin_logo_resolution')}}</p>
    </div>
    </div>
</div>
@endif
@if($row->key == 'site_favicon')
<div class="col-md-4 mb-3">
    <div class="d-flex-p">
        <img src="{{url('uploads/setting/'.$row->value)}}"  class="rounded me-50 image-preview-cls" id="website-favicon-preview" alt="site_favicon" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
        <div class="img-footer">
            <label class="btn btn-primary me-75 mb-0" for="change-website-favicon">
            <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
            <input class="form-control" type="file" name="site_favicon" id="change-website-favicon" hidden accept="image/*" name="site_favicon" onclick="showImagePreview('change-website-favicon','website-favicon-preview',512,512);"/>
            <span class="d-block d-sm-none">
                <i class="me-0" data-feather="edit"></i>
            </span>
            </label>
            <p class="img-label">{{__('lang.admin_website_favicon')}}</p>
            <p class="img-resolution">{{__('lang.admin_website_favicon_resolution')}}</p>
        </div>
    </div>
</div>
@endif
@endforeach