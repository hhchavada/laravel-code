@extends('admin/layout/app')
@section('content')
<script src="{{ asset('admin-assets/js/ckeditor.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/tagify/tagify.css')}}" />

<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/select2/select2.css')}}" />

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/ads')}}"> {{__('lang.admin_ad')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_analytics')}}</h4>
    
    <div class="">
        <div class="row">
            <div class="col-sm-6 col-lg-6 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                          <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-eye ti-md"></i></span>
                          </div>
                          <h4 class="ms-1 mb-0">{{$totalAdsViewsCount}}</h4>
                        </div>
                        <p class="mb-1"> {{__('lang.admin_total_ads_views')}}</p>
                     </div>
                </div>
            </div>
             <div class="col-sm-6 col-lg-6 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                          <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-eye ti-md"></i></span>
                          </div>
                          <h4 class="ms-1 mb-0">{{$totalGuestAdsViewsCount}}</h4>
                        </div>
                        <p class="mb-1"> {{__('lang.admin_total_guest_ads_views')}}</p>
                     </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                          <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-click ti-md"></i></span>
                          </div>
                          <h4 class="ms-1 mb-0">{{$totalAdsClicksCount}}</h4>
                        </div>
                        <p class="mb-1"> {{__('lang.admin_total_ads_click')}}</p>
                     </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                          <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-click ti-md"></i></span>
                          </div>
                          <h4 class="ms-1 mb-0">{{$totalGuestAdsClicksCount}}</h4>
                        </div>
                        <p class="mb-1"> {{__('lang.admin_total_guest_ads_click')}}</p>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card mb-3"></div>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-personal"
                            role="tab"
                            aria-selected="true"
                            >
                            {{__('lang.admin_ad_views')}}({{count($uniqueViewsCount)}})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-account"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_ads_click')}}({{count($uniqueClicksCount)}})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{__('lang.admin_name')}}</th>
                                    <th>{{__('lang.admin_viewed_at')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($views) > 0) 
                                    @foreach($views as $view) 
                                        <tr>
                                            <td>
                                                @if(isset($view->user) && $view->user!=''){{$view->user->name}}@else Deleted User @endif
                                                ({{ \Helpers::getParticularUserAdViewCount($view->user_id,$view->ad_id); }})
                                            </td>
                                            <td>
                                                {{date("d-m-Y", strtotime($view->created_at))}}<br/>
                                                <span>{{date("h:i A", strtotime($view->created_at))}}</span>
                                            </td>
                                        </tr> 
                                    @endforeach 
                                @else 
                                    <tr>
                                        <td colspan="2" class="record-not-found">
                                            <span>{{__('lang.admin_record_not_found')}}</span>
                                        </td>
                                    </tr> 
                                @endif 
                            </tbody>
                        </table>
                        <div class="card-footer">
                            <div class="pagination" style="float: right;">
                                {{$views->withQueryString()->links('pagination::bootstrap-4')}}
                            </div>
                        </div>                    
                    </div>
                    <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{__('lang.admin_name')}}</th>
                                    <th>{{__('lang.admin_clicked_at')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($clicks) > 0) 
                                    @foreach($clicks as $click) 
                                        <tr>
                                            <td>
                                                @if(isset($view->user) && $view->user!=''){{$view->user->name}}@else Deleted User @endif
                                                ({{ \Helpers::getParticularUserAdClickCount($view->user_id,$view->ad_id); }})
                                            </td>
                                            <td>
                                                {{date("d-m-Y", strtotime($click->created_at))}}<br/>
                                                <span>{{date("h:i A", strtotime($click->created_at))}}</span>
                                            </td>
                                        </tr> 
                                    @endforeach 
                                @else 
                                    <tr>
                                        <td colspan="2" class="record-not-found">
                                            <span>{{__('lang.admin_record_not_found')}}</span>
                                        </td>
                                    </tr> 
                                @endif 
                            </tbody>
                        </table>
                        <div class="card-footer">
                            <div class="pagination" style="float: right;">
                                {{$clicks->withQueryString()->links('pagination::bootstrap-4')}}
                            </div>
                        </div>                
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection