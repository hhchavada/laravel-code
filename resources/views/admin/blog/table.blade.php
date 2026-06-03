<table class="table">
    <thead class="table-light">
        <tr>
            @can('delete-blog')
            <th class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1" style="width: 17.4271px;" data-col="0" aria-label="">
                <input id="selectAll" type="checkbox" class="form-check-input">
            </th>
            @endcan
            <th>{{__('lang.admin_image')}}</th>
            <th>@sortablelink('title', __('lang.admin_title'))</th>
            <th>{{__('lang.admin_visibility')}}</th>
            <th>{{__('lang.admin_views')}}</th>
            <th>@sortablelink('schedule_date', __('lang.admin_publish_date_time'))</th>
            <th>@sortablelink('created_at', __('lang.admin_last_modified_date_time'))</th>
            <th>{{__('lang.admin_status')}}</th>
            @canany(['update-blog', 'delete-blog','send-notification','analytics','blog-translation'])
            <th>{{__('lang.admin_action')}}</th>
            @endcanany
        </tr>
    </thead>
    <tbody>    
        @php $i=0; @endphp 
        @if(count($result) > 0) 
            @foreach($result as $row) 
                @php $i++; @endphp 
                <tr>
                    @can('delete-blog')
                    <td>
                        <input type="checkbox" class="form-check-input selectCheckbox" value="{{ $row->id }}">
                    </td>
                    @endcan
                    <td>
                        @if($row->type=="post")
                            @if($row->image!='')
                            <img src="{{ url('uploads/blog/80x45/'.$row->image->image)}}" class="me-75" height="45" width="80" alt="{{$row->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                            @else
                            <img src="{{ url('uploads/no-image.png')}}" class="me-75" height="45" width="80" alt="{{$row->title}}"/>
                            @endif
                        @else
                            @if($row->background_image!='')
                            <img src="{{ url('uploads/blog/'.$row->background_image)}}" class="me-75"  width="80" alt="{{$row->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                            @else
                            <img src="{{ url('uploads/blog/80x45/'.$row->image->image)}}" class="me-75" height="45" width="80" alt="{{$row->title}}" onerror="this.onerror=null;this.src=`{{ asset('uploads/no-image.png') }}`" />
                            @endif
                        @endif

                    </td>
                    <td>
                        @if (Gate::check('update-blog'))
                            <a href="{{url('/admin/update-post/'.$row->type.'/'.$row->id)}}">{{\Helpers::getTableLimitedTitle($row->title,40)}} </a>  
                        @else
                            {{\Helpers::getTableLimitedTitle($row->title,40)}}
                        @endif
                        <span class="badge rounded-pill @if($row->type=='post') bg-primary @else bg-info @endif" style="height: 7px;width: auto;padding-top: 2px;padding-bottom: 14px;padding-left: 5px;padding-right: 5px;font-size: 10px;">{{ucfirst($row->type)}}</span>
                        </br>
                        <span><small>{{$row->category_names}}</small></span>
                    </td>
                    <td>
                        @if($row->is_featured==1)
                            <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">{{__('lang.admin_featured')}}</button>
                        @endif
                        @if(count($row->blog_visibility) || $row->is_featured==1)
                            @foreach($row->blog_visibility as $blog_visibility)
                                @if($blog_visibility->visibility!='')
                                    <button type="button" class="btn btn-xs btn-primary waves-effect waves-light">{{$blog_visibility->visibility->display_name}}</button>
                                @endif
                            @endforeach
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        {{$row->view_count}}
                    </td>
                    <td>
                        {{date("d-m-Y",strtotime($row->schedule_date))}}</br>
                        <span>{{date("h:i A",strtotime($row->schedule_date))}}</span>
                    </td>
                   <td>
                        {{ date("d-m-Y", strtotime($row->updated_at ?? $row->created_at)) }}<br>
                        <span>{{ date("h:i A", strtotime($row->updated_at ?? $row->created_at)) }}</span>
                    </td>
                    <td>
                        @if($row->status==1)
                            <a href="javascript:;" 
                               class="btn btn-xs btn-success changeStatus"
                               data-id="{{ $row->id }}"
                               data-text="{{ __('lang.swal_publish_to_unpublish') }}">
                                {{ __('lang.admin_publish') }}
                            </a>

                        @elseif($row->status==2)
                            <a href="javascript:;" 
                               class="btn btn-xs btn-warning changeStatus"
                               data-id="{{ $row->id }}"
                               data-text="{{ __('lang.swal_draft_to_submit') }}">
                                {{ __('lang.admin_draft') }}
                            </a>

                        @elseif($row->status==3)
                            <a href="javascript:;" 
                               class="btn btn-xs btn-primary changeStatus"
                               data-id="{{ $row->id }}"
                               data-text="{{ __('lang.swal_submit_to_publish') }}">
                                {{ __('lang.admin_submit') }}
                            </a>

                        @elseif($row->status==4)
                            <span class="btn btn-xs btn-info">
                                {{ __('lang.admin_scheduled') }}
                            </span>

                        @elseif($row->status==0)
                            <a href="javascript:;" 
                               class="btn btn-xs btn-danger changeStatus"
                               data-id="{{ $row->id }}"
                               data-text="{{ __('lang.swal_unpublish_to_publish') }}">
                                {{ __('lang.admin_unpublish') }}
                            </a>
                        @endif
                    </td>
                    @canany(['update-blog', 'delete-blog','send-notification','analytics','blog-translation'])
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" title="{{__('lang.admin_select_action')}}">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                @can('update-blog-status')
                                    @if($row->status==1)
                                        <a class="dropdown-item" href="{{url('/admin/update-post-status/'.$row->id.'/0')}}" title="{{__('lang.admin_unpublish')}}">
                                        <i class="ti ti-notes-off me-1 margin-top-negative-4"></i> {{__('lang.admin_unpublish')}} </a>
                                    @elseif($row->status==3)
                                        <a class="dropdown-item" href="{{url('/admin/update-post-status/'.$row->id.'/1')}}" title="{{__('lang.admin_unpublish')}}">
                                        <i class="ti ti-notes me-1 margin-top-negative-4"></i> {{__('lang.admin_publish')}} </a>
                                    @endif
                                @endcan
                                @can('update-blog')
                                <a class="dropdown-item" href="{{url('/admin/update-post/'.$row->type.'/'.$row->id)}}" title="{{__('lang.admin_edit')}}">
                                <i class="ti ti-pencil me-1 margin-top-negative-4"></i> {{__('lang.admin_edit')}} </a>
                                @endcan
                                @can('send-notification')
                                <span class="dropdown-item send_notification_to_users" data-id="{{$row->id}}" title="{{__('lang.admin_notification')}}" style="cursor:pointer">
                                <i class="ti ti-bell me-1 margin-top-negative-4"></i> {{__('lang.admin_notification')}} </span>
                                @endcan
                                @can('analytics')
                                <a class="dropdown-item" href="{{url('/admin/analytics/'.$row->id)}}" title="{{__('lang.admin_analytics')}}">
                                <i class="ti ti-report-analytics me-1 margin-top-negative-4"></i> {{__('lang.admin_analytics')}} </a>
                                @endcan
                                @can('blog-translation')
                                <a class="dropdown-item" href="{{url('/admin/post/translation/'.$row->id)}}" title="{{__('lang.admin_translation')}}">
                                <i class="ti ti-language me-1 margin-top-negative-4"></i> {{__('lang.admin_translation')}} </a>
                                @endcan
                                @can('delete-blog')
                                <form id="deleteForm_{{$row->id}}" action="{{ url('admin/delete-post', $row->id) }}" method="POST" onsubmit="return deleteConfirm('deleteForm_{{$row->id}}');"> @csrf @method('DELETE') <button type="submit" class="dropdown-item" data-toggle="tooltip" data-placement="bottom" title="{{__('lang.admin_delete')}}">
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