var websiteTranslation = window.translations.messages;


function myToastr(msg,type){
    toastr.remove();
    if(type == 'error'){
        toastr.error(msg);
    }else if(type == 'success'){
        toastr.success(msg);
    }
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

function isValidUrl(url) {
    var regex = /^(http|https):\/\/[^ "]+$/;
    return regex.test(url);
}

function validateLogin(formid) {
    var $form = $("#" + formid);
    var data = new FormData($form[0]);
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    console.log(data);
    if (data.get('email') == '') {
        myToastr(websiteTranslation.website_email_error, 'error');
        return false;
    } else if (!emailRegex.test(data.get('email'))) {
        myToastr(websiteTranslation.website_valid_email_error, 'error');
        return false;
    } else if (data.get('password') == '') {
        myToastr(websiteTranslation.website_password_error, 'error');
        return false;
    } else {
        return true;
    }
}

function validateSignup(formid){
    var $form = $("#" + formid);
    // var data = new FormData($form[0]);
    var data = getFormData($form);
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    console.log(data);
    if (data.get('name') == '') {
        myToastr(websiteTranslation.website_email_error, 'error');
        return false;
    }else if (data.get('email') == '') {
        myToastr(websiteTranslation.website_email_error, 'error');
        return false;
    } else if (!emailRegex.test(data.get('email'))) {
        myToastr(websiteTranslation.website_valid_email_error, 'error');
        return false;
    } else if (data.get('password') == '') {
        myToastr(websiteTranslation.website_password_error, 'error');
        return false;
    } else {
        return true;
    }
}
// function validateResetPassword(event, formId) {
//     event.preventDefault();
//     var $form = $("#" + formId);
//     console.log($form);
//     // Custom validation logic and actions here
  
//     // Return false if validation fails to prevent form submission
//     return false;
//   }

function validateResetPassword(event,formid) {
    var $form = $("#" + formid);
    var data = new FormData($form[0]);
    if (data.get('name') == '') {
        myToastr(websiteTranslation.website_email_error, 'error');
        return false;
    }else if (data.get('email') == '') {
        myToastr(websiteTranslation.website_email_error, 'error');
        return false;
    } else if (!emailRegex.test(data.get('email'))) {
        myToastr(websiteTranslation.website_valid_email_error, 'error');
        return false;
    } else if (data.get('password') == '') {
        myToastr(websiteTranslation.website_password_error, 'error');
        return false;
    } else {
        return true;
    }
}

