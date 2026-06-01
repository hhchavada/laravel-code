@php
$localVersion = \Helpers::getVersion(base_path('public/version.json'));
@endphp
<footer class="content-footer footer bg-footer-theme">
  <div class="container-xxl">
    <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
      <div><a href="{{url('/')}}" target="_blank" class="fw-semibold"> © {{date('Y')}} {{__('lang.admin_footer_made_with')}} {{setting('site_name')}}</a>
      </div>
      <div>
         {{__('lang.admin_version')}} : {{$localVersion}}
      </div>
    </div>
  </div>
</footer>