@extends('admin/layout/app') 
@section('content') 
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 display-inline-block">
    <span class="text-muted fw-light">
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_epaper')}} {{__('lang.admin_list')}}
  </h4>
  @can('add-e-paper')
  <button class="btn btn-secondary btn-primary float-right mt-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#add-new-record" aria-controls="add-new-record">
    <span>
      <span class="d-md-inline-block d-none">{{__('lang.admin_create_epaper')}}</span>
    </span>
  </button>
  @endcan
  <div class="card margin-bottom-20">
    <div class="card-header">
      <form method="get">
        <div class="row">
          <h5 class="card-title display-inline-block">{{__('lang.admin_filters')}}</h5>
          <div class="form-group col-sm-3 display-inline-block" >
              <input type="text" class="form-control" placeholder="Search name" name="name" value="@if(isset($_GET['name']) && $_GET['name']!=''){{$_GET['name']}}@endif">
          </div>
          <div class="col-sm-3 display-inline-block">
              <select class="form-control" name="status">
                <option value="">{{__('lang.admin_select_status')}}</option> 
                <option value="0" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==0) selected @endif @endif>{{__('lang.admin_inactive')}}</option>
                <option value="1" @if(isset($_GET['status']) && $_GET['status']!='') @if($_GET['status']==1) selected @endif @endif>{{__('lang.admin_active')}}</option>
              </select>
          </div>
          <div class="col-sm-3 display-inline-block">
            <button type="submit" class="btn btn-primary data-submit">{{__('lang.admin_search')}}</button>
            <a type="reset" class="btn btn-outline-secondary" href="{{url('admin/e-papers')}}">{{__('lang.admin_reset')}} </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title display-inline-block">{{__('lang.admin_epaper')}} {{__('lang.admin_list')}}</h5>
      <h6 class="float-right"> <?php if ($result->firstItem() != null) {?> {{__('lang.admin_showing')}} {{ $result->firstItem() }}-{{ $result->lastItem() }} {{__('lang.admin_of')}} {{ $result->total() }} <?php }?> </h6>
    </div>
    <div class="table-responsive text-nowrap"> @include('admin/e-paper/table') </div>
    <div class="card-footer">
      <div class="pagination" style="float: right;">
        {{$result->withQueryString()->links('pagination::bootstrap-4')}}
      </div>
    </div>
  </div>
  <div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="exampleModalLabel">{{__('lang.admin_add_epaper')}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
      <form class="add-new-record pt-0 row g-2" id="add-record" onsubmit="return validateEpaper('add-record');" action="{{url('admin/add-e-paper')}}" method="POST" enctype="multipart/form-data"> @csrf 
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="name">{{__('lang.admin_name')}} <span class="required">*</span></label>
            <input type="text" class="form-control dt-full-name" id="name" placeholder="{{__('lang.admin_name_placeholder')}}" name="name">
          </div>
        </div>
        
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="basic-icon-default-uname">{{__('lang.admin_pdf')}} <span class="required">*</span></label>
            <div class="d-flex-p">
              <iframe src="" class="rounded me-50 image-preview-cls hide" id="pdf-preview" alt="image" height="80" width="80"></iframe>
              <div class="img-footer">
                <label class="btn btn-primary me-75 mb-0" for="change-pdf">
                  <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
                  <input class="form-control" type="file" name="pdf" id="change-pdf" hidden accept=".pdf" name="pdf" onchange="showPdfPreview(this, 'pdf-preview');"/>
                  <span class="d-block d-sm-none">
                    <i class="me-0" data-feather="edit"></i>
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="mb-1">
            <label class="form-label" for="basic-icon-default-uname">{{__('lang.admin_image')}} <span class="required">*</span></label>
            <div class="d-flex-p">
              <img src="{{url('uploads/image_preview.jpg')}}" class="rounded me-50 image-preview-cls" id="image-preview" alt="image" height="80" width="80"/>
              <div class="img-footer">
                <label class="btn btn-primary me-75 mb-0" for="change-picture">
                  <span class="d-none d-sm-block"><i class="tf-icons ti ti-upload"></i></span>
                  <input class="form-control" type="file" name="image" id="change-picture" hidden accept="image/png, image/jpeg, image/jpg" name="image" onclick="showImagePreview('change-picture','image-preview',512,512);"/>
                  <span class="d-block d-sm-none">
                    <i class="me-0" data-feather="edit"></i>
                  </span>
                </label>
                <p>{{__('lang.admin_epaper_image_resolution')}}</p>
              </div>
            </div>
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
@endsection
<script>
  function showPdfPreview(input, previewId) {
    const file = input.files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
      const preview = document.getElementById(previewId);
      preview.src = reader.result;
      preview.classList.remove('hide');
    }

    if (file) {
      reader.readAsDataURL(file);
    } else {
      const preview = document.getElementById(previewId);
      preview.src = '';
      preview.classList.add('hide');
    }
  }

  // Optional: Trigger preview on file input change
  document.getElementById('change-pdf').addEventListener('change', function() {
    showPdfPreview(this, 'pdf-preview');
  });
</script>

<script>
  function showEditPdfPreview(input, previewId) {
    const file = input.files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
      const preview = document.getElementById(previewId);
      preview.src = reader.result;
      preview.classList.remove('hide');
    }

    if (file) {
      reader.readAsDataURL(file);
    } else {
      const preview = document.getElementById(previewId);
      preview.src = '';
      preview.classList.add('hide');
    }
  }
</script>
<script>
    function validateEpaper(formid) {
    var $form = $("#" + formid);
    var data = new FormData($form[0]);
    var imageFile = data.get('image');
    var pdfFile = data.get('pdf');
    if (data.get('name') == '') {
        myToastr(adminTranslation.admin_name_error, 'error');
        return false;
    } else if (pdfFile.name=='') {
        myToastr(adminTranslation.admin_pdf_error, 'error');
        return false;
    } else if (imageFile.name=='') {
        myToastr(adminTranslation.admin_image_error, 'error');
        return false;
    } else {
        return true;
    }
}

function validateEditEpaper(formid) {

    var $form = $("#" + formid);
    var data = new FormData($form[0]);
    var existingImageSrc = document.getElementById('image-preview_formid').src;
    
    var imageFile = data.get('image');
    var pdfFile = data.get('pdf');
    if (data.get('name') == '') {
        myToastr(adminTranslation.admin_name_error, 'error');
        return false;
    }
    
    if (existingImageSrc.includes('image_preview.png')) {
        myToastr(adminTranslation.admin_image_error, 'error');
        return false;
    }
}
</script>