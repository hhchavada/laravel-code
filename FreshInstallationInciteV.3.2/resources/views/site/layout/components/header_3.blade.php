<div class="jl_topa_blank_nav jl_blank_06"></div>
    <header class="header-wraper header_magazine_full_screen jl_headcus_06 header_magazine_full_screen jl_topa_menu_sticky options_dark_header jl_cus_sihead">
        <div id="menu_wrapper" class="menu_wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!-- begin logo -->
                        <div class="logo_small_wrapper_table">
                            <div class="logo_small_wrapper">
                                <a class="logo_link" href="{{url('/')}}">
                            @php
                            $result = App\Models\Setting::get();
                            @endphp

                            @php $cmsList = \Helpers::getCmsForSite(); @endphp

                            @foreach($result as $row)
                            @if($row->key == 'website_admin_logo')

                            <img src="{{url('uploads/setting/'.$row->value)}}" alt="Logo" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-logo-image.png') }}`"/>

                            @endif
                            @endforeach
                                </a>
                            </div>
                        </div>
                        <!-- end logo -->
                        <!-- main menu -->
                        <div class="menu-primary-container navigation_wrapper header_layout_style1_custom">
                            @include('site/layout/components/header_menu') 
                            <div class="clearfix"></div>
                        </div>
                        <!-- end main menu -->
<!--                         <div class="search_header_menu">
                            <div class="menu_mobile_icons"><i class="fa fa-bars"></i></div>
                            <div class="search_header_wrapper search_form_menu_personal_click"><i class="fa fa-search"></i></div>
                            <ul class="social_icon_header_top jl_socialcolor">
                                
                            </ul>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </header>
    @include('site/layout/components/side-menu') 
    <div class="search_form_menu_personal">
        <div class="menu_mobile_large_close">
            <span class="jl_close_wapper search_form_menu_personal_click"><span class="jl_close_1"></span><span class="jl_close_2"></span></span>
        </div>
        <form method="get" class="searchform_theme" action="{{url('search-blog')}}">
            <input type="text" placeholder="Search..." name="keyword" class="search_btn" />
            <button type="submit" class="button"><i class="fa fa-search fa-2x"></i></button>
        </form>
    </div>
    <div class="mobile_menu_overlay"></div>