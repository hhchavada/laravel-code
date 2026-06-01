@extends('admin/layout/app')
@section('content')
<script src="{{ asset('admin-assets/js/ckeditor.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/select2/select2.css')}}" />
<style>
.ck-editor__editable {
    height: 200px;
    overflow-y: auto;
}

.ck-editor__editable_inline{
    height: 200px;
    overflow-y: auto;    
}
</style>

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
    <form id="edit-record" onsubmit="return validatePost('edit-record');" action="{{url('admin/update-post')}}" method="POST" enctype="multipart/form-data">
        @csrf   
        <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/post')}}"> {{__('lang.admin_blog')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_edit_blog')}}</h4>
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
            <div class="col">
                <div class="card mb-3">
                    
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-personal"
                            role="tab"
                            aria-selected="true"
                            >
                            {{__('lang.admin_blog_basic_info')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-account"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_seo_details')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-source"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_source_details')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-social"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_visibility')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-voting"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_voting_pool_question')}}
                            </button>
                        </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                            <input type="hidden" name="id" value="{{$row->id}}">
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
                                    <input type="text" id="title" class="form-control" name="title" placeholder="{{__('lang.admin_title_placeholder')}}" value="{{$row->title}}" onkeypress="setValue('seo_title',this.value);" onBlur="setValue('seo_title',this.value);" />
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="description">{{__('lang.admin_description')}} <span class="required">*</span></label>
                                    <textarea class="form-control" name="description" id="editor" placeholder="{{__('lang.admin_description_placeholder')}}" ><?php echo $row->description;?></textarea>
                                </div>
                                <div class="col-md-6 select2-primary">
                                    <label class="form-label" for="video_url">{{__('lang.admin_youtube_url')}}</label>
                                    <input type="text" id="video_url" class="form-control" name="video_url" placeholder="{{__('lang.admin_youtube_url_placeholder')}}" value="{{$row->video_url}}" />
                                </div>
                                @if(Carbon\Carbon::parse($row->schedule_date) > Carbon\Carbon::now())
                                <div class="col-md-6">
                                    <label class="form-label" for="schedule_date">{{__('lang.admin_schedule_date')}}</label>
                                    <input type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="schedule_date" readonly="readonly" value="{{$row->schedule_date}}"/>
                                </div>
                                @else
                                    <input hidden type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="schedule_date" readonly="readonly" value="{{$row->schedule_date}}"/>
                                @endif
                            
                            </div>
                            <div class="col-md-12" style="margin-top: 15px;">
                                <!-- Hidden input to track total image count -->
                                <input type="hidden" id="totalImageCount" value="0">
                                <input type="hidden" id="removedImages" name="removed_images" value="">
                                <label>{{__('lang.admin_image')}} <span class="required">*</span> <span>({{__('lang.admin_note_for_multiple_image_upload')}})</span></label>
                                <div class="file-upload-wrapper mt-3" id="upload-area" style="cursor: pointer;">
                                    <label for="files" class="custom-file-label" style="cursor: pointer;">
                                        <p style="margin-top: 10px;padding-top: 10px;">{{__('lang.admin_resolution_background_image')}} <br>
                                        {{__('lang.admin_click_here_to_upload_images')}}
                                        </p>
                                    </label>
                                    <input type="file" id="files" name="image[]" multiple accept="image/*" style="display: none;">
                                </div>
                                <div class="preview-container" id="preview"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">                    
                            <div class="row g-3">                        
                                <div class="col-md-6">
                                    <label class="form-label" for="tags">{{__('lang.admin_tags')}}</label>
                                    <input id="TagifyBasic" class="form-control" name="tags" value="{{$row->tags}}"  placeholder="{{__('lang.admin_tags_placeholder')}}" onkeypress="setValue('seo_tag',this.value);" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="seo_title">{{__('lang.admin_title')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input type="text" id="seo_title" class="form-control" name="seo_title" placeholder="{{__('lang.admin_title_placeholder')}}" value="{{$row->seo_title}}"/>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="seo_keyword">{{__('lang.admin_keyword')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input type="text" id="seo_keyword" class="form-control" name="seo_keyword" placeholder="{{__('lang.admin_keyword_placeholder')}}" value="{{$row->seo_keyword}}"/>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="seo_tag">{{__('lang.admin_tags')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input id="seo_tag" class="form-control" name="seo_tag" value="{{$row->seo_tag}}" placeholder="{{__('lang.admin_tags_placeholder')}}" />
                                </div>
                                <div class="col-md-12">
                                    <div class="form-password-toggle">
                                    <label class="form-label" for="seo_description">{{__('lang.admin_description')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <textarea id="seo_description" class="form-control" name="seo_description" value="{{$row->seo_description}}" placeholder="{{__('lang.admin_description_placeholder')}}">{{$row->seo_description}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="form-tabs-source" role="tabpanel">                    
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="source_name">{{__('lang.admin_source_name')}}</label>
                                    <input type="text" id="source_name" class="form-control" name="source_name" placeholder="{{__('lang.admin_source_name_placeholder')}}" value="{{$row->source_name}}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="source_link">{{__('lang.admin_source_link')}}</label>
                                    <input id="source_link" class="form-control" name="source_link" placeholder="{{__('lang.admin_source_link_placeholder')}}" value="{{$row->source_link}}" />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="form-tabs-social" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-check form-check-primary mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" @if($row->is_featured==1) checked @endif>
                                        <label class="form-check-label" for="is_featured">{{__('lang.admin_featured')}}</label>
                                    </div>
                                </div>
                                @foreach($visibility as $visibility_data)
                                <div class="col-md-12">
                                    <div class="form-check form-check-primary mt-3">
                                        <input class="form-check-input" type="checkbox" name="visibillity[]" id="visibillity_{{$visibility_data->id}}" value="{{$visibility_data->id}}" @if(isset($row->visibilityArr) && count($row->visibilityArr)) @if(in_array($visibility_data->id,$row->visibilityArr)) checked @endif @endif>
                                        <label class="form-check-label" for="visibillity">{{$visibility_data->display_name}}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>                    
                        </div>
                        <div class="tab-pane fade" id="form-tabs-voting" role="tabpanel">
                            <input type="hidden" id="optionCount" value="@if(isset($row->optionArr) && count($row->optionArr)){{count($row->optionArr)}}@endif">
                            <div >
                                <div class="row g-3" >
                                    <div class="col-md-12">
                                        <label class="switch switch-square">
                                            <input type="checkbox" class="switch-input" id="is_voting_enable" name="is_voting_enable" onchange="showQuestion('is_voting_enable');" @if(isset($row) && $row!='') @if($row->is_voting_enable==1) checked @endif @endif>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">{{__('lang.admin_enable_voting')}}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-12 showQuestion @if(isset($row) && $row!='') @if($row->is_voting_enable==1) checked @else hide @endif @else hide @endif">
                                        <label class="form-label" for="question">{{__('lang.admin_question')}}</label>
                                        <input type="hidden" name="question_id" value="{{$row->question_id}}">
                                        <input type="text" id="question" class="form-control" name="question" placeholder="{{__('lang.admin_question_placeholder')}}" value="{{$row->question}}" />
                                    </div>
                                </div>
                                <div class="showQuestion @if(isset($row) && $row!='') @if($row->is_voting_enable==1) checked @else hide @endif @else hide @endif" >
                                    @if(isset($row->optionArr) && count($row->optionArr))
                                        @php $k=0; @endphp
                                        @foreach($row->optionArr as $optionArr)                                           
                                            <div class="row">
                                                <input type="hidden" name="option_id[]" value="{{$optionArr->id}}">
                                                <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                                                    <label class="form-label" for="option">{{__('lang.admin_option')}}</label>
                                                    <input type="text" id="option" class="form-control" name="option[]" placeholder="{{__('lang.admin_option_placeholders')}}" value="{{$optionArr->option}}" />
                                                </div>
                                                @if($k>1)
                                                <div class="mb-3 col-lg-12 col-xl-2 col-12 d-flex align-items-center mb-0">
                                                    <button type="button" onclick="addRemoveOptions('remove');" class="btn btn-label-danger mt-4" data-repeater-delete>
                                                    <i class="ti ti-x ti-xs me-1"></i>
                                                    <span class="align-middle">{{__('lang.admin_delete')}}</span>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>                                            
                                            <hr />
                                            @php $k++; @endphp
                                        @endforeach
                                    @else
                                        <div class="row">
                                            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                                                <label class="form-label" for="option">{{__('lang.admin_option')}}</label>
                                                <input type="text" id="option" class="form-control option" name="option[]" placeholder="{{__('lang.admin_option_placeholders')}}"/>
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="row">
                                            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                                                <label class="form-label" for="option">{{__('lang.admin_option')}}</label>
                                                <input type="text" id="option" class="form-control option" name="option[]" placeholder="{{__('lang.admin_option_placeholders')}}" />
                                            </div>
                                        </div>
                                        <hr />
                                    @endif
                                </div>
                                <div class="showMoreOptions">

                                </div>
                                <div class="mb-0 showQuestion addOption @if(isset($row) && $row!='') @if($row->is_voting_enable==1) checked @else hide @endif @else hide @endif" >
                                <button type="button" onclick="addRemoveOptions('add');" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i>
                                    <span class="align-middle">{{__('lang.admin_add_option')}}</span>
                                </button>
                                </div>
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
   .then(editor => {
        editorInstance = editor;
        const prefilledValue = "<?=$row->description?>";
        editor.setData(prefilledValue);
        // Set the prefilled value on keyup event
        editor.model.document.on('change', () => {
            const updatedValue = editor.getData();
            var stripedHtml = updatedValue.replace(/<[^>]+>/g, '');
            document.getElementById('seo_description').value = stripedHtml;
        });
        // Set the height of the editor's content area
        editor.ui.view.editable.element.style.height = '200px';
    }).catch(error => {
        console.log(error);
    });
    
    
    function validatePost(formid) {
        // Validation logic
        var $form = $("#" + formid);
        var data = new FormData($form[0]);

        // Get CKEditor content
        const editorData = editorInstance ? editorInstance.getData() : '';

        // Get selected category IDs
        var category_id = $(".category_id").map(function(){ return $(this).val(); }).get();

        // Get the total image count from hidden input
        var totalImageCount = $('#totalImageCount').val();
        // Validation checks
        if (category_id.length == 0) {
            myToastr(adminTranslation.admin_category_error, 'error');
            return false;
        } else if (data.get('title') == '') {
            myToastr(adminTranslation.admin_title_error, 'error');
            return false;
        } else if (editorData.trim() === '') {
            myToastr(adminTranslation.admin_description_error, 'error');
            return false;
        } else if (totalImageCount == 0) {
            myToastr('Please select at least one image', 'error');
            return false;
        } else {
            return true;  // All validation passed
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