@extends('admin/layout/app')
@section('content')
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 display-inline-block">
    <span class="text-muted fw-light">
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_short_video')}} {{__('lang.admin_list')}}
  </h4>
@can('add-short-video')
<a class="btn btn-secondary btn-primary float-right mt-3" href="{{url('admin/create-short-video')}}" title="{{__('lang.admin_create_short_video')}}">
    <span>
      <span class="d-md-inline-block d-none">{{__('lang.admin_create_short_video')}}</span>
    </span>
  </a>
@endcan
<div class="card margin-bottom-20">
    <div class="card-header">
      <form method="get">

        <div class="row">
          <h5 class="card-title display-inline-block">{{__('lang.admin_filters')}}</h5>
          <div class="form-group col-sm-3 display-inline-block" >
              <input type="text" class="form-control" placeholder="{{__('lang.admin_search_title')}}" name="title" value="@if(isset($_GET['title']) && $_GET['title']!=''){{$_GET['title']}}@endif">
          </div>

          <div class="col-sm-3 display-inline-block">
              <select class="form-control" name="status">
                <option value="">{{__('lang.admin_status')}}</option> 
                <option value="0" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==0) selected @endif @endif>{{__('lang.admin_unpublish')}}</option>
                <option value="1" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==1) selected @endif @endif>{{__('lang.admin_publish')}}</option>
                <option value="2" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==2) selected @endif @endif>{{__('lang.admin_draft')}}</option>
                <option value="3" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==3) selected @endif @endif>{{__('lang.admin_submit')}}</option>
                <option value="4" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==4) selected @endif @endif>{{__('lang.admin_schedule')}}</option>
              </select>
          </div>

          <div class="form-group col-sm-3">
            <input type="text" class="form-control flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="from_date" value="@if(isset($_GET['from_date']) && $_GET['from_date']!=''){{$_GET['from_date']}}@endif"/>
          </div>
          
          <div class="form-group col-sm-3">
            <input type="text" class="form-control flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="to_date" value="@if(isset($_GET['to_date']) && $_GET['to_date']!=''){{$_GET['to_date']}}@endif"/>
          </div>
          
          <div class="col-sm-3 display-inline-block" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary data-submit">{{__('lang.admin_search')}}</button>
            <a type="reset" class="btn btn-outline-secondary" href="{{url('admin/short-video')}}">{{__('lang.admin_reset')}}</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title display-inline-block">{{__('lang.admin_short_video')}} {{__('lang.admin_list')}}</h5>
      <h6 class="float-right"> <?php if ($result->firstItem() != null) {?> {{__('lang.admin_showing')}} {{ $result->firstItem() }}-{{ $result->lastItem() }} {{__('lang.admin_of')}} {{ $result->total() }} <?php }?> </h6>
    </div>
    @can('delete-short-video')
    <div class="button-group">
        <button style="margin-left: 20px" id="deleteSelected" class="btn btn-danger btn-sm mb-3">{{__('lang.admin_delete_all')}}</button>
    </div>
    <form id="deleteForm" method="POST" action="{{ route('short-video.deleteSelected') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="selectedIds" id="selectedIds" value="">
    </form>
    @endcan
    <div class="table-responsive"> @include('admin/short_video/table') </div>
    <div class="card-footer">
      <div class="pagination" style="float: right;">
        {{$result->withQueryString()->links('pagination::bootstrap-4')}}
      </div>
    </div>
  </div>
</div> 

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
$(document).on('click', '.changeShortVideoStatus', function () {

    let blogId = $(this).data('id');
    let text = $(this).data('text');

    Swal.fire({
        title: text,
        icon: adminTranslation.admin_warning,
        showCancelButton: true,
        confirmButtonText: adminTranslation.admin_delete_warning_yes_button,
        cancelButtonText: adminTranslation.admin_delete_warning_no_button,
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.shortVideo.changeStatusViaList') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: blogId
                },
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                }
            });
        }
    });
});
</script>
@endsection