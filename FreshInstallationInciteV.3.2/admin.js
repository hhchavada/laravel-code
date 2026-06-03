'use strict';
var optionCount = 0;
/* Show loader start */
function showLoader() {
    // alert(1);
    $('.loader').removeClass('hide');
    // $('#overlay').show();
}
/* Show loader end */

/* Hide loader start */
function hideLoader() {
    $('.loader').addClass('hide');
    // $('#overlay').hide();
}

function myToastr(msg, type) {
    toastr.remove();
    if (type == 'error') {
        toastr.error(msg);
    } else if (type == 'success') {
        toastr.success(msg);
    }
}

function triggerEmailsField(val){
    if (val == 'specific') {
        $('.emails').removeClass('hide');
    }else{
        $('.emails').addClass('hide');
    }

}

    function resetFilter() {
        var newURL = location.href.split("?")[0];
        console.log(newURL);
          window.history.pushState('object', document.title, newURL);
          location.reload();
    }

    function resetFilterfeed() {
        var newURL = location.href.split("?")[0];
        console.log(newURL);
        location.href = newURL;
        //   window.history.pushState('object', document.title, newURL);
        //   location.reload();
    }

    function resetFilterBlog() {
        var newURL = location.href.split("&")[0];
        console.log(newURL);
        location.href = newURL;
        //   window.history.pushState('object', document.title, newURL);
        //   location.reload();
    }

    function searchClick() {
        $('#search').click();
    }


$(document).ready(function () {
    $('.image-popup').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-fade',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        }
    });
});

$("#is_voting_enable").on('change', function(){
    if ($(this).is(':checked')) {
        $(".showTopicInput").removeClass('hide');
    }
    else{
        $(".showTopicInput").addClass('hide');
    }
});


function resetForm(formID){
    $("#"+formID).closest('form').find("input[type=text], input[type=number], textarea").val("");
    $("#"+formID).closest('form').find("input[type=checkbox]").removeAttr("checked");
    $("#"+formID).closest('form').find("input[type=radio]").removeAttr("checked");
    $("#custom").attr("checked",true);
    $('select').val('');
    $('#createBtn').html('Create');
    $('#image_add').attr('src','');
}



function printArea() {
    document.getElementById('iframeid').contentWindow.print();
}

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};
    var l = 0;
    $.map(unindexed_array, function (n, i) {
        if (n['name'] == 'category_id[]') {
            if (l == 0) {
                indexed_array['category_id'] = [];
            }
            indexed_array['category_id'].push(n['value']);
            l++;
        } else {
            indexed_array[n['name']] = n['value'];
        }
    });
    return indexed_array;
}

function validateEmail(email) {
    var x = email;
    var atpos = x.indexOf("@");
    var dotpos = x.lastIndexOf(".");
    if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= x.length) {
        return true;
    } else {
        return false;
    }
}

function setDataLimit(limit, getData, type, portal) {
    var url;
    if (getData == 'NA') {
        window.location.href = base_url + "/" + portal + "/" + type + "?per_page=" + limit;
    } else {
        var res = getData.split("&");
        var myarray = [];
        $.each(res, function (key, value) {
            var res1 = value.split("=");
            if (res1[0] == 'per_page') {
                res1[1] = limit;
            }
            var newRes = res1.join("=");
            myarray.push(newRes);
        });
        var newUrl = myarray.join("&");
        if (newUrl) {
            window.location.href = base_url + "/" + portal + "/" + type + "?" + newUrl;
        }
    }
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


var importerobjects = [];
$("#postal").typeahead({
    source: function(query, process) {
        console.log(query);
        if ($("#postal").val() != "") {
            var path = base_url + "/autocomplete";
            var map = {};
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
            $.post(path, {
                term: query.toString()
            }, function(data) {
                console.log(data);
                if (data) {
                    $.each(data, function(i, object) {
                        importerobjects.push(object.email);
                    });
                }
                return process(importerobjects);
            });
        }
    },
});

function add_category(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.name != '') {
        if (data.color != '') {
            if (blogThumbImage != undefined || data.id) {
                $.ajax({
                    type: 'POST',
                    url: base_url + "/add-update-category",
                    headers: {},
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        if (response.success) {
                            myToastr(response.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 500);
                        } else {
                            myToastr(response.message, 'error');
                        }
                    }
                });
            } else {
                myToastr('Please Upload Category Image', 'error');
            }
        } else {
            myToastr('Select category color', 'error');
        }
    } else {
        myToastr('Enter category', 'error');
    }
}

function add_rss_feed_src(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
    if (data.category_id != '') {
        if (data.rss_name != '') {
            if (data.rss_url != '') {
                if (/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(data.rss_url)) {
                    $.ajax({
                        type: 'POST',
                        url: base_url + "/add-update-rss-feed-src",
                        headers: {},
                        contentType: 'application/json',
                        dataType: 'json',
                        data: JSON.stringify(data),
                        success: function (response) {
                            if (response.success) {
                                myToastr(response.message, 'success');
                                setTimeout(function () {
                                    window.location.reload();
                                }, 500);
                            } else {
                                myToastr(response.message, 'error');
                            }
                        }
                    });
                } else {
                    myToastr('Please enter valid URL', 'error');
                }
            } else {
                myToastr('Please enter URL', 'error');
            }
        } else {
            myToastr('Enter name', 'error');
        }
    } else {
        myToastr('Select category', 'error');
    }
}


var blogThumbImage;

function uploadCategoryThumbImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    if (id == 0) {
        var authorimage = 'thumb_image';
    } else {
        var authorimage = 'thumb_image_' + id;
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadCategoryThumbImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                blogThumbImage = data.data;
                                if (blogThumbImage != undefined) {
                                    $('#' + authorimage).val(blogThumbImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


function triggerFileInput(className) {
    $('.' + className).click();
}

var authorImage;

function uploadauthorImage(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var authorimage = 'authorimage' + id;
    } else {
        createBtn = 'createBtn';
        authorimage = 'authorimage';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $("#show_cat_image_add").show();
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                category_image = data.data;
                                if (category_image != undefined) {
                                    $('#' + authorimage).val(category_image);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


function addUpdateAuthor(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    var flag = true;
    e.preventDefault();
    if (data.name == '') {
        flag = false;
        myToastr('Enter name', 'error');
    } else if (data.email == '') {
        flag = false;
        myToastr('Enter email', 'error');
    }

    if (flag) {
        $.ajax({
            type: 'POST',
            url: base_url + "/addUpdateAuthor",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}


var blogThumbImage;

function uploadblogThumbImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'thumb_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadBlogThumbImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                blogThumbImage = data.data;
                                if (blogThumbImage != undefined) {
                                    $('#' + authorimage).val(blogThumbImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

var backgroundImage;

function uploadBackgroundImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'background_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $("#bk_image_image_add").removeClass('hide');
                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('background_image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadBackgroundImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                backgroundImage = data.data;
                                if (backgroundImage != undefined) {
                                    $('#' + authorimage).val(backgroundImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


var BannerImage;

function uploadBannerImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'banner_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadBannerImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                BannerImage = data.data;
                                if (BannerImage != undefined) {
                                    $('#' + authorimage).val(BannerImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


var productMyltipleImages;
var blogImages = [];

function uploadMultipleBannerImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'banner_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    var form_data = new FormData();
    // Read selected files
    var totalfiles = document.getElementById('image').files.length;
    for (var index = 0; index < totalfiles; index++) {
        form_data.append("image[]", document.getElementById('image').files[index]);
    }
    $.ajax({
        url: base_url + '/uploadMultipleBannerImage',
        data: form_data,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            var productMyltipleimages_url = '';
            setTimeout(function () {
                if (response.status) {
                    if (id == 0) {
                        $("#" + previewid).show();
                    }
                    productMyltipleImages = response.data.images;
                    productMyltipleimages_url = response.data.images_url;
                    if(productMyltipleImages.length>0){
                        for (var index = 0; index < productMyltipleImages.length; index++) {
                            if ($.inArray(productMyltipleImages[index], blogImages) == -1) {
                                blogImages.push(productMyltipleImages[index]);
                            }
                            
                        }
                    }else{
                        console.log("Check");
                        for (var index = 0; index < productMyltipleImages.length; index++) {
                            if ($.inArray(productMyltipleImages[index], blogImages) == -1) {
                                blogImages.push(productMyltipleImages[index]);
                            }
                        }  
                    }
                    $('#' + createBtn).prop('disabled', false);
                    if ($('#productId').val()) {
                        $('#' + createBtn).html('Update');
                    } else {
                        $('#' + createBtn).html('Create');
                    }
                }
                for (var index = 0; index < productMyltipleimages_url.length; index++) {
                    var src = productMyltipleimages_url[index];
                    var cls = 'delete_div_' + index;
                    $('#preview').append('<div class="col-span-12 sm:col-span-12" style="float:left" id="' + cls + '"  ><div><img src="' + src + '" class="multipleUpload"></div></div>');
                }
            }, 10);
        }
    })
}





var audiofile;

function uploaudiofile(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var audio = 'audio_file_upload';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "mp3") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    // $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('audio_file', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadAudioFIle',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                audiofile = data.data.name;
                                var fullpath =  data.data.fullpath;
                                if (audiofile != undefined) {
                                    $("#"+previewid).show();
                                    $('#' + audio).val(audiofile);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                    setTimeout(()=>{
                                        $('#audiopreview').html(`<audio controls controlsList="nodownload"><source src="`+fullpath+`" type="audio/mp3"></audio>`);
                                    },500);
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only mp3 file', 'error');
        }
    }
}

function addUpdateBlog(e, formid,submittype) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    data.submittype = submittype;
    var selected = [];
    // selected = $('#language').val();

    selected = $("#language :selected").map((_, e) => e.value).get();
    var description = $(".ck-content").html();
    console.log(description);
    data.description = description;
//     var editor = CKEDITOR.instances['editorName'];
// var content = editor.getData();
// console.log(content);
    data.language_code = selected;
    console.log(data);
    var cat = $("#category_id").map(function(){return $(this).val();}).get();
    if(submittype=='draft'){
        data.image = productMyltipleImages;
        // var desc = CKEDITOR.instances['description'].getData();
        // data.description = desc;
        $.ajax({
            type: 'POST',
            url: base_url + "/addUpdateblogDraft",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.href = base_url + "/blog/side-menu/light";
                    }, 200);
                } else {
                    myToastr("something went wrong.", 'failure');
                    setTimeout(function () {
                        window.location.href = base_url + "/blog/side-menu/light";
                    }, 200);
                }
            }
        });
    }else{
        var flag = true;
        e.preventDefault();
        if(data.language == '') {
            flag = false;
            myToastr('Select language', 'error');
        } else if (cat.length == 0) {
            flag = false;
            myToastr('Select category', 'error');
        } else if (data.title == '') {
            flag = false;
            myToastr('Enter title', 'error');
        } else if (data.slug == '') {
            flag = false;
            myToastr('Enter slug', 'error');
        } else {
            data.image = blogImages;
            console.log(data);
            if (blogImages.length != 0 || data.id) {
                $('#createBtn').attr('disabled');
                $('#createBtn').text('Wait..');
                // var desc = CKEDITOR.instances['description'].getData();
                // data.description = desc;
                $.ajax({
                    type: 'POST',
                    url: base_url + "/addUpdateblog",
                    headers: {},
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        if (response.status) {
                            myToastr(response.message, 'success');
                            setTimeout(function () {
                                window.location.href = base_url + "/blog/side-menu/light";
                            }, 200);
                        } else {
                            if(data.submittype=='create'){
                                $('#createBtn').text('Create');
                            }else{
                                $('#createBtn').text('Update');
                            }                            
                            myToastr(response.message, 'error');
                        }
                    }
                });
            } else {
                flag = false;
                myToastr('Please select image', 'error');
                if(data.submittype=='create'){
                    $('#createBtn').text('Create');
                }else{
                    $('#createBtn').text('Update');
                } 
            }
        }
    }    
}

function exportfile()
{
    window.location = base_url+"/download-csv/";
}

function addUpdateQuotes(e, formid,submittype) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    data.submittype = submittype;    
    var flag = true;
    var cat = $("#category_id").map(function(){return $(this).val();}).get();
    var video_url = $(".video_url").map(function(){return $(this).val();}).get();
    e.preventDefault();
    if (cat.length == 0) {
        flag = false;
        myToastr('Select category', 'error');
    } else if (data.title == '') {
        flag = false;
        myToastr('Enter title', 'error');
    } else if (data.slug == '') {
        flag = false;
        myToastr('Enter slug', 'error');
    } else if (data.background_image == '') {
        flag = false;
        myToastr('Select background image.', 'error');
    } else {
        data.image = blogImages;
        data.video_url = video_url;
        console.log(data);
        // var desc = CKEDITOR.instances['description'].getData();
        // data.description = desc;
        $.ajax({
            type: 'POST',
            url: base_url + "/addUpdateQuotes",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if(data.submittype=='create'){
                    $('#createBtn').text('Create');
                }else{
                    $('#createBtn').text('Update');
                } 
                console.log(response);
                if (response.status) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.href = base_url + "/blog/side-menu/light";
                    }, 200);
                } else {
                    if(data.submittype=='create'){
                        $('#createBtn').text('Create');
                    }else{
                        $('#createBtn').text('Update');
                    }                            
                    myToastr(response.message, 'error');
                }
            }
        });
        // if (blogImages.length != 0 || data.id) {
        //     $('#createBtn').attr('disabled');
        //     $('#createBtn').text('Wait..');
            
        // } else {
        //     flag = false;
        //     myToastr('Please select image', 'error');
        //     if(data.submittype=='create'){
        //         $('#createBtn').text('Create');
        //     }else{
        //         $('#createBtn').text('Update');
        //     } 
        // }
    }   
}

