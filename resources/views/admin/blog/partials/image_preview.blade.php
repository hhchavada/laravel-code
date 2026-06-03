<input hidden class="multiple-image-count" value="{{ isset($images) ? count($images) : 0 }}">

@if(isset($images) && count($images))
    @foreach($images as $index => $image)
        <div class="col row1" data-id="{{ $image->id }}">
            <div class="card h-100">
                <img class="card-img-top" src="{{ url('uploads/blog/768x428/'.$image->image)}}" alt="{{ $image->image }}" />

                <div class="card-body" style="text-align: center;padding: 0;">
                    @if(count($images) == 1)
                        <!-- Only one image, show replace button -->
                        <button class="btn btn-label-info mt-4 mb-4 waves-effect" type="button" onclick="replacePostImage('{{ $image->id }}');">
                            <i class="ti ti-refresh ti-xs me-1"></i>
                            <span class="align-middle">{{__('lang.admin_replace')}}</span>
                        </button>
                    @else
                        <!-- More than one image, show delete button -->
                        <button class="btn btn-label-danger mt-4 mb-4 waves-effect" type="button" onclick="deletePostImage('{{ $image->id }}', {{ count($images) }});">
                            <i class="ti ti-x ti-xs me-1"></i>
                            <span class="align-middle">{{__('lang.admin_delete')}}</span>
                        </button>
                        
                        @if($index === 0 && count($images) === 1)
                            <!-- First image after deleting other, show replace button -->
                            <button class="btn btn-label-info mt-4 mb-4 waves-effect" type="button" onclick="replacePostImage('{{ $image->id }}');">
                                <i class="ti ti-refresh ti-xs me-1"></i>
                                <span class="align-middle">{{__('lang.admin_replace')}}</span>
                            </button>
                        @endif
                    @endif
                </div> 
            </div>
        </div>
    @endforeach
@endif


<script>
function deletePostImage(id) {
    Swal.fire({
        title: adminTranslation.admin_are_you_sure,
        text: adminTranslation.admin_delete_warning,
        icon: adminTranslation.admin_warning,
        showCancelButton: true,
        confirmButtonText: adminTranslation.admin_delete_warning_yes_button,
        cancelButtonText: adminTranslation.admin_delete_warning_no_button,
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            var data = { image_id: id };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                dataType: "json",
                url: base_url + "/admin/remove-image",
                data: data,
                success: function (response) {
                    $("#previewsContainer").html(response.data.html);
                    
                    // Update buttons after image deletion
                    let imageCount = $(".row1").length;
                    
                    if (imageCount === 1) {
                        // If only one image left, change delete button to replace button
                        let remainingImageId = $(".row1").data('id');
                        $(`div[data-id='${remainingImageId}'] .card-body`).html(`
                            <button class="btn btn-label-info mt-4 mb-4 waves-effect" type="button" onclick="replacePostImage('${remainingImageId}');">
                                <i class="ti ti-refresh ti-xs me-1"></i>
                                <span class="align-middle">${adminTranslation.admin_replace}</span>
                            </button>
                        `);
                    }
                }
            });
        }
    });
}

function replacePostImage(id) {
    // Show the modal for image replacement
    $('#basicModal').modal('show');
    $('.imageButtonVal').val('replace');
}

</script>