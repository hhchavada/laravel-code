<!-- Start footer -->

<footer id="footer-container" class="enable_footer_columns_dark">

    <div class="footer-bottom enable_footer_copyright_dark">
        <div class="container">
            <div class="row bottom_footer_menu_text">
                <div class="col-md-6 footer-left-copyright">{{__('lang.website_copyright')}} {{date('Y')}}. {{setting('powered_by')}}</div>
                <div class="col-md-6 footer-menu-bottom">
                    <ul id="menu-footer-menu" class="menu-footer">
                        @php $cmsList = \Helpers::getCmsForSite(); @endphp
                        @if(isset($cmsList) && count($cmsList))
                            @foreach($cmsList as $cms)
                                <li class="menu-item menu-item-10"><a href="{{url('/'.$cms->page_title)}}">{{$cms->title}}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- End footer