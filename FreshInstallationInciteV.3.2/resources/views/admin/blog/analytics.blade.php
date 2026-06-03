@extends('admin/layout/app')
@section('content')
<script src="{{ asset('admin-assets/js/ckeditor.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/tagify/tagify.css')}}" />

<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/select2/select2.css')}}" />

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4 display-inline-block"><span class="text-muted fw-light"><a href="{{url('admin/dashboard')}}">{{__('lang.admin_dashboard')}}</a> /<a href="{{url('admin/post')}}"> {{__('lang.admin_blog')}} {{__('lang.admin_list')}} </a>/</span> {{__('lang.admin_analytics')}}</h4>
    
    
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
                            {{__('lang.admin_blog')}} {{__('lang.admin_analytics')}}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                            type="button"
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#form-tabs-poll"
                            role="tab"
                            aria-selected="false"
                            >
                            {{__('lang.admin_blog_poll')}}({{$blog_pollsCount}})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                        <!-- Line Chart -->
                        <div class="col-12 mb-6">
                          <div class="card">
                            <div class="card-header d-flex justify-content-between">
                              <div>
                                <h5 class="card-title mb-0">{{__('lang.admin_blog')}} {{__('lang.admin_analytics')}}</h5>
                              </div>
                              <div class="d-sm-flex d-none align-items-center">
                                <form id="">
                                    @csrf
                                    <div class="d-flex align-items-center">
                                        <select class="form-control" name="analytic_type" style="margin-right: 2px;">
                                            <option value="view" {{ request('analytic_type') === 'view' ? 'selected' : '' }}>{{__('lang.admin_views')}}</option>
                                            <option value="share" {{ request('analytic_type') === 'share' ? 'selected' : '' }}>{{__('lang.admin_shares')}}</option>
                                            <option value="bookmark" {{ request('analytic_type') === 'bookmark' ? 'selected' : '' }}>{{__('lang.admin_bookmarks')}}</option>
                                        </select>
                                        <select id="filter_type" class="form-control" name="filter_type" style="margin-right: 2px;">
                                            <option value="custom" {{ request('filter_type') === 'custom' ? 'selected' : '' }}>{{__('lang.admin_custom')}}</option>
                                            <option value="today" {{ request('filter_type') === 'today' ? 'selected' : '' }}>{{__('lang.admin_today')}}</option>
                                            <option value="current_month" {{ request('filter_type') === 'current_month' || !request('filter_type') ? 'selected' : '' }}>{{__('lang.admin_current_month')}}</option>
                                            <option value="last_7_days" {{ request('filter_type') === 'last_7_days' ? 'selected' : '' }}>{{__('lang.admin_last_7_days')}}</option>
                                            <option value="last_month" {{ request('filter_type') === 'last_month' ? 'selected' : '' }}>{{__('lang.admin_last_month')}}</option>
                                        </select>
                                        <input type="text" id="dateRange_type" class="form-control flatpickr-range" placeholder="YYYY-MM-DD" name="date_range_type" style="margin-right: 5px;" />
                                        <button type="submit" class="btn btn-primary data-submit" style="margin-right: 2px;"><i class="menu-icon tf-icons ti ti-search" style="margin-right: 2px;"></i></button>
                                        <a href="{{ url('admin/analytics/'.Request::segment(3)) }}"><button type="button" class="btn btn-secondary">{{ __('lang.admin_reset') }}</button></a>
                                    </div>
                                </form>
                              </div>
                            </div>
                            <div class="card-body">
                                <h6 id="totalCountOfAnalytic" class="text-center"></h6>
                               <div id="postAnalyticsViewChart"></div>
                            </div>
                          </div>
                        </div>
                        <!-- /Line Chart -->
                    </div>
                    <div class="tab-pane fade" id="form-tabs-poll" role="tabpanel">
                        <div class="row">
                            <!-- Post boll doughnut chart -->
                            <div class="col-lg-4">
                                <div class="card">
                                <h5 class="card-header">{{__('lang.admin_post_poll_result')}}</h5>
                                    <div class="card-body">
                                      <canvas id="doughnutChartPostPoll" class="chartjs mb-6" data-height="350"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- Table -->
                            <div class="col-lg-8">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{__('lang.admin_question')}}</th>
                                            <th>{{__('lang.admin_option')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>    
                                        @if(count($blog_polls) > 0) 
                                            @foreach($blog_polls as $poll) 
                                                <tr>
                                                    <td>
                                                        {{ \Helpers::getParticularBlogQuestion($poll->blog_id) }}
                                                    </td>
                                                    <td>
                                                        @php 
                                                            // Get options for the current poll
                                                            $options = \Helpers::getParticularBlogPollQuestionOptions($poll->blog_id);
                                                            
                                                            // Define labels for options
                                                            $optionLabels = ['A' => 'Yes', 'B' => 'No', 'C' => 'Both', 'D' => 'None of these'];
                                                        @endphp
                                                        @if(count($options))
                                                            @foreach($options as $index => $option)
                                                                @php 
                                                                    // Get alphabet based on option index
                                                                    $alphabet = chr(65 + $index); // A, B, C, ...
                                                                @endphp
                                                                <p>
                                                                    {{ $alphabet }}: {{ $optionLabels[$alphabet] }} (
                                                                    Votes: {{ \App\Models\BlogAnalytic::where('type', 'blog_poll_option')
                                                                            ->where('blog_poll_option_id', $option->id)
                                                                            ->count() }} )
                                                                </p>
                                                            @endforeach
                                                        @endif
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
                                        {{$blog_polls->withQueryString()->links('pagination::bootstrap-4')}}
                                    </div>
                                </div>
                            </div>            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelect = document.getElementById('filter_type');
        const dateRangeInput = document.getElementById('dateRange_type');
        
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
@endsection