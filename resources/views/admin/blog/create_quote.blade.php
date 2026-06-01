@extends('admin/layout/app')
@section('content')
<script src="{{ asset('admin-assets/js/ckeditor.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/tagify/tagify.css')}}" />

<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/select2/select2.css')}}" />

<style>
    .file-upload-wrapper {
        border: 2px dashed #007bff;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .file-upload-wrapper:hover {
        background-color: #f7f7f7;
    }

    .file-upload-wrapper input[type="file"] {
        display: none;
    }

    .preview-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .preview-container .preview {
        position: relative;
        margin: 10px;
    }

    .preview-container img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .preview-container .remove-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <form id="add-record" onsubmit="return validateAddQuotes('add-record');" action="{{url('admin/add-quote')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/post')}}"> {{__('lang.admin_blog')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_add_quote')}}</h4>
        <div class="float-right py-3">
            <input type="submit" id="publish" name="button_name" class="btn btn-success me-sm-3 me-1" value="{{__('lang.admin_publish')}}"/>
            <input type="submit" id="submit" name="button_name" class="btn btn-primary me-sm-3 me-1" value="{{__('lang.admin_submit')}}"/>
            <input type="submit" id="draft" name="button_name" class="btn btn-warning me-sm-3 me-1" value="{{__('lang.admin_draft')}}"/>
            <a href="{{url('admin/post')}}" class="btn btn-label-secondary">{{__('lang.admin_button_cancel')}}</a>
        </div>
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                <div class="card-body">
                        <input type="hidden" name="type" value="{{Request::segment('4')}}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="formtabs-first-name">{{__('lang.admin_category')}} <span class="required">*</span></label>
                                <select id="category_id" class="select2 form-select category_id" placeholder="Select Category" name="category_id[]" multiple onchange="showSubCategory('category_id','subCategory');">
                                    <option value="">{{__('lang.admin_select_category')}}</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="formtabs-first-name">{{__('lang.admin_subcategory')}}</label>
                                <select id="sub_category_id" class="select2 form-select sub_category_id subCategory" name="sub_category_id[]" multiple>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="title">{{__('lang.admin_title')}} <span class="required">*</span></label>
                                <input type="text" id="title" class="form-control" name="title" placeholder="{{__('lang.admin_title_placeholder')}}" />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="description">{{__('lang.admin_description')}}</label>
                                <textarea class="form-control" name="description" id="editor" placeholder="{{__('lang.admin_description_placeholder')}}" ></textarea>
                            </div>
                            <div class="col-md-6 select2-primary">
                                    <label class="form-label" for="video_type">{{__('lang.admin_type')}}</label>
                                    <select class="form-control video_type_cls" name="video_type">
                                        <option value="youtube_url" >{{__('lang.admin_youtube_url')}}</option>
                                      </select>
                                </div>

                                <div class="col-md-6 select2-primary youtube_url_input_cls">
                                    <label class="form-label" for="video_url">{{__('lang.admin_youtube_url')}}</label>
                                    <input type="text" id="video_url" class="form-control" name="video_url" placeholder="{{__('lang.admin_youtube_url_placeholder')}}" accept="video/*"/>
                                </div>
                            <div class="col-md-6">
                                <label class="form-label" for="schedule_date">{{__('lang.admin_schedule_date')}}</label>
                                <input type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="schedule_date" readonly="readonly"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-md-12" style="margin-top: 15px;">
                                <label>{{__('lang.admin_image')}} <span class="required">*</span> <span>({{__('lang.admin_note_for_multiple_image_upload')}})</span></label>
                                <div class="file-upload-wrapper mt-3" id="upload-area">
                                    <p style="margin-top: 10px;padding-top: 10px;">{{__('lang.admin_quote_resolution_background_image')}} <br>
                                    {{__('lang.admin_click_here_to_upload_images')}}
                                    </p>
                                    <input type="file" id="files" name="image[]" multiple accept="image/*">
                                </div>
                                <div class="preview-container" id="preview"></div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    ClassicEditor
    .create(document.querySelector('#editor'), {
    })
    .catch(error => {
        console.log(error);
    });
    
    
    function validateAddQuotes(formid) {
        var $form = $("#" + formid);
        var data = new FormData($form[0]);
        var imageFile = document.getElementById('files').files;
        var category_id = $(".category_id").map(function(){return $(this).val();}).get();
        if(category_id.length==0){
            myToastr(adminTranslation.admin_category_error, 'error');
            return false;
        }else if (data.get('title') == '') {
            myToastr(adminTranslation.admin_title_error, 'error');
            return false;
        } else if (imageFile.name=='') {
            myToastr(adminTranslation.admin_background_image_error, 'error');
            return false;
        }else if (imageFile.length === 0) {
            myToastr(adminTranslation.admin_select_an_image_error, 'error');  // Display toastr if no image is selected
            return false;
        } else {
            return true;
        }
    }
</script>
<script>
    const fileInput = document.getElementById('files');
    const previewContainer = document.getElementById('preview');
    const uploadArea = document.getElementById('upload-area');
    const submitBtn = document.getElementById('submitBtn');

    let fileList = [];  // Store all selected files here

    // Trigger file input on click
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Handle file input changes
    fileInput.addEventListener('change', function (e) {
        handleFiles(this.files);
    });

    // Function to handle file input and display preview
    function handleFiles(files) {
        previewContainer.innerHTML = '';  //pwn
        Array.from(files).forEach((file, index) => {
            fileList.push(file);  // Add new files to the existing list

            const reader = new FileReader();
            reader.onload = function (event) {
                const previewDiv = document.createElement('div');
                previewDiv.classList.add('preview');
                const img = document.createElement('img');
                img.src = event.target.result;
                previewDiv.appendChild(img);

                // Create Remove Button
                const removeBtn = document.createElement('button');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerHTML = '&times;';
                removeBtn.setAttribute('data-index', fileList.length - 1);  // Set index for removal

                // Remove button event
                removeBtn.addEventListener('click', function () {
                    removeImage(removeBtn.getAttribute('data-index'));
                });

                previewDiv.appendChild(removeBtn);
                previewContainer.appendChild(previewDiv);
            };
            reader.readAsDataURL(file);
        });

        submitBtn.classList.remove('d-none');
    }

    // Function to remove image preview
    function removeImage(index) {
        fileList.splice(index, 1);  // Remove from file list
        updatePreview();  // Rebuild the preview UI
    }

    // Update the preview container
    function updatePreview() {
        previewContainer.innerHTML = '';  // Clear previews
        fileList.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (event) {
                const previewDiv = document.createElement('div');
                previewDiv.classList.add('preview');
                const img = document.createElement('img');
                img.src = event.target.result;
                previewDiv.appendChild(img);

                // Create Remove Button
                const removeBtn = document.createElement('button');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerHTML = '&times;';
                removeBtn.setAttribute('data-index', index);  // Update index for the remove button

                // Remove button event
                removeBtn.addEventListener('click', function () {
                    removeImage(index);  // Call remove function
                });

                previewDiv.appendChild(removeBtn);
                previewContainer.appendChild(previewDiv);
            };
            reader.readAsDataURL(file);
        });

        // Hide submit button if no files are left
        if (fileList.length === 0) {
            submitBtn.classList.add('d-none');
        }
    }
</script>
@endsection