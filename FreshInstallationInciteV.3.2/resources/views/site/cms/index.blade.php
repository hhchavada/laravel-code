@extends('site/layout/site-app')
@section('content')

<div class="jl_single_style5">
  <div class="single_content_header single_captions_bottom_image_full_width">
    <div class="image-post-thumb" style="background-image: url({{url('uploads/cms/'.$row->image)}})"></div>
  </div>
  <div class="single_captions_bottom_image_full_width_wrapper">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="single_post_entry_content">
            <h1 style="margin-top: -260px;font-size: 50px;" class="single_post_title_main"> {{$row->title}} </h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<section id="content_main" class="clearfix jl_spost">
  <div class="container">
    <div class="row main_content">
      <div class="col-md-8  loop-large-post" id="content">
        <div class="widget_container content_page">
          <!-- start post -->
          <div class="post-2804 post type-post status-publish format-standard has-post-thumbnail hentry category-active tag-food tag-game tag-inspiration" id="post-2804">
            <div class="single_section_content box blog_large_post_style">
              <div class="post_content">
                <p><?php echo $row->description;?></p>
              </div>
            </div>
          </div>
          <!-- end post -->
          <div class="brack_space"></div>
        </div>
      </div>
      <!-- start sidebar -->

      <!-- end sidebar -->
    </div>
  </div>
</section>


@endsection