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
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_rss_feed_items')}} {{__('lang.admin_list')}}
  </h4>
<div class="card margin-bottom-20">
    <div class="card-header">
      <form method="get">
        <div class="row">
          <h5 class="card-title display-inline-block">{{__('lang.admin_filters')}}</h5>
              <div class="form-group col-sm-3 display-inline-block" >
        <select class="form-control" name="category_id" id="category_id" onchange="updateSources(this.value)">
            <option value="">{{__('lang.admin_select_category')}}</option> 
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
        </select>
    </div>
    <div class="form-group col-sm-3 display-inline-block" >
        <select class="form-control source" name="source_id" id="source_id">
            <option value="">{{__('lang.admin_select_source')}}</option>
            @if(isset($sources) && count($sources))
                @foreach($sources as $source)
                    <option value="{{$source->id}}" @if(isset($_GET['source_id']) && $_GET['source_id']!='') @if($_GET['source_id']==$source->id) selected @endif @endif>{{$source->rss_name}}</option>
               @endforeach
            @endif
        </select>
    </div>
          <div class="col-sm-3 display-inline-block">
            <button type="submit" class="btn btn-primary data-submit">{{__('lang.admin_search')}}</button>
            <a type="reset" class="btn btn-outline-secondary" href="{{url('admin/rss-feed-items')}}">{{__('lang.admin_reset')}}</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title display-inline-block">{{__('lang.admin_rss_feed_items')}} {{__('lang.admin_list')}}</h5>
      <h6 class="float-right"> <?php if ($result->firstItem() != null) {?> {{__('lang.admin_showing')}} {{ $result->firstItem() }}-{{ $result->lastItem() }} {{__('lang.admin_of')}} {{ $result->total() }} <?php }?> </h6>
    </div>
    <div class="table-responsive"> @include('admin/rss-feed-item/table') </div>
    <div class="card-footer">
      <div class="pagination" style="float: right;">
        {{$result->withQueryString()->links('pagination::bootstrap-4')}}
      </div>
    </div>
  </div>

</div> 
@endsection
<script>
    function updateSources(categoryId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ route('admin.get-sources-by-category') }}',
            type: 'POST',
            data: {category_id: categoryId},
            dataType: 'json',
            success: function(response) {
                $('#source_id').empty();
                $.each(response.sources, function(index, source) {
                    $('#source_id').append('<option value="' + source.id + '">' + source.rss_name + '</option>');
                });
            }
        });
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".save-post-form").forEach(function (form) {
            form.addEventListener("submit", function () {
                document.getElementById("fullScreenLoader").style.display = "flex"; 
            });
        });
    });
</script>