var websiteTranslation = window.translations.messages;
console.log(websiteTranslation);

function myToastr(msg,type){
    toastr.remove();
    if(type == 'error'){
        toastr.error(msg);
    }else if(type == 'success'){
        toastr.success(msg);
    }
}

function selectImage(input_id,preview_id) {
    $('#'+input_id).trigger('click');
    $('#'+input_id).on('change', function() {
        var file = this.files[0];
        var reader = new FileReader();
        
        reader.onload = function(event) {
            var img = new Image();
            console.log(img);
            img.onload = function() {
                if (file.size < 1048576) { // Check if file size is less than or equal to 1 MB
                    $('#'+preview_id).attr('src', event.target.result);
                } else {
                    myToastr(websiteTranslation.admin_image_size_error, 'error');
                    $('#' + input_id).val('');
                    $('#'+preview_id).attr('src', '');
                }
            };
            
            img.src = event.target.result;
        };
        
        reader.readAsDataURL(file);
    });   
}

function deletebtnModal(id,flag = true) {
    if (flag) {
        $('#'+id).attr('style','display:block');
    }else{
        $('#'+id).removeAttr('style');
    }
}

function addRemoveBookMarks(blog_id,page) {
    var data = {};
    data.blog_id = blog_id;
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url: base_url + '/add-remove-bookmark',
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(response) {
        console.log(response);
        if (response.status) {
            if(page=='story'){
                window.location.reload();
            }else{
                if(response.data.type=='add'){
                    $(".notmarked").addClass('hide');
                    $(".marked").removeClass('hide');
                }else{
                    $(".marked").addClass('hide');
                    $(".notmarked").removeClass('hide');
                }
            }
        } else {
          myToastr(response.message,'error');
        }
      },
    });
  }