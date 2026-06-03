@extends('admin/layout/app') 
@section('content')
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 display-inline-block">
    <span class="text-muted fw-light">
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_blog')}} {{__('lang.admin_list')}}
  </h4>
  <div class="card margin-bottom-20">
    <div class="card-header">
      <form method="get">
        <div class="row">
          <h5 class="card-title display-inline-block">{{__('lang.admin_filters')}}</h5>
          <div class="form-group col-sm-3 display-inline-block" >
              <input type="text" class="form-control" placeholder="{{__('lang.admin_search_title')}}" name="title" value="@if(isset($_GET['title']) && $_GET['title']!=''){{$_GET['title']}}@endif">
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="select2 form-select form-select-lg select2-hidden-accessible" name="category_id">
                <option value="">{{__('lang.admin_select_category')}}</option> 
                @if(count($category))
                  @foreach($category as $category_data)
                    <option value="{{$category_data->id}}" @if(isset($_GET['category_id']) && $_GET['category_id']!='') @if($_GET['category_id']==$category_data->id) selected @endif @endif>{{$category_data->name}}</option>
                  @endforeach
                @endif
              </select>
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="select2 form-select form-select-lg select2-hidden-accessible" name="visibility_id">
                <option value="">{{__('lang.admin_select_visibility')}}</option> 
                <option value="0" @if(isset($_GET['visibility_id']) && $_GET['visibility_id']!='') @if($_GET['visibility_id']==0) selected @endif @endif>{{__('lang.admin_featured')}}</option> 
                @if(count($visibility))
                  @foreach($visibility as $visibility_data)
                    <option value="{{$visibility_data->id}}" @if(isset($_GET['visibility_id']) && $_GET['visibility_id']!='') @if($_GET['visibility_id']==$visibility_data->id) selected @endif @endif>{{$visibility_data->display_name}}</option>
                  @endforeach
                @endif
              </select>
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
          <div class="col-sm-3 display-inline-block" style="margin-top: 20px;">
              <select class="form-control" name="type">
                <option value="">{{__('lang.admin_type')}}</option> 
                <option value="post" @if(isset($_GET['type']) && $_GET['type']!='') @if($_GET['type']=='post') selected @endif @endif>{{__('lang.admin_post')}}</option>
                <option value="quote" @if(isset($_GET['type']) && $_GET['type']!='') @if($_GET['type']=='quote') selected @endif @endif>{{__('lang.admin_quote')}}</option>
              </select>
          </div>
          <div class="form-group col-sm-3" style="margin-top: 20px;">
            <input type="text" class="form-control flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="from_date" value="@if(isset($_GET['from_date']) && $_GET['from_date']!=''){{$_GET['from_date']}}@endif"/>
          </div>
          <div class="form-group col-sm-3" style="margin-top: 20px;">
            <input type="text" class="form-control flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="to_date" value="@if(isset($_GET['to_date']) && $_GET['to_date']!=''){{$_GET['to_date']}}@endif"/>
          </div>
          <div class="col-sm-3 display-inline-block" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary data-submit">{{__('lang.admin_search')}}</button>
            <a type="reset" class="btn btn-outline-secondary" href="{{url('admin/post')}}">{{__('lang.admin_reset')}}</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title display-inline-block">{{__('lang.admin_blog')}} {{__('lang.admin_list')}}</h5>
      <h6 class="float-right"> <?php if ($result->firstItem() != null) {?> {{__('lang.admin_showing')}} {{ $result->firstItem() }}-{{ $result->lastItem() }} {{__('lang.admin_of')}} {{ $result->total() }} <?php }?> </h6>
    </div>
    
    @can('delete-blog')
    <div class="button-group">
        <button style="margin-left: 20px" id="deleteSelected" class="btn btn-danger btn-sm mb-3">{{__('lang.admin_delete_all')}}</button>
    </div>
    <form id="deleteForm" method="POST" action="{{ route('deleteSelected') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="selectedIds" id="selectedIds" value="">
    </form>
    @endcan


    <div class="table-responsive"> @include('admin/blog/table') </div>
    <div class="card-footer">
      <div class="pagination" style="float: right;">
        {{$result->withQueryString()->links('pagination::bootstrap-4')}}
      </div>
    </div>
  </div>

</div> 


<!--============Model===============-->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="sendNotificationForm" action="{{ route('send-notification-to-users') }}" method="post">
          @csrf
          <div class="modal-header">
              <h5 class="modal-title">Send Notification</h5>
              <button type="button" class="close_send_notification_modal" style="border: 0; font-size: 20px; color: red;">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body row">
              <div class="form-group col-sm-6">
                  <label>
                      <input type="radio" name="recipient" value="all_users" checked>
                      {{ __('lang.admin_all_users') }}
                  </label>
              </div>
              <div class="form-group col-sm-6">
                  <label>
                      <input type="radio" name="recipient" value="preferred_users">
                      {{ __('lang.admin_feed_users') }}
                  </label>
              </div>
              <input type="hidden" name="id" class="hidden_id">
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary close_send_notification_modal">Close</button>
              <button type="button" id="sendNotificationBtn" class="btn btn-primary">Send Notification</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


<script>
  $(document).on('click','.send_notification_to_users',function(){
      var id = $(this).attr('data-id');
      $('.hidden_id').val(id);
      $('#sendNotificationModal').modal('show');
  });

  $(document).on('click','.close_send_notification_modal',function(){
      $('#sendNotificationModal').modal('hide');
  });

  $(document).on('click', '#sendNotificationBtn', function (e) {
      e.preventDefault();

      var form = $('#sendNotificationForm');
      var formData = form.serialize();
      var submitButton = $(this);
      
      // Show loader and disable button
      submitButton.prop('disabled', true);
      submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
          url: form.attr('action'),
          type: 'POST',
          data: formData,
          success: function (response) {
            // Success Message
            myToastr(response.message,response.type);
            $('#sendNotificationModal').modal('hide');
          },
          error: function (xhr) {
            var response = xhr.responseJSON;
            // Error Message
            if(response.statusCode == 422){
              myToastr(response.message,response.type);
            }
          },
          complete: function () {
              // Reset Button
              submitButton.prop('disabled', false);
              submitButton.html('Send Notification');
          }
      });
  });
</script>
<script>
$(document).on('click', '.changeStatus', function () {

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
                url: "{{ route('admin.blog.changeStatusViaList') }}",
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