function editAddress(value){
    console.log(value);
    $("#address_value").val(value);
    if($("#address").val()==''){
        $("#latitude").val('');
        $("#longitude").val('')
    }
}

function validateSlug(slug) {
    var flag = true;
    console.log(slug);
    // e.preventDefault();
    if (slug == '') {
        flag = false;
        myToastr('input slug', 'error');
    } else {
        var data = {};
        data.slug = slug;
        $.ajax({
            type: 'POST',
            url: base_url + "/validateSlug",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                console.log(response);
                if (response.status) {
                    // myToastr(response.message, 'success');
                } else {
                    // $('#createBtn').text('Update');
                    myToastr(response.message, 'error');
                }
            }
        });
    }  
}

var logoUpload;

function uploadLogoImage(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var logoimage = 'app_logo' + id;
    } else {
        createBtn = 'createBtn';
        logoimage = 'app_logo';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $("#show_cat_image_add").show();
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadLogoImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                logoUpload = data.data;
                                if (logoUpload != undefined) {
                                    $('#' + logoimage).val(logoUpload);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

var profileUpload;

function uploadProfileImage(input, previewid, type, id) {

    if (id != 0) {
        var createBtn = 'createBtn' + id;
        var logoimage = 'photo' + id;
    } else {
        createBtn = 'createBtn';
        logoimage = 'photo';
    }

    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $("#show_cat_image_add").show();
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadProfileImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                profileUpload = data.data;
                                if (profileUpload != undefined) {
                                    $('#' + logoimage).val(profileUpload);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Save');
                                    } else {
                                        $('#' + createBtn).html('Save');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

var bgUpload;

function uploadBgImage(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var authorimage = 'bg_image' + id;
    } else {
        createBtn = 'createBtn';
        authorimage = 'bg_image';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadBGImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                bgUpload = data.data;
                                if (bgUpload != undefined) {
                                    $('#' + authorimage).val(bgUpload);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Save');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


var siteLogo;

function uploadWebsiteLogo(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var authorimage = 'site_logo' + id;
    } else {
        createBtn = 'createBtn';
        authorimage = 'site_logo';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadLogoImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                siteLogo = data.data;
                                if (siteLogo != undefined) {
                                    $('#' + authorimage).val(siteLogo);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Save');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

var siteFavicon;

function uploadWebsiteFavicon(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var authorimage = 'site_favicon' + id;
    } else {
        createBtn = 'createBtn';
        authorimage = 'site_favicon';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "ico") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadLogoFavicon',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                siteFavicon = data.data;
                                if (siteFavicon != undefined) {
                                    $('#' + authorimage).val(siteFavicon);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Save');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

var liveNewsLogo;
function uploadLiveNewsLogo(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var authorimage = 'live_news_logo' + id;
    } else {
        createBtn = 'createBtn';
        authorimage = 'live_news_logo';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadLiveNewsLogo',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                liveNewsLogo = data.data;
                                if (liveNewsLogo != undefined) {
                                    $('#' + authorimage).val(liveNewsLogo);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Save');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

var EPaperLogo;
function uploadEPaperLogo(input, previewid, type, id) {
    if (id) {
        var createBtn = 'createBtn' + id;
        var authorimage = 'e_paper_logo' + id;
    } else {
        createBtn = 'createBtn';
        authorimage = 'e_paper_logo';
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    if (type == "add") {
                        $('#' + previewid).attr('src', e.target.result);
                    } else if (type == "update") {
                        $("#image_update_" + id + "").attr('src', e.target.result);
                    }
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadEpaperLogo',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                EPaperLogo = data.data;
                                if (EPaperLogo != undefined) {
                                    $('#' + authorimage).val(EPaperLogo);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Save');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


var BannerImage;

function uploadCmsBannerImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'banner_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');

    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadCMSBannerImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                BannerImage = data.data;
                                if (BannerImage != undefined) {
                                    $('#' + authorimage).val(BannerImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


function addUpdateCmsPage(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    var flag = true;
    e.preventDefault();
    if (data.title == '') {
        flag = false;
        myToastr('Enter title', 'error');
    }
    var description = $(".ck-content").html();
    console.log(description);
    data.description = description;
    // var desc = CKEDITOR.instances['blogdescription'].getData();
    // data.description = desc;
    if (flag) {
        $.ajax({
            type: 'POST',
            url: base_url + "/addUpdateCMSPage",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}


$(function () {

    $("#tablecontents_ads_images").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderToServer($("#ad_id").val());
        }
    });

    function sendOrderToServer(ad_id) {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/ads-media-sortable",
            data: {
                ad_id:ad_id,
                order: order,
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});

$(function () {

    $("#tablecontents_blog_images").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderToServer($("#blog_id").val());
        }
    });

    function sendOrderToServer(blog_id) {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/blog-media-sortable",
            data: {
                blog_id:blog_id,
                order: order,
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});

$(function () {

    $("#tablecontentschild").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderofChildToServer();
        }
    });

    function sendOrderofChildToServer(child_id) {
        console.log($(this).attr('data-blogid'))
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/child-card-sortable",
            data: {
                order: order,
                blog_id: $(".row1").attr('data-blogid'),
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});

$(function () {

    $("#tablecontentsads").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderofAdsToServer();
        }
    });

    function sendOrderofAdsToServer(ad_id) {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/ads-sortable",
            data: {
                order: order,
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});


$(function () {

    $("#tablecontents").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderToServer();
        }
    });

    function sendOrderToServer(ad_id) {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/category-sortable",
            data: {
                order: order,
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});

$(function () {

    $("#tablecontentsslider").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderOfSliderPost();
        }
    });

    function sendOrderOfSliderPost() {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/blog-sortable",
            data: {
                order: order,
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});

$(function () {

    $("#tablecontenttrending").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function () {
            sendOrderOfTrendingPost();
        }
    });

    function sendOrderOfTrendingPost() {
        var order = [];
        var token = $('meta[name="csrf-token"]').attr('content');
        $('tr.row1').each(function (index, element) {
            order.push({
                id: $(this).attr('data-id'),
                position: index + 1
            });
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + "/blog-sortable",
            data: {
                order: order,
                _token: token
            },
            success: function (response) {
                if (response.status == "success") {
                    console.log(response);
                } else {
                    console.log(response);
                }
            }
        });
    }
});

function deleteBlogImage(blog_image_id) {
    var data = {};
    data.blog_image_id = blog_image_id;
    $.ajax({
        type: 'GET',
        url: base_url + "/deleteBlogImage/" + blog_image_id,
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        success: function (response) {
            if (response.status) {
                myToastr(response.message, 'success');
                $('.delete_div_' + blog_image_id).remove();
            } else {
                myToastr(response.message, 'error');
            }
        }
    });
}

function add_social(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.name != '') {
        if(data.url !='') {
            if(data.icon !=''){
                $.ajax({
                    type: 'POST',
                    url: base_url + "/add-update-social",
                    headers: {},
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        if (response.success) {
                            myToastr(response.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 500);
                        } else {
                            myToastr(response.message, 'error');
                        }
                    }
                });
            }else{
                myToastr('Enter icon', 'error');
            }
        }else{
            myToastr('Enter url', 'error');
        }   
    } else {
        myToastr('Enter name', 'error');
    }
}

function add_subadmin(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.name == '') {
        myToastr('Enter name', 'error');
    }else if (data.email == '') {
        myToastr('Enter email', 'error');
    }else{
        $.ajax({
            type: 'POST',
            url: base_url + "/add-update-sub-admin",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.success) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}

var subAdminThumbImage;

function uploadSubadminThumbImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    if (id == 0) {
        var authorimage = 'image';
    } else {
        var authorimage = 'image_' + id;
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadSubAdminThumbImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                subAdminThumbImage = data.data;
                                if (subAdminThumbImage != undefined) {
                                    $('#' + authorimage).val(subAdminThumbImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


function loadHtml(id, dataObj) {
    var html = "";
    var html = dataObj.html;
    $("#" + id).html(html);
}




var blogThumbImage;

function uploadLogo(input, previewid, type, id) {
    var createBtn = 'createBtn';
    if (id == 0) {
        var authorimage = 'thumb_image';
    } else {
        var authorimage = 'thumb_image_' + id;
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/upload-logo',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                blogThumbImage = data.data;
                                if (blogThumbImage != undefined) {
                                    $('#' + authorimage).val(blogThumbImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


function add_livenews(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.company_name != '') {
        if (blogThumbImage != undefined || data.id) {
            $.ajax({
                type: 'POST',
                url: base_url + "/add-update-live-news",
                headers: {},
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.success) {
                        myToastr(response.message, 'success');
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        myToastr(response.message, 'error');
                    }
                }
            });
        } else {
            myToastr('Please select image', 'error');
        }
        
    } else {
        myToastr('Enter category', 'error');
    }
}


var blogThumbImage;

function uploadEpaperLogo(input, previewid, type, id) {
    var createBtn = 'createBtn';
    if (id == 0) {
        var authorimage = 'thumb_image';
    } else {
        var authorimage = 'thumb_image_' + id;
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/upload-logo-e-paper',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                blogThumbImage = data.data;
                                if (blogThumbImage != undefined) {
                                    $('#' + authorimage).val(blogThumbImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}



var pdf;

function uploadPdf(input, previewid, type, id) {
    var createBtn = 'createBtn';
    if (id == 0) {
        var authorimage = 'upload_file';
    } else {
        var authorimage = 'upload_file_' + id;
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        console.log(input.files[0]);
        console.log(extn);
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var fd = new FormData();
                    fd.append('upload_file', input.files[0]);
                    console.log(fd);
                    $.ajax({
                        url: base_url + '/uploadPdf',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                pdf = data.data;
                                console.log(data);
                                 $('#' + previewid).text(pdf);
                                if (pdf != undefined) {
                                    $('#' + authorimage).val(pdf);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
        } else {
            myToastr('Please select only PDF', 'error');
        }
    }
}


function add_epaper(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.paper_name != '') {
        if (blogThumbImage != undefined || data.id) {
            $.ajax({
                type: 'POST',
                url: base_url + "/add-update-e-paper",
                headers: {},
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data),
                success: function (response) {
                    if (response.success) {
                        myToastr(response.message, 'success');
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        myToastr(response.message, 'error');
                    }
                }
            });
        } else {
            myToastr('Please select image', 'error');
        }
        
    } else {
        myToastr('Enter paper name', 'error');
    }
}




function getTranslationValues(id) {
    var data = {};
    data.id = id;

    $('#append').html('');

    $.ajax({
        type: 'POST',
        url: base_url + "/languages/translations/show",
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status) {
                if (response.data.data.length > 0) {
                    $('#content-key').text('update key :'+ response.data.keyword);
                    for(var c =0; c<response.data.data.length; c++){
                        $('#append').append(`
                            <input type="hidden" name="id[]" value=`+response.data.data[c].id+`
                            <div class="p-5 grid grid-cols-12 mt-5 gap-4 row-gap-3">
                                <div class="col-span-12 sm:col-span-12">
                                    <label>`+response.data.data[c].language_name+`</label>
                                    <input type="text" class="input w-full border mt-2 flex-1 focus" name="value[]" placeholder="value" value="`+response.data.data[c].value+`">
                                </div>
                            </div>`
                            );
                    }
                    setTimeout(()=>{
                        $('.focus').focus();
                    },500);
                }
            } else {
                myToastr(response.message, 'error');
            }
        }
    });
}

function getSources(id,source) {
    var data = {};
    data.category_id = id;
    $('#source').html('<option value="">All Source</option>');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: base_url + "/getFeeds",
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status==true) {
                // var firstNew = '<option value="">All Source</option>';
                for(var c =0; c<response.data.length; c++){
                    if(source!=0){
                        var first = '<option value='+response.data[c].id+'';
                        if(source == response.data[c].id){
                            var second = " selected";
                        }else{
                            var second = "";
                        }
                        var third = '>'+response.data[c].rss_name+'</option>';
                        var final = first+''+second+''+third;
                        // $('#source').append(`` if(`+source+` == `+response.data[c].id+`)selected >`+response.data[c].rss_name+`</option>`);
                        $('#source').append(final);
                    }else{
                        $('#source').append(`<option value=`+response.data[c].id+`>`+response.data[c].rss_name+`</option>`);  
                    }
                }              
            } else {
                myToastr(response.message, 'error');
            }
        }
    });
}


function getCategoryTranslation(category_id,language_code){
    $('#category_name_'+category_id).val('');
    var data = {};
    data.category_id = category_id;
    data.language_code = language_code;
    $.ajax({
        type:'POST',
        url: base_url +'/get-category-translation',
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),

        success: function (response) {
            if (response.status) {
                if (response.data != null) {
                    $('#category_name_'+category_id).val(response.data.name);
                }
            }
        }
    })
}

function translateCategory(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.category_id == '') {
        myToastr('something went wrong try to rfresh page !', 'error');
    }else if(data.language_code == ''){
        myToastr('select language', 'error');
    }else if(data.name == ''){
        myToastr('enter name', 'error');
    }else{
        $.ajax({
            type: 'POST',
            url: base_url + "/translate-category",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }  
}



function getLiveNewsTranslation(live_news_id,language_code){
    $('#company_name_'+live_news_id).val('');
    $('#youtube_url_'+live_news_id).val('');
    var data = {};
    data.live_news_id = live_news_id;
    data.language_code = language_code;
    $.ajax({
        type:'POST',
        url: base_url +'/get-live-news-translation',
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status) {
                if (response.data != null) {
                    $('#company_name_'+live_news_id).val(response.data.company_name);
                    $('#youtube_url_'+live_news_id).val(response.data.url);
                }
            }
        }
    })
}


function translateLiveNews(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.live_news_id == '') {
        myToastr('something went wrong try to rfresh page !', 'error');
    }else if(data.language_code == ''){
        myToastr('select language', 'error');
    }else if(data.company_name == ''){
        myToastr('enter company name', 'error');
    }else if(data.url == ''){
        myToastr('enter youtube url', 'error');
    }else{
        $.ajax({
            type: 'POST',
            url: base_url + "/translate-live-news",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }  
}



function getEpaperTranslation(e_paper_id,language_code){
    $('#paper_name_'+e_paper_id).val('');
    $('#upload_file_'+e_paper_id+'_translate').val('');
    $('#translate_pdf_name_'+e_paper_id).html('No file selected');

    var data = {};
    data.e_paper_id = e_paper_id;
    data.language_code = language_code;
    $.ajax({
        type:'POST',
        url: base_url +'/get-e-paper-translation',
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status) {
                if (response.data != null) {
                    $('#paper_name_'+e_paper_id).val(response.data.paper_name);
                    if (response.data.pdf_exist) {
                        $('#upload_file_'+e_paper_id+'_translate').val(response.data.pdf);
                        $tag = `<a href="`+response.data.pdf_file+`" target="_blank">view</a>`
                        $('#translate_pdf_name_'+e_paper_id).html($tag);
                    }
                }
            }
        }
    })
}

function translateEpaper(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.e_paper_id == '') {
        myToastr('something went wrong try to rfresh page !', 'error');
    }else if(data.language_code == ''){
        myToastr('select language', 'error');
    }else if(data.paper_name == ''){
        myToastr('enter paper name', 'error');
    }else{
        $.ajax({
            type: 'POST',
            url: base_url + "/translate-e-paper",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }  
}



function getCmsTranslation(cms_id,language_code){
    $('#title').val('');
    CKEDITOR.instances['description'].setData('');
    var data = {};
    data.cms_id = cms_id;
    data.language_code = language_code;
    $.ajax({
        type:'POST',
        url: base_url +'/get-cms-page-translation',
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status) {
                if (response.data != null) {
                    $('#title').val(response.data.title);
                    CKEDITOR.instances['description'].setData(response.data.description);
                }
            }
        }
    })
}


function translateCmsPage(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    var flag = true;
    e.preventDefault();
    if (data.title == '') {
        flag = false;
        myToastr('Enter title', 'error');
    }
    // var desc = CKEDITOR.instances['description'].getData();
    // data.description = desc;
    if (flag) {
        $.ajax({
            type: 'POST',
            url: base_url + "/translate-cms-page",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}


function getBlogTranslation(blog_id,language_code){
    $('#title').val('');
    $('#blogdescription').val('');
    $('#seo_title').val('');
    $('#seo_keyword').val('');
    $('#seo_description').val('');
    CKEDITOR.instances['blogdescription'].setData('');
    $('#tags').tagsinput('removeAll');
    $('#seo_tag').tagsinput('removeAll');

    var data = {};
    data.blog_id = blog_id;
    data.language_code = language_code;
    $.ajax({
        type:'POST',
        url: base_url +'/get-blog-translation',
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),

        success: function (response) {
            if (response.status) {
                if (response.data != null) {
                    $('#title').val(response.data.title);
                    CKEDITOR.instances['blogdescription'].setData(response.data.description);
                    $('#seo_title').val(response.data.seo_title);
                    $('#seo_keyword').val(response.data.seo_keyword);
                    $('#seo_description').val(response.data.seo_description);
                    $('#tags').tagsinput('add', response.data.tags);
                    $('#seo_tag').tagsinput('add', response.data.seo_tag);
                }
            }
        }
    })
}


function translateBlog(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    var flag = true;
    e.preventDefault();
    var desc = CKEDITOR.instances['blogdescription'].getData();
    data.description = desc;
    if (data.title == '') {
        flag = false;
        myToastr('Enter title', 'error');
    }else if (data.description == '') {
        flag = false;
        myToastr(adminTranslation.admin_description_error, 'error');
    }

    if (flag) {
        $.ajax({
            type: 'POST',
            url: base_url + "/translate-blog",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}



function convertToSlug(Text)
{
    var slug = Text
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'')
        ;
    $('#slug').val(slug);
}


function getQuoteTranslation(quote_id,language_code){
    $('#quote_'+quote_id).val('');
    var data = {};
    data.quote_id = quote_id;
    data.language_code = language_code;
    $.ajax({
        type:'POST',
        url: base_url +'/get-quote-translation',
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status) {
                if (response.data != null) {
                    $('#quote_'+quote_id).val(response.data.quote);
                   
                }
            }
        }
    })
}

function deleteCategory(e,category_id,type) {
    var data = {};
	data.id = category_id;	
    var flag = true;
    e.preventDefault();
	if(type=="yes"){
        if ($("#category_id_"+category_id).val() == '') {
            flag = false;
            myToastr('Select Category', 'error');
        }
    }
    if (flag) {
		if(type=="yes"){
            data.category_id = $("#category_id_"+category_id).val();
        }
        $.ajax({
            type: 'POST',
            url: base_url + "/delete-category",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
					window.location.reload();
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}

function selectPermission(id){
	console.log('select');
	setTimeout(function () {
		if(document.getElementById(id).checked){
		alert('CHecked');
        // Put your code here if checkbox is checked
    }else{
		alert('unchecked');
	}
	}, 500);
    
	
}

function uploadMultipleAdsVideos(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'banner_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    var form_data = new FormData();
    // Read selected files
    var videoUrl = $(".videos").map(function(){return $(this).val();}).get();
    var videoName = $(".videos_name").map(function(){return $(this).val();}).get();
    console.log(videoUrl);
    var totalfiles = document.getElementById('videos').files.length;
    for (var index = 0; index < totalfiles; index++) {
        form_data.append("videos[]", document.getElementById('videos').files[index]);
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: base_url + '/api/uploads/ads_videos',
        data: form_data,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            var multiplevideos_url = "";
            var multiplevideos_name = "";
            console.log(response);
            multiplevideos_url = response.data.videos_url;
            multiplevideos_name = response.data.videos;
            if(videoUrl.length>0){
                for (var index = 0; index < multiplevideos_url.length; index++) {
                    if ($.inArray(multiplevideos_url[index], videoUrl) == -1) {
                        videoUrl.push(multiplevideos_url[index]);
                        videoName.push(multiplevideos_name[index]);

                    }
                    
                }
            }else{
                console.log("Check");
                for (var index = 0; index < multiplevideos_url.length; index++) {
                    if ($.inArray(multiplevideos_url[index], videoUrl) == -1) {
                        videoUrl.push(multiplevideos_url[index]);
                        videoName.push(multiplevideos_name[index]);

                    }
                }  
            }
            $('#showInputs').html('');
            $('#display_videos').html('');
            $('#showInputsVideos').html('');
            for (var index_url = 0; index_url < videoUrl.length; index_url++) {
                $('#showInputs').append('<input type="hidden" class="videos" name="videos_url" value="'+videoUrl[index_url]+'">');
                $('#showInputsVideos').append('<input type="hidden" class="videos_name" name="videos_name[]" value="'+videoName[index_url]+'">');
                $('#display_videos').append('<div class="col-span-12 xl:col-span-3"><div class="border border-gray-200 dark:border-dark-5 rounded-md p-5"><div class="w-40 h-40 relative image-fit cursor-pointer zoom-in mx-auto"><video controls="false" style="width: 100%;height: 100%;"><source src="'+videoUrl[index_url]+'" type="video/mp4" controls="false"><source src="'+videoUrl[index_url]+'" type="video/webm">Your browser does not support the video tag.</video></div><div class="w-40 mx-auto cursor-pointer relative mt-5"><button type="button" class="button w-full bg-theme-1 text-white" onclick="removeVideo('+index_url+')">Change Photo</button></div></div> </div>');
            }
        }
    })
}

function removeVideo(index){
    var videoUrl = $(".videos").map(function(){return $(this).val();}).get();
    console.log(videoUrl);
    var videoName = $(".videos_name").map(function(){return $(this).val();}).get();
    // Remove the element at index 2 using the .splice() method
    videoUrl.splice(index, 1);
    videoName.splice(index, 1);
    
    // The array now contains four elements: "apple", "banana", "grape", and "kiwi"
    console.log(videoUrl); // Output: ["apple", "banana", "grape", "kiwi"]
    console.log(videoName); // Output: ["apple", "banana", "grape", "kiwi"]
    
    // Re-arrange the indexes of the array to start from 0
    videoUrl = $.grep(videoUrl, function(n) { return (n); });
    videoName = $.grep(videoName, function(n) { return (n); });

    
    // The array now contains four elements with indexes starting from 0: "apple", "banana", "grape", and "kiwi"
    console.log(videoUrl);
    $('#showInputs').html('');
    $('#showInputsVideos').html('');

    $('#display_videos').html('');
    for (var index_url = 0; index_url < videoUrl.length; index_url++) {
        $('#showInputs').append('<input type="hidden" class="videos" name="videos_url" value="'+videoUrl[index_url]+'">');
        $('#showInputsVideos').append('<input type="hidden" class="videos_name" name="videos_name[]" value="'+videoName[index_url]+'">');
        $('#display_videos').append('<div class="col-span-12 xl:col-span-3"><div class="border border-gray-200 dark:border-dark-5 rounded-md p-5"><div class="w-40 h-40 relative image-fit cursor-pointer zoom-in mx-auto"><video controls="false" style="width: 100%;height: 100%;"><source src="'+videoUrl[index_url]+'" type="video/mp4" controls="false"><source src="'+videoUrl[index_url]+'" type="video/webm">Your browser does not support the video tag.</video></div><div class="w-40 mx-auto cursor-pointer relative mt-5"><button type="button" class="button w-full bg-theme-1 text-white" onclick="removeImage('+index_url+')">Change Photo</button></div></div> </div>');
    }
}

function uploadMultipleAdsImages(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'banner_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    var form_data = new FormData();
    // Read selected files
    var imagesUrl = $(".images").map(function(){return $(this).val();}).get();
    var imagesName = $(".images_name").map(function(){return $(this).val();}).get();
    console.log(imagesUrl);
    var totalfiles = document.getElementById('images').files.length;
    for (var index = 0; index < totalfiles; index++) {
        form_data.append("images[]", document.getElementById('images').files[index]);
    }
    $.ajax({
        url: base_url + '/api/uploads/ads_images',
        data: form_data,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            var multipleimages_url = "";
            var multipleimages_name = "";
            console.log(response);
            multipleimages_url = response.data.images_url;
            multipleimages_name = response.data.images;
            if(imagesUrl.length>0){
                for (var index = 0; index < multipleimages_url.length; index++) {
                    if ($.inArray(multipleimages_url[index], imagesUrl) == -1) {
                        imagesUrl.push(multipleimages_url[index]);
                        imagesName.push(multipleimages_name[index]);
                    }
                    
                }
            }else{
                console.log("Check");
                for (var index = 0; index < multipleimages_url.length; index++) {
                    if ($.inArray(multipleimages_url[index], imagesUrl) == -1) {
                        imagesUrl.push(multipleimages_url[index]);
                        imagesName.push(multipleimages_name[index]);
                    }
                }  
            }
            $('#showInputsImages').html('');
            $('#display_images').html('');
            $('#showInputsImagesName').html('');
            for (var index_url = 0; index_url < imagesUrl.length; index_url++) {
                $('#showInputsImages').append('<input type="hidden" class="images" name="images_url" value="'+imagesUrl[index_url]+'">');
                $('#showInputsImagesName').append('<input type="hidden" class="images_name" name="images_name[]" value="'+imagesName[index_url]+'">');
                $('#display_images').append('<div class="col-span-12 xl:col-span-3"><div class="border border-gray-200 dark:border-dark-5 rounded-md p-5"><div class="w-40 h-40 relative image-fit cursor-pointer zoom-in mx-auto"><img class="rounded-md" alt="Midone Tailwind HTML Admin Template" src="'+imagesUrl[index_url]+'"></div><div class="w-40 mx-auto cursor-pointer relative mt-5"><button type="button" class="button w-full bg-theme-1 text-white" onclick="removeImage('+index_url+')">Delete Image</button></div></div> </div>');
            }
        }
    })
}

function removeImage(index){
    var imagesUrl = $(".images").map(function(){return $(this).val();}).get();
    var imagesName = $(".images_name").map(function(){return $(this).val();}).get();
    // Remove the element at index 2 using the .splice() method
    imagesUrl.splice(index, 1);
    imagesName.splice(index, 1);
    
    // The array now contains four elements: "apple", "banana", "grape", and "kiwi"
    console.log(imagesUrl); // Output: ["apple", "banana", "grape", "kiwi"]
    console.log(imagesName); // Output: ["apple", "banana", "grape", "kiwi"]
    
    // Re-arrange the indexes of the array to start from 0
    imagesUrl = $.grep(imagesUrl, function(n) { return (n); });
    imagesName = $.grep(imagesName, function(n) { return (n); });

    
    // The array now contains four elements with indexes starting from 0: "apple", "banana", "grape", and "kiwi"
    $('#showInputsImages').html('');
    $('#display_images').html('');
    $('#showInputsImagesName').html('');
    for (var index_url = 0; index_url < imagesUrl.length; index_url++) {
        $('#showInputsImages').append('<input type="hidden" class="images" name="images_url" value="'+imagesUrl[index_url]+'">');
        $('#showInputsImagesName').append('<input type="hidden" class="images_name" name="images_name[]" value="'+imagesName[index_url]+'">');
        $('#display_images').append('<div class="col-span-12 xl:col-span-3"><div class="border border-gray-200 dark:border-dark-5 rounded-md p-5"><div class="w-40 h-40 relative image-fit cursor-pointer zoom-in mx-auto"><img class="rounded-md" alt="Midone Tailwind HTML Admin Template" src="'+imagesUrl[index_url]+'"></div><div class="w-40 mx-auto cursor-pointer relative mt-5"><button type="button" class="button w-full bg-theme-1 text-white" onclick="removeImage('+index_url+')">Delete Image</button></div></div> </div>');
    }
}

function validateAdForm(formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    var totalFiles = 0;
    var videoUrl = $(".videos").map(function(){return $(this).val();}).get();
    var youtubeVideoUrl = $(".video_url").map(function(){return $(this).val();}).get();
    var imagesUrl = $(".images").map(function(){return $(this).val();}).get();
    if(videoUrl.length>0){
        if(videoUrl[0]!=''){
            totalFiles = totalFiles + 1;
        }
    }
    if(imagesUrl.length>0){
        if(imagesUrl[0]!=''){
            totalFiles = totalFiles + 1;
        }
    }
    if(youtubeVideoUrl.length>0){
        if(youtubeVideoUrl[0]!=''){
            totalFiles = totalFiles + 1;
        }
    }
    if (data.title == '') {
        myToastr('Enter title', 'error');
        return false;
    }else if(data.start_date == '') {
        myToastr('Select start date', 'error');
        return false;
    }else if(data.end_date == '') {
        myToastr('Select end date', 'error'); 
        return false;
    }else if(data.end_date < data.start_date) {
        myToastr('End date should be greater than start date', 'error');
        return false;
    }else if(data.frequency == '') {
        myToastr('Enter frequency', 'error');
        return false;
    }else if(totalFiles==0) {
        myToastr('At least one media is mandatory either you have to select 1 image, video or video url.', 'error');
        return false;
    }else{
        return true;
    } 
    
}

function deleteAdImageVideo(id) {
    var data = {};
	data.id = id;	
    $.ajax({
        type: 'POST',
        url: base_url + "/delete-ad-image-video",
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            if (response.status) {
                myToastr(response.message, 'success');
                window.location.reload();
            } else {
                myToastr(response.message, 'error');
            }
        }
    });
}

function add_edit_redirected_url(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.redirected_url == '') {
        myToastr('Enter redirected url', 'error');
    }else{
        $.ajax({
            type: 'POST',
            url: base_url + "/addUpdateRedirectedUrl",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}

function googleAutoComplete() {
    // var input = (document.getElementById('address'));
    // var autocomplete = new google.maps.places.Autocomplete(input);
    // google.maps.event.addListener(autocomplete, 'place_changed', function() {
    //     var place = autocomplete.getPlace();
    //     console.log(place);
    //     if (!place.geometry) {
    //         return;
    //     }
    //     var pos = {
    //       lat: place.geometry.location.lat(),
    //       lng: place.geometry.location.lng()
    //     };
    //     if (place) {
    //       $('#latitude').val(pos.lat);
    //       $('#longitude').val(pos.lng);
    //       console.log(place.formatted_address);
    //       $('#address_value').val(place.formatted_address);
    //     //   for (var i=0; i<place.address_components.length; i++){
    //     //     for (var b=0;b<place.address_components[i].types.length;b++){
    //     //       if (place.address_components[i].types[b] == "sublocality_level_1" || place.address_components[i].types[b] == "sublocality"){
    //     //         $('#address').val(place.formatted_address);
    //     //       }
    //     //     }
    //     //   }
    //     }
    // });

    var input = (document.getElementById('address'));
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
    var place = autocomplete.getPlace();
    console.log(place);
    if (!place.geometry) {
        return;
    }
    var pos = {
        lat: place.geometry.location.lat(),
        lng: place.geometry.location.lng()
    };
    if (place) {
        $('#latitude').val(pos.lat);
        $('#longitude').val(pos.lng);
        console.log(place.formatted_address);
        $('#address_value').val(place.formatted_address);

        // Get the city
        var city = null;
        for (var i = 0; i < place.address_components.length; i++) {
            var component = place.address_components[i];
            if (component.types.includes('locality')) {
                city = component.long_name;
                break;
            }
        }
        if (city) {
            console.log('City: ' + city);
        }

        // Get the country
        var country = null;
        for (var i = 0; i < place.address_components.length; i++) {
            var component = place.address_components[i];
            if (component.types.includes('country')) {
                country = component.long_name;
                break;
            }
        }
        if (country) {
            console.log('Country: ' + country);
        }
        $('#city').val(city);
        $('#country').val(country);
    }
});
}

var finalPostArr = [];
function checkSelectedPosts(){
    var array = []; 
    $("input:checkbox[name='postCheck[]']:checked").each(function() { 
        array.push($(this).val()); 
    });
    finalPostArr = array;
    $("#countdisplayorder").html(array.length+" Selected");
}

function checkallPosts($this){
    console.log($this);
    if($this.checked) {
        $('.postcheck:checkbox').each(function() {
            $this.checked = true;
            checkSelectedPosts();
        });
    }else {
        $('.postcheck:checkbox').each(function() {
            $this.checked = false;
        });
    }
}

function bulkDeletePost(){
    var data = {};
    console.log(finalPostArr);
    if(finalPostArr.length==0){
        myToastr("Please select any post first then proceed.",'error');
    }else{
        data.post_id = finalPostArr;
        $.ajax({
            type: 'post',
            url: base_url +"/bulkPostDelete",
            data: data,
            success: function (response) {
                if(!response.status){                
                    myToastr(response.message,'error');
                }else{
                    location.reload();
                    myToastr(response.message,'success');
                }
            }
        });
    }    
}

function selectChildCard(value,blog_id){
    location.href = base_url +"/blog/add-child-card/side-menu/light/"+blog_id+"?child_card="+value;
}

function addRemoveDivs(type){
    if(type=='add'){
        optionCount = parseInt(optionCount) + 1;
        // fieldCounter++;
        var newField = $('<div class="grid grid-cols-12 gap-6 mt-5"><div class="intro-y col-span-12 lg:col-span-12 bg_"><div class="intro-y box p-5"><div class="mt-3"><label>Title <span class="required">*</span></label><input type="text" class="input w-full border mt-2" name="title" placeholder="Title"></div><div class="mt-3"><label>Description</label><div class="mt-2"><div class="preview"><textarea name="description" id="blogdescription"></textarea></div></div></div><div class="mt-3"><button type="button" onclick="addRemoveOptions(`remove`);" class="button w-14 bg-theme-6 text-white"><i class="ti ti-plus me-1"></i><span class="align-middle">Delete</span></button></div></div></div></div>');
        $(".showMoreDivs").append(newField);
    }else{
        optionCount = parseInt(optionCount) - 1;
        // $(".showMoreOptions .row:last").remove();
        $(".showMoreDivs .row:last, .showMoreDivs .option-divider:last").remove();
    }    
}

var BannerImage;

function uploadCmsBannerImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    var authorimage = 'banner_image';
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');

    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadCMSBannerImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                BannerImage = data.data;
                                if (BannerImage != undefined) {
                                    $('#' + authorimage).val(BannerImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}


function addUpdateCmsPage(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    var flag = true;
    e.preventDefault();
    if (data.title == '') {
        flag = false;
        myToastr('Enter title', 'error');
    }
    var description = $(".ck-content").html();
    console.log(description);
    data.description = description;
    if (flag) {
        $.ajax({
            type: 'POST',
            url: base_url + "/addUpdateCMSPage",
            headers: {},
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
                if (response.status) {
                    myToastr(response.message, 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                } else {
                    myToastr(response.message, 'error');
                }
            }
        });
    }
}

function showImagePreview(input_id,preview_id) {
    var fileInput = document.getElementById(input_id);
    var handleImageChange = function() {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function(event) {
            var img = new Image();
            img.onload = function() {
                $("#" + preview_id).removeClass('hide');
                $('#' + preview_id).attr('src', event.target.result);
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    };
    setTimeout(function () {
        fileInput.addEventListener('change', handleImageChange);
    }, 2000);
}

function add_source(e, formid) {
    var $form = $("#" + formid);
    var data = getFormData($form);
    e.preventDefault();
    if (data.name != '') {
        if (data.color != '') {
            if (sourceImage != undefined || data.id) {
                $.ajax({
                    type: 'POST',
                    url: base_url + "/add-update-source",
                    headers: {},
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(data),
                    success: function (response) {
                        if (response.success) {
                            myToastr(response.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 500);
                        } else {
                            myToastr(response.message, 'error');
                        }
                    }
                });
            } else {
                myToastr('Please Upload Source Image', 'error');
            }
        } else {
            myToastr('Select source color', 'error');
        }
    } else {
        myToastr('Enter source', 'error');
    }
}

var sourceImage;

function uploadSourceImage(input, previewid, type, id) {
    var createBtn = 'createBtn';
    if (id == 0) {
        var authorimage = 'thumb_image';
    } else {
        var authorimage = 'thumb_image_' + id;
    }
    $('#' + createBtn).prop('disabled', true);
    $('#' + createBtn).html('<i class="fa fa-spinner fa-spin"></i> Loading');
    if (input.files && input.files[0]) {
        var imgPath = input.files[0].name;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.readAsDataURL(input.files[0]);
                reader.onload = function (e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('#' + previewid).attr('src', e.target.result);
                    var fd = new FormData();
                    fd.append('image', input.files[0]);
                    $.ajax({
                        url: base_url + '/uploadSourceImage',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            setTimeout(function () {
                                sourceImage = data.data;
                                if (sourceImage != undefined) {
                                    $('#' + authorimage).val(sourceImage);
                                    $('#' + createBtn).prop('disabled', false);
                                    if (id) {
                                        $('#' + createBtn).html('Update');
                                    } else {
                                        $('#' + createBtn).html('Create');
                                    }
                                }
                            }, 10);
                        }
                    })
                };
            } else {
                myToastr('Something went wrong', 'error');
            }
        } else {
            myToastr('Please select only image', 'error');
        }
    }
}

function saveOrder(value, blog_id) {
    var data = {};   
    data.order = value;
    data.blog_id = blog_id;
    showLoader();
    $.ajax({
        type: 'POST',
        url: base_url + "/updateOrderSequenceofBlog",
        headers: {},
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(data),
        success: function (response) {
            hideLoader();
            if (response.status) {
                myToastr(response.message, 'success');
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                myToastr(response.message, 'error');
            }
        }
    });
}

$(document).ready(function() {
    const inputContainer = $("#input-container");
    const addButton = $("#add-input");
    let inputCount = 1;
  
    addButton.click(function() {
      inputCount++;
      const newInputRow = $("<div>", { class: "input-row" });
      const newInput = $("<input>", { type: "text", class: "input w-half border mt-2 video_url", placeholder: "Video url", name: "video_url[]" });
      const deleteButton = $("<a>", { class: "button bg-theme-6 text-white delete-input", text: "Delete", style: "margin-left: 14px;" });
      newInputRow.append(newInput, deleteButton);
      inputContainer.append(newInputRow);
    });
  
    inputContainer.on("click", ".delete-input", function() {
      $(this).parent().remove();
    });
  });