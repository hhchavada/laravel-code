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
    <form id="edit-record" onsubmit="return validateEditQuotes('edit-record');" action="{{url('admin/update-quote')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$row->id}}">
        <!--  -->
        <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/post')}}"> {{__('lang.admin_blog')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_edit_quote')}}</h4>
        <div class="float-right py-3">
            @if($row->status==2)
                <input type="submit" id="publish" name="button_name" class="btn btn-success me-sm-3 me-1" value="{{__('lang.admin_publish')}}"/>
                <input type="submit" id="submit" name="button_name" class="btn btn-primary me-sm-3 me-1" value="{{__('lang.admin_submit')}}"/>
            @elseif($row->status==3 || $row->status==0)
                <input type="submit" id="publish" name="button_name" class="btn btn-success me-sm-3 me-1" value="{{__('lang.admin_publish')}}"/>         
            @endif
            <input type="submit" id="update" name="button_name" class="btn btn-primary me-sm-3 me-1" value="{{__('lang.admin_update')}}"/>
            <a href="{{url('admin/post')}}" class="btn btn-label-secondary">{{__('lang.admin_button_cancel')}}</a>
        </div>
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="formtabs-first-name">{{__('lang.admin_category')}} <span class="required">*</span></label>
                                <select id="category_id" class="select2 form-select category_id" name="category_id[]" multiple onchange="showSubCategory('category_id','subCategory');">
                                    <option value="">{{__('lang.admin_select_category')}}</option>
                                    @foreach($categories as $category)
                                        <option @if(isset($row->categoryArr) && count($row->categoryArr)) @if(in_array($category->id,$row->categoryArr)) selected @endif @endif value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="formtabs-first-name">{{__('lang.admin_subcategory')}}</label>
                                <select id="sub_category_id" class="select2 form-select sub_category_id subCategory" name="sub_category_id[]" multiple>
                                    @if(isset($subcategory) && count($subcategory))
                                        @foreach($subcategory as $subcategory_data)
                                            <option value="{{$subcategory_data->id}}" @if(isset($row->subcategoryArr) && count($row->subcategoryArr)) @if(in_array($subcategory_data->id,$row->subcategoryArr)) selected @endif @endif>{{$subcategory_data->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="title">{{__('lang.admin_title')}} <span class="required">*</span></label>
                                <input type="text" id="title" class="form-control" name="title" placeholder="{{__('lang.admin_title_placeholder')}}" value="{{$row->title}}" />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="description">{{__('lang.admin_description')}}</label>
                                <textarea class="form-control" name="description" id="editor" placeholder="{{__('lang.admin_description_placeholder')}}" value="{{$row->description}}">{{$row->description}}</textarea>
                            </div>
                                <div class="col-md-6 select2-primary">
                                    <label class="form-label" for="video_url">{{__('lang.admin_type')}}</label>
                                    <select class="form-control video_type_cls" name="video_type">
                                        <option <?=$row->video_url != '' ? 'selected' : '';?> value="youtube_url" >{{__('lang.admin_youtube_url')}}</option>
                                      </select>
                                </div>

                                <div class="col-md-6 select2-primary youtube_url_input_cls">
                                    <label class="form-label" for="video_url">{{__('lang.admin_youtube_url')}}</label>
                                    <input type="text" id="video_url" class="form-control" name="video_url" placeholder="{{__('lang.admin_youtube_url_placeholder')}}" value="{{$row->video_url}}"/>
                                </div>
                            
                            @if(Carbon\Carbon::parse($row->schedule_date) > Carbon\Carbon::now())
                            <div class="col-md-6">
                                <label class="form-label" for="schedule_date">{{__('lang.admin_schedule_date')}}</label>
                                <input type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="schedule_date" readonly="readonly" value="{{$row->schedule_date}}"/>
                            </div>
                            @else
                                <input hidden type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="schedule_date" readonly="readonly" value="{{$row->schedule_date}}"/>
                            @endif
                                
                            <div class="col-md-6">
                                <label class="form-label" for="created_at">{{__('lang.admin_created_date')}}</label>
                                <input type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="created_at" readonly="readonly" value="{{$row->created_at}}"/>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 15px;">
                            <!-- Hidden input to track total image count -->
                            <input type="hidden" id="totalImageCount" value="0">
                            <input type="hidden" id="removedImages" name="removed_images" value="">
                            <label>{{__('lang.admin_image')}} <span class="required">*</span> <span>({{__('lang.admin_note_for_multiple_image_upload')}})</span></label>
                            <div class="file-upload-wrapper mt-3" id="upload-area" style="cursor: pointer;">
                                <label for="files" class="custom-file-label" style="cursor: pointer;">
                                    <p style="margin-top: 10px;padding-top: 10px;">{{__('lang.admin_quote_resolution_background_image')}} <br>
                                    {{__('lang.admin_click_here_to_upload_images')}}
                                    </p>
                                </label>
                                <input type="file" id="files" name="image[]" multiple accept="image/*" style="display: none;">
                            </div>
                            <div class="preview-container" id="preview"></div>
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
    
    function validateEditQuotes(formid) {
        var $form = $("#" + formid);
        var data = new FormData($form[0]);
        var totalImageCount = $('#totalImageCount').val();
        var category_id = $(".category_id").map(function(){return $(this).val();}).get();
        if(category_id.length==0){
            myToastr(adminTranslation.admin_category_error, 'error');
            return false;
        }else if (data.get('title') == '') {
            myToastr(adminTranslation.admin_title_error, 'error');
            return false;
        }else if (totalImageCount == 0) {
            myToastr('Please select at least one image', 'error');
            return false;
        } else {
            return true;
        }
    }
</script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const previewContainer = document.getElementById('preview');
        const fileInput = document.getElementById('files');
        const totalImageCountInput = document.getElementById('totalImageCount'); // Hidden input
        const removedImagesInput = document.getElementById('removedImages'); // Hidden input for removed images
        const existingImages = {!! json_encode($row->images) !!}; // Pass the entire image objects to know the IDs

        let fileList = [];  // This will store new files being uploaded
        let totalImageCount = existingImages.length; // Initialize count from existing images
        let removedImages = []; // Array to track removed images
        totalImageCountInput.value = totalImageCount; // Set initial value to the hidden input

        // Display existing images
        existingImages.forEach((image) => {
            const previewDiv = document.createElement('div');
            previewDiv.classList.add('preview');
            
            const img = document.createElement('img');
            img.src = base_url + `/uploads/blog/768x428/${image.image}`;  // Correct path to the image
            previewDiv.appendChild(img);

            // Create Remove Button
            const removeBtn = document.createElement('button');
            removeBtn.classList.add('remove-btn');
            removeBtn.innerHTML = '&times;';
            removeBtn.setAttribute('data-name', image.image);  // Store the image name for removal
            removeBtn.addEventListener('click', function () {
                removeImage(previewDiv, image.image);  // Call remove image logic
            });

            previewDiv.appendChild(removeBtn);
            previewContainer.appendChild(previewDiv);
        });

        // Function to handle file input changes and display preview for new uploads
        fileInput.addEventListener('change', function (e) {
            Array.from(this.files).forEach((file) => {
                fileList.push(file);  // Add new files to the list
                totalImageCount++;  // Increase the total image count
                totalImageCountInput.value = totalImageCount;  // Update hidden input

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
                    removeBtn.setAttribute('data-new', fileList.length - 1);  // Set index for new files

                    removeBtn.addEventListener('click', function () {
                        removeImage(previewDiv, null, fileList.length - 1);  // Call remove image logic for new files
                    });

                    previewDiv.appendChild(removeBtn);
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        });

        // Function to remove an image (either existing or new)
        function removeImage(previewDiv, imageName = null, newIndex = null) {
            previewDiv.remove();  // Remove image from the preview UI
            totalImageCount--;  // Decrease the total image count
            totalImageCountInput.value = totalImageCount;  // Update the hidden input

            if (imageName !== null) {
                removedImages.push(imageName); // Track the removed existing image
                // Log removed images
                console.log('Removed images:', removedImages);
            } else if (newIndex !== null) {
                fileList.splice(newIndex, 1);  // Remove the new image from fileList if applicable
            }

            // Update the hidden input for removed images
            removedImagesInput.value = JSON.stringify(removedImages); // Store removed images as a JSON string
        }
    });
</script>
@endsection