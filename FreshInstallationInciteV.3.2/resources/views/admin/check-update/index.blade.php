@extends('admin/layout/app') @section('content') <div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4 display-inline-block">
    <span class="text-muted fw-light">
      <a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> / </span> {{__('lang.admin_check_update')}}
  </h4>
  <div class="card mb-4">
    <h5 class="card-header">{{__('lang.admin_check_update')}}</h5>
    <div class="card-body">
      <h6 class="fw-semibold mb-3">{{__('lang.admin_check_update_subtitle')}}</h6>
      <p class="w-50">
        {{__('lang.admin_check_update_description')}}
        <a href="https://newinciteweb.technofox.co.in/privacy-policy" target="_blank">{{__('lang.admin_check_logs')}}</a>
      </p>
      <p class="w-50 required">
        {{__('lang.admin_note')}} : {{__('lang.admin_check_update_note')}}
      </p>
      @if(setting('website_updates')==true)
        <a href="{{url('update-website')}}" class="btn btn-primary mt-2">
            {{__('lang.admin_update_website')}}
        </a>
      @endif
    </div>
  </div>
</div> 
@endsection