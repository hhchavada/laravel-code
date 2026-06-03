@extends('admin/layout/app')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/short-video')}}"> {{__('lang.admin_short_video')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_analytics')}}</h4>
    
    <div class="">
        <div class="row">
            <div class="col-sm-6 col-lg-6 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                          <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-eye ti-md"></i></span>
                          </div>
                          <h4 class="ms-1 mb-0">{{$totalShortVideoViewsCount}}</h4>
                        </div>
                        <p class="mb-1"> {{__('lang.admin_total_short_video_views')}}</p>
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
                          <h4 class="ms-1 mb-0">{{$totalGuestShortVideoViewsCount}}</h4>
                        </div>
                        <p class="mb-1"> {{__('lang.admin_total_guest_short_video_views')}}</p>
                     </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col">
            <div class="card mb-3">
                
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
                            {{__('lang.admin_short_video_views')}}({{count($uniqueViewsCount)}})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-share"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_short_video_share')}}({{count($shares)}})
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
                                              ({{ \Helpers::getParticularUserShortVideoViewCount($view->user_id,$view->short_video_id); }})
                                            </td>
                                            <td>
                                                {{date("d-m-Y",strtotime($view->created_at))}}</br>
                                                <span>{{date("h:i A",strtotime($view->created_at))}}</span>
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
                    <div class="tab-pane fade" id="form-tabs-share" role="tabpanel">                    
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{__('lang.admin_name')}}</th>
                                    <th>{{__('lang.admin_viewed_at')}}</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if(count($shares) > 0) 
                                    @foreach($shares as $share) 
                                        <tr>
                                            <td>
                                            @if(isset($share->user) && $share->user!=''){{$share->user->name}}@else Guest @endif
                                            </td>
                                            <td>
                                                {{date("d-m-Y",strtotime($share->created_at))}}</br>
                                                <span>{{date("h:i A",strtotime($share->created_at))}}</span>
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
                                {{$shares->withQueryString()->links('pagination::bootstrap-4')}}
                            </div>
                        </div>             
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection