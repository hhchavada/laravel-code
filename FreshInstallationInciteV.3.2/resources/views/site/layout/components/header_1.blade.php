 <!-- Start header -->
 <header class="header-wraper jl_header_magazine_style two_header_top_style header_layout_style3_custom jl_cusdate_head">
    <div class="header_top_bar_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="menu-primary-container navigation_wrapper">
                        <ul id="jl_top_menu" class="jl_main_menu">
                            <li class="menu-item menu-item-3602">
                                <a href="phone:{{setting('site_phone')}}"><i class="fa fa-phone"></i>{{setting('site_phone')}}<span class="border-menu"></span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="jl_top_bar_right"><span class="jl_current_title">{{__('lang.website_current_date')}}:</span> {{date('d M, Y')}}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Main menu -->
    <div class="jl_blank_nav"></div>
    <div id="menu_wrapper" class="menu_wrapper jl_menu_sticky jl_stick">
        <div class="container">
            <div class="row">
                <div class="main_menu col-md-12">
                    <div class="logo_small_wrapper_table">
                        <div class="logo_small_wrapper">
                            <!-- begin logo -->
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
                            <!-- end logo -->
                        </div>
                    </div>
                    <!-- main menu -->
                    <div class="menu-primary-container navigation_wrapper">
                        @include('site/layout/components/header_menu') 
                    </div>
                    <!-- end main menu -->
<!--                     <div class="search_header_menu">
                        <div class="menu_mobile_icons"><i class="fa fa-bars"></i></div>
                        <div class="search_header_wrapper search_form_menu_personal_click"><i class="fa fa-search"></i></div>
                        <div class="menu_mobile_share_wrapper">
                            <ul class="social_icon_header_top">
                               
                            </ul>
                        </div>
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