<table class="table">
    <thead class="table-light">
        <tr>
            @can('delete-ad')
            <th class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1" style="width: 17.4271px;" data-col="0" aria-label="">
                <input id="selectAll" type="checkbox" class="form-check-input">
            </th>
            @endcan
            <th>{{__('lang.admin_id')}}</th>
            <th>{{__('lang.admin_media')}}</th>
            <th>{{__('lang.admin_title')}}</th>
            <th>{{__('lang.admin_timestamp')}}</th>
            <th>{{__('lang.admin_frequency')}}</th>
            <th>{{__('lang.admin_created_date_time')}}</th>
            <th>{{__('lang.admin_expiry_status')}}</th>
            @can('update-ad-status')
            <th>{{__('lang.admin_status')}}</th>
            @endcan
            @canany(['update-ad', 'delete-ad','analytics-ad'])
            <th>{{__('lang.admin_action')}}</th>
            @endcanany
        </tr>
    </thead>
    <tbody id="ad_table">    
        @php $i=0; @endphp 
        @if(count($result) > 0) 
            @foreach($result as $row) 
                @php 
                    $i++; 
                    $today = \Carbon\Carbon::today(); 
                    $startDate = \Carbon\Carbon::parse($row->start_date);
                    $endDate = \Carbon\Carbon::parse($row->end_date);
                @endphp 
                <tr class="row1" data-id="{{ $row->id }}">
                    @can('delete-ad')
                    <td>
                        <input type="checkbox" class="form-check-input selectCheckbox" value="{{ $row->id }}">
                    </td>
                    @endcan
                    <td>{{$i}}</td>
                    <td>
                        @if($row->media_type=='image')
                             <img src="{{ url('uploads/ad/'.$row->media)}}" class="me-75" height="80" width="80" alt="{{$row->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                        @elseif($row->media_type=='video_url')
                            <iframe class="uploded-video-url-frame" src="https://www.youtube.com/embed/{{\Helpers::getVideoIdFromYoutubeUrl($row->video_url)}}" frameborder="0" allowfullscreen></iframe>
                        @elseif($row->media_type=='video')
                            <img src="{{asset('uploads/video_preview.png')}}" class="me-75" height="80" width="80" alt="{{$row->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                        @endif
                    </td>
                    <td>
                        @if (Gate::check('update-ad'))
                            <a href="{{url('/admin/update-ad/'.$row->id)}}">{{ \Helpers::getTableLimitedTitle($row->title,40) }}</a>
                        @else
                            {{ \Helpers::getTableLimitedTitle($row->title,40) }}
                        @endif
                    </td>
                    <td>
                        {{date("d-m-Y",strtotime($row->start_date))}} - {{date("d-m-Y",strtotime($row->end_date))}}
                    </td>
                    <td>
                        {{$row->frequency}}
                    </td>
                    <td>
                        {{date("d-m-Y",strtotime($row->created_at))}}</br>
                        <span>{{date("h:i A",strtotime($row->created_at))}}</span>
                    </td>
                    <td>
                        @if ($endDate->isPast()) 
                            <span class="badge bg-danger">{{__('lang.admin_expired')}}</span>
                        @elseif ($startDate->isFuture()) 
                            <span class="badge bg-warning">{{__('lang.admin_upcoming')}}</span>
                        @else 
                            <span class="badge bg-success">{{__('lang.admin_active')}}</span>
                        @endif
                    </td>
                    @can('update-ad-status')
                    <td>
                        @if($row->status==1) 
                        <a href="{{url('admin/update-ad-status/'.$row->id.'/0')}}">
                            <span class="badge bg-success">{{__('lang.admin_active')}}</span>
                        </a> @else <a href="{{url('admin/update-ad-status/'.$row->id.'/1')}}">
                            <span class="badge bg-danger">{{__('lang.admin_inactive')}}</span>
                        </a> 
                        @endif 
                    </td>
                    @endcan
                    @canany(['update-ad', 'delete-ad','analytics-ad'])
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                @can('analytics-ad')
                                <a class="dropdown-item" href="{{url('/admin/ad-analytics/'.$row->id)}}" title="{{__('lang.admin_analytics')}}">
                                <i class="ti ti-report-analytics me-1 margin-top-negative-4"></i> {{__('lang.admin_analytics')}} </a>
                                @endcan
                                @can('update-ad')
                                <a class="dropdown-item" href="{{url('/admin/update-ad/'.$row->id)}}">
                                <i class="ti ti-pencil me-1 margin-top-negative-4"></i> {{__('lang.admin_edit')}} </a>
                                @endcan
                                @can('delete-ad')
                                <form id="deleteForm_{{$row->id}}" onsubmit="return deleteConfirm('deleteForm_{{$row->id}}');" action="{{ url('admin/delete-ad', $row->id) }}" method="POST"> @csrf @method('DELETE') <button type="submit" class="dropdown-item" data-toggle="tooltip" data-placement="bottom" title="Delete">
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
                <td colspan="9" class="record-not-found">
                    <span>{{__('lang.admin_record_not_found')}}</span>
                </td>
            </tr> 
        @endif 
    </tbody>
</table>