@if($row->key == 'live_news_logo')
    <div class="col-md-3 mb-3">
        <div class="d-flex-p">
        <img src="{{url('uploads/setting/'.$row->value)}}" class="rounded me-50 image-preview-cls" id="news-logo-preview" alt="live_news_logo" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
        <div class="img-footer">
            <label class="btn btn-primary me-75 mb-0" for="change-news-logo">
            <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
            <input class="form-control" type="file" name="live_news_logo" id="change-news-logo" hidden accept="image/*" name="live_news_logo" onclick="showImagePreview('change-news-logo','news-logo-preview',512,512);"/>
            <span class="d-block d-sm-none">
                <i class="me-0" data-feather="edit"></i>
            </span>
            </label>
            <p class="img-label">{{__('lang.admin_live_news_image')}}</p>
            <p class="img-resolution">{{__('lang.admin_live_news_image_resolution')}}</p>
        </div>
        </div>
    </div>
@endif

@if($row->key == 'live_news_status')
   <div class="col-md-3 mb-3">
    <label class="switch switch-square" style="margin-left: -50px;">
        <input value="1" type="checkbox" class="switch-input" id="live_news_status" name="live_news_status" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_live_news_status_placeholder')}}</span>
    </label>
</div>
@endif

@if($row->key == 'e_paper_logo')
    <div class="col-md-3 mb-3">
        <div class="d-flex-p">
        <img src="{{url('uploads/setting/'.$row->value)}}" class="rounded me-50 image-preview-cls" id="epaper-logo-preview" alt="e_paper_logo" height="80" width="80" onerror="this.onerror=null;this.src=`{{ asset('uploads/image_preview.jpg') }}`"/>
        <div class="img-footer">
            <label class="btn btn-primary me-75 mb-0" for="change-epaper-logo">
            <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
            <input class="form-control" type="file" name="e_paper_logo" id="change-epaper-logo" hidden accept="image/*" name="e_paper_logo" onclick="showImagePreview('change-epaper-logo','epaper-logo-preview',512,512);"/>
            <span class="d-block d-sm-none">
                <i class="me-0" data-feather="edit"></i>
            </span>
            </label>
            <p class="img-label">{{__('lang.admin_epaper_image')}}</p>
            <p class="img-resolution">{{__('lang.admin_epaper_image_resolution')}}</p>
        </div>
        </div>
    </div>
@endif

@if($row->key == 'e_paper_status')
   <div class="col-md-3 mb-3">
    <label class="switch switch-square" style="margin-left: -50px;">
        <input value="1" type="checkbox" class="switch-input" id="e_paper_status" name="e_paper_status" @if($row->value == 1) checked @endif>
        <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
        </span>
        <span class="switch-label">{{__('lang.admin_e_paper_status_placeholder')}}</span>
    </label>
</div>
@endif