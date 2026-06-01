@extends('admin/layout/app') 
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 display-inline-block">
    <span class="text-muted fw-light">
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_rss_feeds')}} {{__('lang.admin_list')}}
  </h4>
  @can('add-social-media')
  <button class="btn btn-secondary btn-primary float-right mt-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#add-new-record" aria-controls="add-new-record">
    <span>
      <span class="d-md-inline-block d-none">{{__('lang.admin_create_rss_feeds')}}</span>
    </span>
  </button>
  @endcan
  <div class="card margin-bottom-20">
    <div class="card-header">
      <form method="get">
        <div class="row">
          <h5 class="card-title display-inline-block">{{__('lang.admin_filters')}}</h5>
          <div class="form-group col-sm-3 display-inline-block" >
              <input type="text" class="form-control dt-full-name" placeholder="{{__('lang.search_keyword')}}" name="search" value="@if(isset($_GET['search']) && $_GET['search']!=''){{$_GET['search']}}@endif">
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="form-control" name="status">
                <option value="">{{__('lang.admin_select_status')}}</option> 
                <option value="0" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==0) selected @endif @endif>{{__('lang.admin_inactive')}}</option>
                <option value="1" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==1) selected @endif @endif>{{__('lang.admin_active')}}</option>
              </select>
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="select2 form-select select2-hidden-accessible" name="category_id">
                  <option value="">{{__('lang.admin_select_category')}}</option> 
                  @if(isset($categories) && count($categories))
                    @foreach($categories as $category)
                      <optgroup label="{{$category->name}}">
                        <option value="{{$category->id}}" @if(isset($_GET['category_id']) && $_GET['category_id']!='') @if($_GET['category_id']==$category->id) selected @endif @endif>{{__('lang.admin_all')}} {{$category->name}}</option>
                        @if(isset($category->sub_category) && count($category->sub_category))
                          @foreach($category->sub_category as $sub_category)
                            <option value="{{$sub_category->id}}" @if(isset($_GET['category_id']) && $_GET['category_id']!='') @if($_GET['category_id']==$sub_category->id) selected @endif @endif>{{$sub_category->name}}</option>
                          @endforeach
                        @endif
                      </optgroup>
                    @endforeach
                  @endif
              </select>
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="form-control" name="language_id">
                  <option value="">{{__('lang.admin_select_language')}}</option> 
                  @if(isset($languages) && count($languages))
                    @foreach($languages as $language)
                      <option value="{{$language->id}}" @if(isset($_GET['language_id']) && $_GET['language_id']!='') @if($_GET['language_id']==$language->id) selected @endif @endif>{{$language->name}}</option>
                    @endforeach
                  @endif
              </select>
          </div>
          <div class="col-sm-3 display-inline-block" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary data-submit">{{__('lang.admin_search')}}</button>
            <a type="reset" class="btn btn-outline-secondary" href="{{url('admin/rss-feeds')}}">{{__('lang.admin_reset')}}</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title display-inline-block">{{__('lang.admin_rss_feeds')}} {{__('lang.admin_list')}}</h5>
      <h6 class="float-right"> <?php if ($result->firstItem() != null) {?> {{__('lang.admin_showing')}} {{ $result->firstItem() }}-{{ $result->lastItem() }} {{__('lang.admin_of')}} {{ $result->total() }} <?php }?> </h6>
    </div>
    <div class="table-responsive text-nowrap"> @include('admin/rss-feed/table') </div>
    <div class="card-footer">
      <div class="pagination" style="float: right;">
        {{$result->withQueryString()->links('pagination::bootstrap-4')}}
      </div>
    </div>
  </div>
  <div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="exampleModalLabel">{{__('lang.admin_add_rss_feeds')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
      <form class="add-new-record pt-0 row g-2" id="add-record" onsubmit="return validateRss('add-record');" action="{{url('admin/add-rss-feeds')}}" method="POST"> 
      @csrf 
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="name">{{__('lang.admin_category')}} <span class="required">*</span></label>
            <select class="form-control" name="category_id">
                <option value="">{{__('lang.admin_select_category')}}</option> 
                @if(isset($categories) && count($categories))
                  @foreach($categories as $category)
                    <optgroup label="{{$category->name}}">
                      <option value="{{$category->id}}">{{__('lang.admin_all')}} {{$category->name}}</option>
                      @if(isset($category->sub_category) && count($category->sub_category))
                        @foreach($category->sub_category as $sub_category)
                          <option value="{{$sub_category->id}}">{{$sub_category->name}}</option>
                        @endforeach
                      @endif
                    </optgroup>
                  @endforeach
                @endif
            </select>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="language_id">{{__('lang.admin_language')}} <span class="required">*</span></label>
            <select class="form-control" name="language_id">
                <option value="">{{__('lang.admin_select_language')}}</option> 
                @if(isset($languages) && count($languages))
                  @foreach($languages as $language)
                    <option value="{{$language->id}}">{{$language->name}}</option>
                  @endforeach
                @endif
            </select>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="rss_name">{{__('lang.admin_name')}} <span class="required">*</span></label>
            <input type="text" class="form-control dt-full-name" id="rss_name" placeholder="{{__('lang.admin_name_placeholder')}}" name="rss_name">
          </div>
        </div>
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="rss_url">{{__('lang.admin_url')}} <span class="required">*</span></label>
            <input type="text" class="form-control dt-full-name" id="rss_url" placeholder="{{__('lang.admin_url_placeholder')}}" name="rss_url">
          </div>
        </div>

        <p class="mb-3 mt-3 border-top"></p>
        <p class="text-center mt-3"><strong>{{__('lang.admin_post_autopublished')}}</strong></p>
        @can('update-social-media-status')
        <div class="col-sm-12 mb-1">
          <label class="switch switch-square">
              <input value="1" type="checkbox" class="switch-input" name="post_auto_publish">
              <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
              </span>
              <span class="switch-label">{{__('lang.admin_enable_post_auto_publish')}}</span>
          </label>
        </div>
        @endcan
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="auto_publish_hour">{{__('lang.admin_hour')}}</label>
            <select class="form-control" name="auto_publish_hour">
                <option value="">{{__('lang.admin_select_hour')}}</option> 
                <?php for ($hour = 1; $hour <= 24; $hour++): ?>
                    <option value="<?php echo $hour; ?>"><?php echo $hour; ?></option>
                <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="auto_publish_post_count">{{__('lang.admin_post_count')}}</label>
            <select class="form-control" name="auto_publish_post_count">
                <option value="">{{__('lang.admin_select_post_count')}}</option> 
                <?php for ($post = 1; $post <= 10; $post++): ?>
                    <option value="<?php echo $post; ?>"><?php echo $post; ?></option>
                <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="col-sm-12">
          <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">{{__('lang.admin_button_save_changes')}}</button>
          <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{__('lang.admin_button_cancel')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
    function validateRss(formId) {
      var $form = $("#" + formId);
      var data = new FormData($form[0]);

      // Validate category selection
      var categoryId = data.get('category_id');
      if (!categoryId) {
          myToastr('{{__('lang.admin_category_error')}}', 'error');
          return false;
      }

      // Validate language selection
      var languageId = data.get('language_id');
      if (!languageId) {
          myToastr('{{__('lang.admin_language_error')}}', 'error');
          return false;
      }

      // Validate RSS name
      var rssName = data.get('rss_name').trim();
      if (rssName === '') {
          myToastr('{{__('lang.admin_name_error')}}', 'error');
          return false;
      }

      // Validate RSS URL
      var rssUrl = data.get('rss_url').trim();
      if (rssUrl === '') {
          myToastr('{{__('lang.admin_url_error')}}', 'error');
          return false;
      }
      
      var urlPattern = /^(https?:\/\/)(www\.)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)*(\.xml)?$/i;
      if (!urlPattern.test(rssUrl)) {
          myToastr('{{__('lang.admin_url_invalid_error')}}', 'error');
          return false;
      }

      // Check if "Post Auto Publish" is enabled
      var isAutoPublishEnabled = $form.find('input[name="post_auto_publish"]').is(":checked");

      if (isAutoPublishEnabled) {
          var autoPublishHour = data.get('auto_publish_hour');
          var autoPublishPostCount = data.get('auto_publish_post_count');

          if (!autoPublishHour) {
              myToastr('{{__('lang.admin_hour_required_error')}}', 'error');
              return false;
          }   

          if (!autoPublishPostCount) {
              myToastr('{{__('lang.admin_post_count_required_error')}}', 'error');
              return false;
          }
      }

      return true;
  }


    function fieldFilledAlert(){
       myToastr('{{__('lang.admin_please_update_autopublish_settings_from_edit_action')}}', 'error');
    }
</script>
@endsection