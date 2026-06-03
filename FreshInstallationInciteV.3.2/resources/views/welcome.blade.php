<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title>@if(setting('meta_title')!='') {{setting('meta_title')}}@endif</title>
    <meta name="description" content="@if(setting('meta_description')!='') {{setting('meta_description')}}@endif">
    <meta name="keywords" content="@if(setting('meta_tag')!='') {{setting('meta_tag')}}@endif">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('site-assets/css/styles.css')}}">
    @if(setting('site_favicon')!='')
    <link rel="icon" type="image/x-icon" href="{{url('uploads/setting/'.setting('site_favicon'))}}" />
    @else
    <link rel="icon" type="image/x-icon" href="{{url('uploads/no-favicon.png')}}" />
    @endif 
    @if(setting('google_analytics_code')){{setting('google_analytics_code')}}@endif
</head>
<body id="top" class="ss-preload theme-static">
    <div id="preloader">
        <div id="loader" class="dots-fade">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <section id="intro" class="s-intro">
        <div class="s-intro__bg" style="background-image: url('{{ url('uploads/setting/'.setting('home_banner')) }}');"></div>
        <header class="s-intro__header"> 
            <div class="s-intro__logo">
                <a class="logo" href="{{url('/')}}">
                    <img src="{{url('uploads/setting/'.setting('website_admin_logo'))}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-logo-image.png') }}`" alt="Homepage">
                </a>
            </div>
        </header> 
    </section>

</body>
</html>