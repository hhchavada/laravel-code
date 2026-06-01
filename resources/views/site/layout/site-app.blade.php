<!DOCTYPE html>
<html lang="en-US">
    <head>
        <!-- paqwan -->
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        @if(Request::segment(1)=='post')
            <title>{{$row->seo_title}}</title>
            @if($row->author_name!='')
                <meta name="author" content="{{$row->author_name}}">
            @else
                <meta name="author" content="{{setting('site_seo_title')}}">
            @endif
            <meta name="description" content="{{$row->seo_description}}">
            @if($row->seo_keyword!='')
                <meta name="keywords" content="{{$row->seo_keyword}}">
            @else
                <meta name="keywords" content="{{setting('site_seo_keyword')}}">
            @endif
        @elseif(isset($row) && isset($row->id))
            <title>{{$row->meta_char ?? setting('site_seo_title')}}</title>
            <meta name="author" content="{{$row->meta_char}}">
            <meta name="description" content="{{$row->meta_desc}}">
            <meta name="keywords" content="{{$row->meta_keywords}}">
        @else
            <title>{{setting('site_seo_title')}}</title>
            <meta name="author" content="{{setting('site_seo_title')}}">
            <meta name="description" content="{{setting('site_seo_description')}}">
            <meta name="keywords" content="{{setting('site_seo_keyword')}}">
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(setting('site_favicon')!='')
        <link rel="icon" type="image/x-icon" href="{{url('uploads/setting/'.setting('site_favicon'))}}" />
        @else
        <link rel="icon" type="image/x-icon" href="{{url('uploads/no-favicon.png')}}" />
        @endif
        <link rel="stylesheet" href="{{ asset('site-assets/css/bootstrap.css')}}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('site-assets/css/style.css')}}" type="text/css" media="all" />
        <link rel="stylesheet" href="{{ asset('site-assets/css/responsive.css')}}" type="text/css" media="all" />

        <style type="text/css">
        .header_magazine_full_screen .logo_link img {
        max-height: 40px!important;
        max-width: 130px!important;
        }
        </style>
        
        @if(isset($_GET['home']) && $_GET['home']!='')
            @if($_GET['home']=='home_2')
                <link rel="stylesheet" href="{{ asset('site-assets/css/demo4.css')}}" type="text/css" media="all" />
            @elseif($_GET['home']=='home_3')
                <link rel="stylesheet" href="{{ asset('site-assets/css/demo5.css')}}" type="text/css" media="all" />
            @elseif($_GET['home']=='home_4')
                <link rel="stylesheet" href="{{ asset('site-assets/css/demo6.css')}}" type="text/css" media="all" />
            @else
                <link rel="stylesheet" href="{{ asset('site-assets/css/main.css')}}" type="text/css" media="all" />
            @endif
        @else
            @if(setting('homepage_theme')=='home_2')
                <link rel="stylesheet" href="{{ asset('site-assets/css/demo4.css')}}" type="text/css" media="all" />
            @elseif(setting('homepage_theme')=='home_3')
                <link rel="stylesheet" href="{{ asset('site-assets/css/demo5.css')}}" type="text/css" media="all" />
            @elseif(setting('homepage_theme')=='home_4')
                <link rel="stylesheet" href="{{ asset('site-assets/css/demo6.css')}}" type="text/css" media="all" />
            @else
                <link rel="stylesheet" href="{{ asset('site-assets/css/main.css')}}" type="text/css" media="all" />
            @endif
        @endif
        <!-- <link rel="stylesheet" href="{{ asset('site-assets/css/toastr.css')}}" /> -->
        <link rel="stylesheet" href="{{ asset('site-assets/css/toastr.min.css')}}" rel="stylesheet" type="text/css">
        <script>
            var base_url = "{{url('')}}";
            window.translations = {
                locale: '{{ app()->getLocale() }}',
                messages: @json(__('lang'))
            };
        </script>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=@if(setting('google_analytics_code')){{setting('google_analytics_code')}}@endif"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', '@if(setting('google_analytics_code')){{setting('google_analytics_code')}}@endif');
        </script>
    </head>
    <body class="@if(Request::segment(1)=='login' || Request::segment(1)=='signup' || Request::segment(1)=='forget-password' || Request::segment(1)=='reset-password' || Request::segment(1)=='profile') woocommerce-account woocommerce-page @endif mobile_nav_class jl-has-sidebar">
        <div class="options_layout_wrapper @if(isset($_GET['home']) && $_GET['home']!='') @if($_GET['home']!='home_2')jl_radius @endif @endif jl_none_box_styles jl_border_radiuss">
            <div class="options_layout_container full_layout_enable_front">
                @if(isset($_GET['home']) && $_GET['home']!='')
                    @php $explode = explode("_",$_GET['home']); @endphp
                    @if($_GET['home']=='home_5' || setting('homepage_theme')=='home_5')
                        @include('site/layout/components/header_1') 
                    @else
                        @include('site/layout/components/header_'.$explode[1]) 
                    @endif
                @else
                    @if(setting('homepage_theme')=='home_5')
                        @include('site/layout/components/header_1') 
                    @else
                        @php $explode = explode("_",setting('homepage_theme')); @endphp
                        @include('site/layout/components/header_'.$explode[1])  
                    @endif                    
                @endif
                    @yield('content')
                @include('site/layout/components/footer')
            </div>
        </div>
        <div id="go-top">
            <a href="#go-top"><i class="fa fa-angle-up"></i></a>
        </div>
        <!-- <script src="{{ asset('site-assets/js/jquery.js')}}"></script> -->
        <script src="{{ asset('site-assets/js/jquery-v3.6.0.js') }}"></script>
        <script src="{{ asset('site-assets/js/fluidvids.js')}}"></script>
        <script src="{{ asset('site-assets/js/infinitescroll.js')}}"></script>
        <script src="{{ asset('site-assets/js/justified.js')}}"></script>
        <script src="{{ asset('site-assets/js/slick.js')}}"></script>
        <script src="{{ asset('site-assets/js/theia-sticky-sidebar.js')}}"></script>
        <script src="{{ asset('site-assets/js/aos.js')}}"></script>
        <script src="{{ asset('site-assets/js/custom.js')}}"></script>
        <script src="{{ asset('site-assets/js/toastr.min.js')}}"></script>
        <script src="{{ asset('site-assets/js/validation.js')}}"></script>
        <script src="{{ asset('site-assets/js/script.js')}}"></script>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v11.0&appId=YOUR_APP_ID&autoLogAppEvents=1" nonce="YOUR_NONCE"></script>
        <script>
        
        <?php if(Session::has('success')){ ?>
            toastr.success("<?php echo Session::get('success'); ?>");
        <?php }else if(Session::has('error')){  ?>
            toastr.error("<?php echo Session::get('error'); ?>");
        <?php } ?>
        $(document).ready(function() {
            // Remove the class "my-class" from the <ul> element
            $("ul").removeClass("pagination");
        });
        </script>
        @if(Request::segment(1)=='blog')
        <script>
            document.querySelectorAll('.share-button').forEach(function(element) {
                element.addEventListener('click', function(event) {
                    event.preventDefault();

                    var social = this.getAttribute('data-social');
                    var urlToShare = '{{ url('/blog/'.$row->slug) }}';
                    
                    switch (social) {
                        case 'facebook':
                            // $blogUrl = 'https://www.example.com/your-blog-post';
                            // $blogTitle = 'Check out this blog post: ' . $blogUrl;
                            // Facebook
                            // var facebookAppURL = 'fb://page/page_id_or_username'; // Replace with the actual Facebook page ID or username
                            var facebookAppURL = 'fb://post?text=' + encodeURIComponent('Check out this blog: ' + urlToShare);
                            var facebookWebURL = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(urlToShare);
                            openAppOrWeb(facebookAppURL, facebookWebURL, 'facebook');
                            break;
                            
                        case 'twitter':
                            // Twitter
                            var twitterAppURL = 'twitter://post?text=Check out this blog: ' + encodeURIComponent(urlToShare);
                            var twitterWebURL = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(urlToShare);
                            openAppOrWeb(twitterAppURL, twitterWebURL, 'twitter');
                            break;

                        case 'whatsapp':
                            // WhatsApp
                            var whatsappAppURL = 'whatsapp://send?text=Check out this blog: ' + encodeURIComponent(urlToShare);
                            openAppOrWeb(whatsappAppURL, whatsappAppURL, 'whatsapp');
                            break;
                    }
                });
            });

            function openAppOrWeb(appURL, webURL, packageName) {
                var userAgent = navigator.userAgent || navigator.vendor || window.opera;
                var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);

                if (isMobile) {
                    if(packageName=='facebook'){
                        FB.ui({
                            method: 'share',
                            href: '{{ url('/blog/'.$row->slug) }}', // Replace with the URL you want to share
                        }, function(response) {
                            // Handle the response (e.g., check if the user shared the content)
                        });
                    }else{
                        window.location.href = appURL;
                    }                    
                    setTimeout(function() {
                        if(packageName=='facebook'){                            
                            window.location.href = 'https://play.google.com/store/apps/details?id=com.facebook.katana';                        
                        }else if(packageName=='twitter'){
                            window.location.href = 'https://play.google.com/store/apps/details?id=com.twitter.android';                        
                        }else if(packageName=='whatsapp'){
                            window.location.href = 'https://play.google.com/store/apps/details?id=com.whatsapp';                        
                        }
                    }, 1000); 
                } else {
                    window.open(webURL, '_blank');
                }
            }
            </script>
        @endif
    </body>
</html>