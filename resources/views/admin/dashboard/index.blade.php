@extends('admin/layout/app')
@section('content')

<link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/pages/cards-advance.css')}}"/>
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/swiper/swiper.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row d-flex align-items-stretch">
        <!--Boxes-->
        <div class="col-lg-4">
            <div class="row">
                <!-- First box (Top Row) -->
                <div class="col-sm-6 col-lg-6 mb-4">
                    <div class="card card-border-shadow-primary h-100">
                        @canany(['blog'])
                        <a href="{{url('admin/post?type=post')}}" style="color:unset;">
                        @endcanany
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-article ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{$blog}}</h4>
                                </div>
                                <p class="mb-1">{{__('lang.admin_total')}} {{__('lang.admin_blogs')}}</p>
                            </div>
                        @canany(['blog'])
                        </a>
                        @endcanany
                    </div>
                </div>

                <!-- Second box (Top Row) -->
                <div class="col-sm-6 col-lg-6 mb-4">
                    <div class="card card-border-shadow-warning h-100">
                        @canany(['blog'])
                        <a href="{{url('admin/post?type=quote')}}" style="color:unset;">
                        @endcanany
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-quote ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{$quote}}</h4>
                                </div>
                                <p class="mb-1">{{__('lang.admin_total')}} {{__('lang.admin_quotes')}}</p>
                            </div>
                        @canany(['blog'])
                        </a>
                        @endcanany
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Third box (Bottom Row) -->
                <div class="col-sm-6 col-lg-6 mb-4">
                    <div class="card card-border-shadow-danger h-100">
                        @can('category')
                        <a href="{{url('admin/category')}}" style="color:unset;">
                        @endcan
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-category ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{$category}}</h4>
                                </div>
                                <p class="mb-1">{{__('lang.admin_total')}} {{__('lang.admin_categories')}}</p>
                            </div>
                        @can('category')
                        </a>
                        @endcan
                    </div>
                </div>

                <!-- Fourth box (Bottom Row) -->
                <div class="col-sm-6 col-lg-6 mb-4">
                    <div class="card card-border-shadow-info h-100">
                        @canany(['user'])
                        <a href="{{url('admin/user')}}" style="color:unset;">
                        @endcanany
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-info"><i class="ti ti-users"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{$user}}</h4>
                                </div>
                                <p class="mb-1">{{__('lang.admin_total')}} {{__('lang.admin_users')}}</p>
                            </div>
                        @canany(['user'])
                        </a>
                        @endcanany
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Fifth box (Bottom Row) -->
                <div class="col-sm-6 col-lg-6 mb-4">
                    <div class="card card-border-shadow-danger h-100">
                        @can('ads')
                        <a href="{{url('admin/ads')}}" style="color:unset;">
                        @endcan
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-ad ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{$ad}}</h4>
                                </div>
                                <p class="mb-1">{{__('lang.admin_total')}} {{__('lang.admin_ads')}}</p>
                            </div>
                        @can('ads')
                        </a>
                        @endcan
                    </div>
                </div>

                <!-- Sixth box (Bottom Row) -->
                <div class="col-sm-6 col-lg-6 mb-4">
                    <div class="card card-border-shadow-info h-100">
                        @canany(['short-video'])
                        <a href="{{url('admin/short-video')}}" style="color:unset;">
                        @endcanany
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-video"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{$short_video}}</h4>
                                </div>
                                <p class="mb-1">{{__('lang.admin_total')}} {{__('lang.admin_short_video')}}</p>
                            </div>
                        @canany(['short-video'])
                        </a>
                        @endcanany
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Type doughnut chart -->
        <div class="col-lg-4">
          <div class="card">
            <h5 class="card-header">{{__('lang.admin_user_by_device_type')}}</h5>
            <div class="card-body">
              <canvas id="doughnutChartType" class="chartjs mb-6" data-height="350" style="max-height:240px!important"></canvas>
              <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1 mt-4 mb-3">
                <li class="ct-series-0 d-flex flex-column">
                  <h5 class="mb-0">Android</h5>
                  <span
                    class="badge badge-dot my-2 cursor-pointer rounded-pill"
                    style="background-color: rgb(102, 110, 232); width: 35px; height: 6px"></span>
                    <div class="text-muted" id="android_device_type">{{ number_format($android_percentage, 2) }}%</div>
                </li>
                <li class="ct-series-1 d-flex flex-column">
                  <h5 class="mb-0">IOS</h5>
                  <span
                    class="badge badge-dot my-2 cursor-pointer rounded-pill"
                    style="background-color: rgb(40, 208, 148); width: 35px; height: 6px"></span>
                    <div class="text-muted" id="ios_device_type">{{ number_format($ios_percentage, 2) }}%</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Login method doughnut chart -->
        <div class="col-lg-4">
          <div class="card">
            <h5 class="card-header">{{__('lang.admin_user_by_users')}} </h5>
            <div class="card-body">
              <canvas id="doughnutChart" class="chartjs mb-4" data-height="350" style="max-height:240px!important"></canvas>
              <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1 mb-3">
                <li class="ct-series-1 d-flex flex-column">
                  <h5 class="mb-0 fw-bold">{{__('lang.admin_email')}}</h5>
                  <span
                    class="badge badge-dot my-2 cursor-pointer rounded-pill"
                    style="background-color: #EA4335; width: 35px; height: 6px"
                  ></span>
                  <div class="text-muted">{{ number_format($total_email, 2) }}%</div>
                </li>
                <li class="ct-series-0 d-flex flex-column">
                  <h5 class="mb-0 fw-bold">{{__('lang.admin_apple')}}</h5>
                  <span
                    class="badge badge-dot my-2 cursor-pointer rounded-pill"
                    style="background-color: #fdac34; width: 35px; height: 6px"
                  ></span>
                  <div class="text-muted">{{ number_format($total_apple, 2) }}%</div>
                </li>
                <li class="ct-series-1 d-flex flex-column">
                  <h5 class="mb-0 fw-bold">{{__('lang.admin_google')}}</h5>
                  <span
                    class="badge badge-dot my-2 cursor-pointer rounded-pill"
                    style="background-color: #3b5998; width: 35px; height: 6px"
                  ></span>
                  <div class="text-muted">{{ number_format($total_gmail, 2) }}%</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
    </div>



    <!-- Post Charts -->
    <div class="row mt-4">
        <div class="col-12 mb-6">
          <div class="card">
            <div class="card-header header-elements">
              <div>
                <h5 class="card-title mb-0">{{__('lang.admin_all_post_statistics')}}</h5>
              </div>
              <div class="card-header-elements ms-auto py-0">
                <form id="">
                    @csrf
                    <div class="d-flex align-items-center">
                        <i class="font-medium-2" data-feather="calendar"></i>
                        <select id="filter_view" class="form-control" name="filter_view" style="margin-right: 2px;">
                            <option value="custom" {{ request('filter_view') === 'custom' ? 'selected' : '' }}>{{__('lang.admin_custom')}}</option>
                            <option value="today" {{ request('filter_view') === 'today' ? 'selected' : '' }}>{{__('lang.admin_today')}}</option>
                            <option value="current_month" {{ request('filter_view') === 'current_month' ? 'selected' : '' }}>{{__('lang.admin_current_month')}}</option>
                            <option value="last_7_days" {{ request('filter_view') === 'last_7_days' ? 'selected' : '' }}>{{__('lang.admin_last_7_days')}}</option>
                            <option value="last_month" {{ request('filter_view') === 'last_month' ? 'selected' : '' }}>{{__('lang.admin_last_month')}}</option>
                        </select>
                        <input type="text" id="dateRange_view" class="form-control flatpickr-range" placeholder="YYYY-MM-DD" name="date_range_view" style="margin-right: 5px;" />
                        <button type="submit" class="btn btn-primary data-submit" style="margin-right: 2px;"><i class="menu-icon tf-icons ti ti-search" style="margin-right: 2px;"></i></button>
                        <a href="{{ url('admin/dashboard') }}"><button type="button" class="btn btn-secondary">{{ __('lang.admin_reset') }}</button></a>
                    </div>
                </form>
              </div>
            </div>
            @if(request('filter_view') === 'custom')
                @php
                    $dateRange = explode(' to ', $_GET['date_range_view']);
                    $fromDate = date_create(explode(' ', $dateRange[0])[0]); // Extracting only the date
                    $toDate = date_create(explode(' ', $dateRange[1])[0]);

                    $fromMonth = date_format($fromDate, 'd M Y');
                    $toMonth = date_format($toDate, 'd M Y');
                @endphp
                <h6 class="text-center">{{ $fromMonth }} - {{ $toMonth }}</h6>
                <h6 id="viewMonthYearLabel" style="display: none;"></h6>
            @else
                <h6 id="viewMonthYearLabel" class="text-center"></h6>
            @endif
            <div class="card-body pt-2">
              <canvas id="postViewChart" class="chartjs" data-height="500"></canvas>
            </div>
          </div>
        </div>
    </div>
    <!-- /Post Charts -->

    <!-- User views Chart -->
    <div class="row mt-4">
        <div class="col-xl-12 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">{{ __('lang.admin_user_statistic') }}</h5>
                    <div class="card-action-element ms-auto py-0">
                        <form id="filterForm">
                            @csrf
                            <div class="d-flex align-items-center">
                                <i class="font-medium-2" data-feather="calendar"></i>
                                <select id="filter" class="form-control" name="filter" style="margin-right: 2px;">
                                    <option value="custom" {{ request('filter_view') === 'custom' ? 'selected' : '' }}>{{__('lang.admin_custom')}}</option>
                                    <option value="today" {{ request('filter_view') === 'today' ? 'selected' : '' }}>{{__('lang.admin_today')}}</option>
                                    <option value="current_month" {{ request('filter_view') === 'current_month' ? 'selected' : '' }}>{{__('lang.admin_current_month')}}</option>
                                    <option value="last_7_days" {{ request('filter_view') === 'last_7_days' ? 'selected' : '' }}>{{__('lang.admin_last_7_days')}}</option>
                                    <option value="last_month" {{ request('filter_view') === 'last_month' ? 'selected' : '' }}>{{__('lang.admin_last_month')}}</option>
                                </select>
                                <input type="text" id="dateRange" class="form-control flatpickr-range" placeholder="YYYY-MM-DD" name="date_range" style="margin-right: 5px;" />
                                <button type="submit" class="btn btn-primary data-submit" style="margin-right: 2px;"><i class="menu-icon tf-icons ti ti-search" style="margin-right: 2px;"></i></button>
                                <a href="{{ url('admin/dashboard') }}"><button type="button" class="btn btn-secondary">{{ __('lang.admin_reset') }}</button></a>
                            </div>
                        </form>
                    </div>
                </div>

                @if(isset($_GET['filter']) && request('filter') !== 'custom')
                    <h6 id="monthYearLabel" class="text-center">{{ ucfirst(str_replace('_', ' ', request('filter'))) }}</h6>
                @elseif(request('filter') === 'custom')
                    @php
                        $dateRange = explode(' to ', $_GET['date_range']);
                        $fromDate = date_create(explode(' ', $dateRange[0])[0]); // Extracting only the date
                        $toDate = date_create(explode(' ', $dateRange[1])[0]);

                        $fromMonth = date_format($fromDate, 'd M Y');
                        $toMonth = date_format($toDate, 'd M Y');
                    @endphp
                    <h6 class="text-center">{{ $fromMonth }} - {{ $toMonth }}</h6>
                    <h6 id="monthYearLabel" style="display: none;"></h6>
                @else
                    <h6 id="monthYearLabel" class="text-center"></h6>
                @endif

                <div class="card-body">
                    <canvas id="barChart1" class="chartjs" data-height="400"></canvas>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const filterSelect = document.getElementById('filter');
                const dateRangeInput = document.getElementById('dateRange');
                
                // Function to toggle the visibility of the date range input
                function toggleDateRangeInput() {
                    if (filterSelect.value !== 'custom') {
                        dateRangeInput.style.display = 'none';
                    } else {
                        dateRangeInput.style.display = 'block';
                    }
                }


                // Listen for filter change and update the form and chart data
                filterSelect.addEventListener('change', function() {
                    toggleDateRangeInput();
                });

                // Initialize based on current selected filter
                toggleDateRangeInput();
            });


            document.addEventListener('DOMContentLoaded', function() {
                const filterSelect = document.getElementById('filter_view');
                const dateRangeInput = document.getElementById('dateRange_view');
                
                // Function to toggle the visibility of the date range input
                function toggleDateRangeInput() {
                    if (filterSelect.value !== 'custom') {
                        dateRangeInput.style.display = 'none';
                    } else {
                        dateRangeInput.style.display = 'block';
                    }
                }


                // Listen for filter change and update the form and chart data
                filterSelect.addEventListener('change', function() {
                    toggleDateRangeInput();
                });

                // Initialize based on current selected filter
                toggleDateRangeInput();
            });
        </script>
        <div class="col-lg-12 mb-4"> 
            <div class="card">
              <div class="card-header">
                <h5 class="card-title display-inline-block">{{__('lang.admin_most_view_blogs')}}</h5>
              </div>
              <div class="table-responsive"> 
              <table class="table">
                  <thead class="table-light">
                    <tr>
                        <th>{{__('lang.admin_image')}}</th>
                        <th>{{__('lang.admin_title')}}</th>
                        <th>{{__('lang.admin_visibility')}}</th>
                        <th>{{__('lang.admin_views')}}</th>
                        <th>{{__('lang.admin_created_date_time')}}  </th>
                        <th>{{__('lang.admin_status')}}</th>
                        @canany(['update-blog-status','update-blog','send-notification','analytics','delete-blog'])
                        <th>{{__('lang.admin_action')}}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>    
                    @php $i=0; @endphp 
                    @if(count($most_viewed_blogs) > 0) 
                        @foreach($most_viewed_blogs as $most_viewed_blog) 
                            @php $i++; @endphp 
                            <tr>
                                <td>
                                    @if($most_viewed_blog->type=="post")
                                        @if($most_viewed_blog->image!='')
                                            <img src="{{ url('uploads/blog/80x45/'.$most_viewed_blog->image->image)}}" class="me-75" height="45" width="80" alt="{{$most_viewed_blog->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                                        @else
                                            <img src="{{ url('uploads/no-image.png')}}" class="me-75" height="45" width="80" alt="{{$most_viewed_blog->title}}"/>
                                        @endif
                                    @else
                                        <img src="{{ url('uploads/blog/'.$most_viewed_blog->background_image)}}" class="me-75" height="45" width="80" alt="{{$most_viewed_blog->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                                    @endif
                                </td>
                                <td>
                                    @if (Gate::check('update-blog'))
                                    <a href="{{url('/admin/update-blog/'.$most_viewed_blog->type.'/'.$most_viewed_blog->id)}}">{{$most_viewed_blog->title}}</a>
                                    @else
                                    {{$most_viewed_blog->title}}
                                    @endif
                                    </br>
                                    <span><small>{{$most_viewed_blog->category_names}}</small></span>
                                </td>
                                <td>
                                    @if(count($most_viewed_blog->blog_visibility))
                                        @foreach($most_viewed_blog->blog_visibility as $blog_visibility)
                                            @if($blog_visibility->visibility!='')
                                                <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">{{$blog_visibility->visibility->display_name}}</button>
                                            @endif
                                        @endforeach
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>
                                    {{$most_viewed_blog->view_count}}
                                </td>
                                <td>
                                    {{date("d-m-Y",strtotime($most_viewed_blog->created_at))}}</br>
                                    <span>{{date("h:i A",strtotime($most_viewed_blog->created_at))}}</span>
                                </td>
                                <td>
                                    @if($most_viewed_blog->status==1)
                                        <a href="javascript:;" class="btn btn-xs btn-success waves-effect waves-light" title="{{__('lang.admin_publish')}}">{{__('lang.admin_publish')}}</a>
                                    @elseif($most_viewed_blog->status==2)
                                        <a href="javascript:;" class="btn btn-xs btn-warning waves-effect waves-light" title="{{__('lang.admin_draft')}}">{{__('lang.admin_draft')}}</a>
                                    @elseif($most_viewed_blog->status==3)
                                        <a href="javascript:;" class="btn btn-xs btn-primary waves-effect waves-light" title="{{__('lang.admin_submit')}}">{{__('lang.admin_submit')}}</a>
                                    @elseif($most_viewed_blog->status==4)
                                        <a href="javascript:;" class="btn btn-xs btn-info waves-effect waves-light" title="{{__('lang.admin_scheduled')}}">{{__('lang.admin_scheduled')}}</a>
                                    @elseif($most_viewed_blog->status==0)
                                        <a href="javascript:;" class="btn btn-xs btn-danger waves-effect waves-light" title="{{__('lang.admin_unpublish')}}">{{__('lang.admin_unpublish')}}</a>
                                    @endif
                                </td>
                                @canany(['update-blog-status','update-blog','send-notification','analytics','delete-blog'])
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" title="{{__('lang.admin_select_action')}}">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('update-blog-status')
                                                @if($most_viewed_blog->status==1)
                                                    <a class="dropdown-item" href="{{url('/admin/update-post-status/'.$most_viewed_blog->id.'/0')}}" title="{{__('lang.admin_unpublish')}}">
                                                    <i class="ti ti-notes-off me-1 margin-top-negative-4"></i> {{__('lang.admin_unpublish')}} </a>
                                                @elseif($most_viewed_blog->status==3)
                                                    <a class="dropdown-item" href="{{url('/admin/update-post-status/'.$most_viewed_blog->id.'/1')}}" title="{{__('lang.admin_unpublish')}}">
                                                    <i class="ti ti-notes me-1 margin-top-negative-4"></i> {{__('lang.admin_publish')}} </a>
                                                @endif
                                            @endcan
                                            @can('update-blog')
                                            <a class="dropdown-item" href="{{url('/admin/update-post/'.$most_viewed_blog->type.'/'.$most_viewed_blog->id)}}" title="{{__('lang.admin_edit')}}">
                                            <i class="ti ti-pencil me-1 margin-top-negative-4"></i> {{__('lang.admin_edit')}} </a>
                                            @endcan
                                            @can('send-notification')
                                            <span class="dropdown-item send_notification_to_users" data-id="{{$most_viewed_blog->id}}" title="{{__('lang.admin_notification')}}" style="cursor:pointer">
                                            <i class="ti ti-bell me-1 margin-top-negative-4"></i> {{__('lang.admin_notification')}} </span>
                                            @endcan
                                            @can('analytics')
                                            <a class="dropdown-item" href="{{url('/admin/analytics/'.$most_viewed_blog->id)}}" title="{{__('lang.admin_analytics')}}">
                                            <i class="ti ti-report-analytics me-1 margin-top-negative-4"></i> {{__('lang.admin_analytics')}} </a>
                                            @endcan
                                            @can('blog-translation')
                                            <a class="dropdown-item" href="{{url('/admin/post/translation/'.$most_viewed_blog->id)}}" title="{{__('lang.admin_translation')}}">
                                            <i class="ti ti-language me-1 margin-top-negative-4"></i> {{__('lang.admin_translation')}} </a>
                                            @endcan
                                            @can('delete-blog')
                                            <form id="deleteForm_{{$most_viewed_blog->id}}" action="{{ url('admin/delete-post', $most_viewed_blog->id) }}" method="POST" onsubmit="return deleteConfirm('deleteForm_{{$most_viewed_blog->id}}');"> @csrf @method('DELETE') <button type="submit" class="dropdown-item" data-toggle="tooltip" data-placement="bottom" title="{{__('lang.admin_delete')}}">
                                                <i class="ti ti-trash me-1 margin-top-negative-4"></i>{{__('lang.admin_delete')}} </button>
                                            </form>
                                            @endcan
                                        </div>
                                </div>
                                </td>
                                @endcanany
                            </tr> 
                        @endforeach 
                    @else 
                        <tr>
                            <td colspan="7" class="record-not-found">
                                <span>{{__('lang.admin_record_not_found')}}</span>
                            </td>
                        </tr> 
                    @endif 
                </tbody>
                </table>
              </div>
            </div>
        </div>
        <div class="col-lg-12 mb-4"> 
            <div class="card">
              <div class="card-header">
                <h5 class="card-title display-inline-block">{{__('lang.admin_selected_categories')}}</h5>
              </div>
              <div class="table-responsive"> 
              <table class="table">
                  <thead class="table-light">
                    <tr class="text-nowrap">
                        <th>{{__('lang.admin_id')}}</th>
                        <th>{{__('lang.admin_image')}}</th>
                        <th>{{__('lang.admin_main_category')}}</th>
                        <th>{{__('lang.admin_name')}}</th>
                        <th>{{__('lang.admin_total_blogs')}}</th>
                        @can('update-category-column')
                        <th>{{__('lang.admin_featured')}}</th>
                        @endcan
                        @can('update-category-column')
                        <th>{{__('lang.admin_status')}}</th>
                        @endcan
                        @canany(['update-category', 'delete-category', 'translation-category'])
                        <th>{{__('lang.admin_action')}}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>    
                    @php $i=0; @endphp 
                    @if(count($most_selected_categories) > 0) 
                        @foreach($most_selected_categories as $most_selected_category) 
                            @php $i++; @endphp 
                            <tr>
                                <td>{{$i}}</td>
                                <td>
                                    <img src="{{ url('uploads/category/'.$most_selected_category->image)}}" class="me-75" height="50" width="50" alt="{{$most_selected_category->name}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                                </td>
                                <td>@if(isset($most_selected_category->main_category) && $most_selected_category->main_category!='')<a class="cursor-pointer" data-bs-toggle="offcanvas" data-bs-target="#edit-new-record_{{$most_selected_category->main_category->id}}" aria-controls="edit-new-record_{{$most_selected_category->main_category->id}}">{{$most_selected_category->main_category->name}}</a>@else--@endif</td>
                                <td><a class="cursor-pointer" data-bs-toggle="offcanvas" data-bs-target="#edit-new-record_{{$most_selected_category->id}}" aria-controls="edit-new-record_{{$most_selected_category->id}}">{{$most_selected_category->name}}</a></td>
                                <td>
                                    @if (Gate::check('blog'))
                                    <a href="{{url('admin/post?category_id='.$most_selected_category->id)}}">{{$most_selected_category->blog_count}}</a>
                                    @else
                                    {{$most_selected_category->blog_count}}
                                    @endif
                                </td>
                                @can('update-category-column')
                                <td> @if($most_selected_category->is_featured==1) 
                                    <a href="{{url('admin/update-category-column/'.$most_selected_category->id.'/is_featured/0')}}" title="{{__('lang.admin_yes')}}">
                                    <span class="badge bg-success">{{__('lang.admin_yes')}}</span>
                                    </a> @else <a href="{{url('admin/update-category-column/'.$most_selected_category->id.'/is_featured/1')}}" title="{{__('lang.admin_no')}}">
                                    <span class="badge bg-danger">{{__('lang.admin_no')}}</span>
                                    </a> @endif 
                                </td>
                                @endcan
                                @can('update-category-column')
                                <td> @if($most_selected_category->status==1) <a href="{{url('admin/update-category-column/'.$most_selected_category->id.'/status/0')}}" title="{{__('lang.admin_active')}}">
                                    <span class="badge bg-success">{{__('lang.admin_active')}}</span>
                                    </a> @else <a href="{{url('admin/update-category-column/'.$most_selected_category->id.'/status/1')}}" title="{{__('lang.admin_inactive')}}">
                                    <span class="badge bg-danger">{{__('lang.admin_inactive')}}</span>
                                    </a> @endif 
                                </td>
                                @endcan
                                @canany(['update-category', 'delete-category', 'translation-category'])
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" title="{{__('lang.admin_select_action')}}">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- @can('update-category')
                                            <a class="dropdown-item" type="button" data-bs-toggle="offcanvas" data-bs-target="#edit-new-record_{{$most_selected_category->id}}" aria-controls="edit-new-record_{{$most_selected_category->id}}" title="{{__('lang.admin_edit')}}">
                                            <i class="ti ti-pencil me-1 margin-top-negative-4"></i> {{__('lang.admin_edit')}} </a>
                                            @endcan -->
                                            @can('translation-category')
                                            <a class="dropdown-item" href="{{url('/admin/translation-category/'.$most_selected_category->id)}}" title="{{__('lang.admin_translation')}}">
                                            <i class="ti ti-language me-1 margin-top-negative-4"></i> {{__('lang.admin_translation')}} </a>
                                            @endcan
                                            @can('delete-category')
                                            <form id="deleteForm_{{$most_selected_category->id}}" action="{{ url('admin/delete-category', $most_selected_category->id) }}" method="POST" onsubmit="return deleteConfirm('deleteForm_{{$most_selected_category->id}}');"> @csrf @method('DELETE') <button type="submit" class="dropdown-item" data-toggle="tooltip" data-placement="bottom" title="{{__('lang.admin_delete')}}">
                                                <i class="ti ti-trash me-1 margin-top-negative-4"></i>{{__('lang.admin_delete')}} </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                                @endcanany
                            </tr> 
                        @endforeach 
                    @else 
                        <tr>
                            <td colspan="8" class="record-not-found">
                                <span>{{__('lang.admin_record_not_found')}}</span>
                            </td>
                        </tr> 
                    @endif 
                </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
    <!-- /User views Chart -->
</div>

<!--============Model===============-->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{route('send-notification-to-users')}}" method="post">
          @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="notificationModalLabel">Send Notification</h5>
          <button type="button" class="close_send_notification_modal" style="border: 0;font-size: 20px;color: red;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body row">
          <div class="form-group col-sm-6 col-md-6">
            <label>
              <input type="radio" name="recipient" value="all_users" checked>
               {{__('lang.admin_all_users')}}
            </label>
          </div>
          <div class="form-group col-sm-6 col-md-6">
            <label>
              <input type="radio" name="recipient" value="preferred_users">
              {{__('lang.admin_feed_users')}}
            </label>
          </div>
          <input type="hidden" name="id" class="hidden_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary close_send_notification_modal">Close</button>
          <button type="submit" class="btn btn-primary">Send Notification</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
$(document).on('click','.send_notification_to_users',function(){
    var id = $(this).attr('data-id');
    $('.hidden_id').val(id);
    $('#sendNotificationModal').modal('show');
});

$(document).on('click','.close_send_notification_modal',function(){
    $('#sendNotificationModal').modal('hide');
});
</script>