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
    <form id="add-record" onsubmit="return validatePost('add-record');" action="{{url('admin/add-post')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <!--  -->
        <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/post')}}"> {{__('lang.admin_blog')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_add_blog')}}</h4>
        <div class="float-right py-3">
            <input type="submit" id="publish" name="button_name" class="btn btn-success me-sm-3 me-1" value="{{__('lang.admin_publish')}}"/>
            <input type="submit" id="submit" name="button_name" class="btn btn-primary me-sm-3 me-1" value="{{__('lang.admin_submit')}}"/>
            <input type="submit" id="draft" name="button_name" class="btn btn-warning me-sm-3 me-1" value="{{__('lang.admin_draft')}}"/>
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
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="formtabs-first-name"> {{__('lang.admin_category')}} <span class="required">*</span></label>
                                    <select id="category_id" class="select2 form-select category_id" placeholder="{{__('lang.admin_select_category')}}" name="category_id[]" multiple onchange="showSubCategory('category_id','subCategory');">
                                        <option value="">{{__('lang.admin_select_category')}}</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="formtabs-first-name"> {{__('lang.admin_subcategory')}}</label>
                                    <select id="sub_category_id" class="select2 form-select sub_category_id subCategory" name="sub_category_id[]" multiple placeholder="{{__('lang.admin_subcategory')}}">
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="title"> {{__('lang.admin_title')}} <span class="required">*</span></label>
                                    <input type="text" id="title" class="form-control" name="title" placeholder="{{__('lang.admin_title_placeholder')}}" onkeypress="setValue('seo_title',this.value);" onBlur="setValue('seo_title',this.value);" />
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="description">{{__('lang.admin_description')}} <span class="required">*</span></label>
                                    <textarea class="form-control" name="description" id="editor" placeholder="{{__('lang.admin_description_placeholder')}}" row="6"></textarea>
                                </div>
                                <div class="col-md-6 select2-primary">
                                    <label class="form-label" for="video_url">{{__('lang.admin_youtube_url')}}</label>
                                    <input type="text" id="video_url" class="form-control" name="video_url" placeholder="{{__('lang.admin_youtube_url_placeholder')}}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="schedule_date">{{__('lang.admin_schedule_date')}}</label>
                                    <input type="text" class="form-control flatpickr-input active flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM AA" name="schedule_date" readonly="readonly"/>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top: 15px;">
                                <label>{{__('lang.admin_image')}} <span class="required">*</span> <span>({{__('lang.admin_note_for_multiple_image_upload')}})
                                </span></label>
                                <div class="file-upload-wrapper mt-3" id="upload-area">
                                    <p style="margin-top: 10px;padding-top: 10px;">{{__('lang.admin_resolution_background_image')}} <br>
                                    {{__('lang.admin_click_here_to_upload_images')}}
                                    </p>
                                    <input type="file" id="files" name="image[]" multiple accept="image/*">
                                </div>
                                <div class="preview-container" id="preview"></div>
                            </div>                        
                        </div>
                        <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">                    
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="tags">{{__('lang.admin_tags')}}</label>
                                    <input id="TagifyBasic" class="form-control" placeholder="{{__('lang.admin_tags_placeholder')}}" name="tags"  onblur="getTags();"/>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="seo_title">{{__('lang.admin_title')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input type="text" id="seo_title" class="form-control" name="seo_title" placeholder="{{__('lang.admin_title_placeholder')}}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="seo_keyword">{{__('lang.admin_keyword')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input type="text" id="seo_keyword" class="form-control" name="seo_keyword" placeholder="{{__('lang.admin_keyword_placeholder')}}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="seo_tag">{{__('lang.admin_tags')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <input id="seo_tag" class="form-control" name="seo_tag" placeholder="{{__('lang.admin_tags_placeholder')}}" />
                                </div>
                                <div class="col-md-12">
                                    <div class="form-password-toggle">
                                    <label class="form-label" for="seo_description">{{__('lang.admin_description')}} ({{__('lang.admin_meta_tag')}})</label>
                                    <textarea id="seo_description" class="form-control" name="seo_description" placeholder="{{__('lang.admin_description_placeholder')}}"></textarea>
                                    </div>
                                </div>
                            </div>                    
                        </div>
                        <div class="tab-pane fade" id="form-tabs-source" role="tabpanel">                    
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="source_name">{{__('lang.admin_source_name')}}</label>
                                    <input type="text" id="source_name" class="form-control" name="source_name" placeholder="{{__('lang.admin_source_name_placeholder')}}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="source_link">{{__('lang.admin_source_link')}}</label>
                                    <input id="source_link" class="form-control" name="source_link" placeholder="{{__('lang.admin_source_link_placeholder')}}" />
                                </div>
                            </div>                    
                        </div>
                        <div class="tab-pane fade" id="form-tabs-social" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-check form-check-primary mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured">
                                        <label class="form-check-label" for="is_featured">{{__('lang.admin_featured')}}</label>
                                    </div>
                                </div>
                                @foreach($visibility as $visibility_data)
                                <div class="col-md-12">
                                    <div class="form-check form-check-primary mt-3">
                                        <input class="form-check-input" type="checkbox" name="visibillity[]" id="visibillity_{{$visibility_data->id}}" value="{{$visibility_data->id}}">
                                        <label class="form-check-label" for="visibillity">{{$visibility_data->display_name}}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="tab-pane fade" id="form-tabs-voting" role="tabpanel">
                            <input type="hidden" id="optionCount" value="2">
                            <div class="form-repeater">
                                <div class="row g-3" >
                                    <div class="col-md-12">
                                        <label class="switch switch-square">
                                            <input type="checkbox" class="switch-input" id="is_voting_enable" name="is_voting_enable" onchange="showQuestion('is_voting_enable');">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">{{__('lang.admin_enable_voting')}}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-12 showQuestion hide">
                                        <label class="form-label" for="question">{{__('lang.admin_question')}}</label>
                                        <input type="text" id="question" class="form-control" name="question" placeholder="{{__('lang.admin_question_placeholder')}}" />
                                    </div>
                                </div>
                                
                                <div class="showQuestion hide">
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
                                </div>
                                <div class="showMoreOptions showQuestion hide">
                                    
                                </div>
                                <div class="mb-0 showQuestion addOption hide" >
                                <button type="button" onclick="addRemoveOptions('add');" class="btn btn-primary" data-repeater-create>
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
        height: '200px'
    })
    .then(editor => {
        editorInstance = editor;
        const prefilledValue = document.getElementById('seo_description').value;
        editor.setData(prefilledValue);
        // Set the prefilled value on keyup event
        editor.model.document.on('change', () => {
            const updatedValue = editor.getData();
            var stripedHtml = updatedValue.replace(/<[^>]+>/g, '');
            document.getElementById('seo_description').value = stripedHtml;
        });
    }).catch(error => {
        console.log(error);
    });
    
    function validatePost(formid) {
        var $form = $("#" + formid);
        var data = new FormData($form[0]);

        // Get the value of the image file input (for newly selected files)
        var imageFile = document.getElementById('files').files;

        // Retrieve CKEditor content
        const editorData = editorInstance ? editorInstance.getData() : '';

        // Get selected category IDs
        var category_id = $(".category_id").map(function(){ return $(this).val(); }).get();

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
        } 
        // Check if no image was uploaded (both on add form and file input is empty)
        else if (imageFile.length === 0) {
            myToastr(adminTranslation.admin_select_an_image_error, 'error');  // Display toastr if no image is selected
            return false;
        } else {
            return true;  // All validation passed
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