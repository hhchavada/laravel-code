@extends('admin/layout/app') 
@section('content') 

<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<!-- Full-Screen Loader -->
<div id="fullScreenLoader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div class="spinner-border text-light" role="status" style="width: 5rem; height: 5rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 display-inline-block">
    <span class="text-muted fw-light">
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_news_api')}} {{__('lang.admin_list')}}
  </h4>
  @if(setting('news_api_key')=='' && setting('mediastack_api_key')=='')
  <div class="alert alert-warning d-flex align-items-center" role="alert">
      <span class="alert-icon text-warning me-2">
        <i class="ti ti-alert-triangle ti-xs"></i>
      </span>
      {{__('lang.admin_news_api_not_found_message')}}
  </div>
  @endif
  <div class="card margin-bottom-20">
    <div class="card-header">
      <form method="get">
        <div class="row">
          <h5 class="card-title display-inline-block">{{__('lang.admin_filters')}}</h5>
          <div class="col-sm-3 display-inline-block">
              <select id="apiType" class="select2 form-select form-select-lg select2-hidden-accessible" name="type" required>
                  <option value="">Select Type</option> 
                  <option <?php if(isset($_GET['type'])){ if($_GET['type']=='newsapi'){ echo "selected"; } }?> value="newsapi">{{__('lang.admin_newsapi')}}</option>
                  <option <?php if(isset($_GET['type'])){ if($_GET['type']=='mediastackapi'){ echo "selected"; } }?> value="mediastackapi">{{__('lang.admin_mediastack_api')}}</option>
              </select>
          </div>
          <div class="form-group col-sm-3 display-inl ine-block" >
            <input type="text" class="form-control" placeholder="{{__('lang.admin_keyword')}}" name="keyword" value="@if(isset($_GET['keyword']) && $_GET['keyword']!=''){{$_GET['keyword']}}@endif">
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="select2 form-select form-select-lg select2-hidden-accessible" name="sources">
                <option value="">{{__('lang.admin_select_source')}}</option> 
                @if(isset($source) && count($source))
                  @foreach($source as $source_data)
                    <option value="{{$source_data['id']}}" <?php if(isset($_GET['sources'])){ if($_GET['sources']==$source_data['id']){ echo "selected"; } }?>>{{$source_data['name']}}</option>
                  @endforeach
                @endif
              </select>
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="select2 form-select form-select-lg select2-hidden-accessible" name="language">
                <option value="">{{__('lang.admin_select_language')}}</option> 
                @if(count($api_language))
                  @foreach($api_language as $key => $value)
                    <option value="{{$key}}" <?php if(isset($_GET['language'])){ if($_GET['language']==$key){ echo "selected"; } }?>>{{$value}}</option>
                  @endforeach
                @endif
              </select>
          </div>
          <div class="form-group col-sm-3 display-inline-block" style="margin-top: 20px;">
              <input type="text" class="form-control flatpickr-input active flatpickr-date" placeholder="YYYY-MM-DD" name="from" @if(isset($_GET['from'])) value="{{$_GET['from']}}" @endif readonly="readonly"/>
          </div>
          <div class="form-group col-sm-3 display-inline-block" style="margin-top: 20px;">
              <input type="text" class="form-control flatpickr-input active flatpickr-date" placeholder="YYYY-MM-DD" name="to" readonly="readonly" @if(isset($_GET['to'])) value="{{$_GET['to']}}" @endif/>
          </div>
          <div class="col-sm-3 display-inline-block" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary data-submit">{{__('lang.admin_search')}}</button>
            <a type="reset" class="btn btn-outline-secondary" href="{{url('admin/news-api')}}">{{__('lang.admin_reset')}}</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title display-inline-block">{{__('lang.admin_news_api')}} {{__('lang.admin_list')}}</h5>
      <h6 class="float-right"> <?php if ($result->firstItem() != null) {?> {{__('lang.admin_showing')}} {{ $result->firstItem() }}-{{ $result->lastItem() }} {{__('lang.admin_of')}} {{ $result->total() }} <?php }?> </h6>
    </div>
    <div class="table-responsive"> @include('admin/news-api/table') </div>
    <div class="card-footer">
      <div class="pagination" style="float: right;">
        {{$result->withQueryString()->links('pagination::bootstrap-4')}}
      </div>
    </div>
  </div>

</div> 
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".save-post-form").forEach(function (form) {
            form.addEventListener("submit", function () {
                document.getElementById("fullScreenLoader").style.display = "flex"; 
            });
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    $('#apiType').on('change.select2', function () {

        // Show loader
        document.getElementById("fullScreenLoader").style.display = "flex";

        let selectedType = $(this).val();
        let baseUrl = "{{ url('admin/news-api') }}";

        // Reload page with type parameter
        window.location.href = baseUrl + "?type=" + selectedType;
    });

});
</script>

