<table class="table">
    <thead class="table-light">
        <tr class="text-nowrap">
            <th>{{__('lang.admin_id')}}</th>
            <th>{{__('lang.admin_role_name')}}</th>
            @canany(['update-role', 'delete-role'])
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
                    <td>{{$i}}</td>
                    <td>
                        @if (Gate::check('update-role'))
                            @if($row->name!='')<a class="cursor-pointer" href="javascript:;" data-bs-toggle="modal" data-bs-target="#editRoleModal_{{$row->id}}" class="role-edit-modal">{{$row->name}}</a>@else -- @endif
                        @else
                            @if($row->name!=''){{$row->name}}@else -- @endif
                        @endif 
                    </td>
                    @canany(['update-role', 'delete-role'])
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                @can('update-role')
                                <a class="dropdown-item" type="button" href="javascript:;" data-bs-toggle="modal" data-bs-target="#editRoleModal_{{$row->id}}" class="role-edit-modal">
                                <i class="ti ti-pencil me-1 margin-top-negative-4"></i> {{__('lang.admin_edit')}} </a>
                                @endcan
                                @if($row->id != 1)
                                    @can('delete-role')
                                    <form id="deleteForm_{{$row->id}}" onsubmit="return deleteConfirm('deleteForm_{{$row->id}}');" action="{{ url('admin/delete-role', $row->id) }}" method="POST"> @csrf @method('DELETE') <button type="submit" class="dropdown-item" data-toggle="tooltip" data-placement="bottom" title="{{__('lang.admin_delete')}}">
                                        <i class="ti ti-trash me-1 margin-top-negative-4"></i>{{__('lang.admin_delete')}} </button>
                                    </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        <div class="modal fade" id="editRoleModal_{{$row->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                                <div class="modal-content p-3 p-md-5">
                                    <button
                                    type="button"
                                    class="btn-close btn-pinned"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                    ></button>
                                    <div class="modal-body">
                                        <div class="text-center mb-4">
                                            <h3 class="role-title mb-2">{{__('lang.admin_edit_role')}}</h3>
                                            <p class="text-muted">{{__('lang.admin_set_role_permission')}}</p>
                                        </div>
                                        <!-- Add role form -->
                                        <form class="row g-3" id="edit-record_{{$row->id}}" onsubmit="return validateRole('edit-record_{{$row->id}}');" action="{{url('admin/update-role')}}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$row->id}}">
                                        
                                            <div class="col-12 mb-4">
                                                <label class="form-label" for="name">{{__('lang.admin_role_name')}} <span class="required">*</span></label>
                                                <input
                                                    type="text"
                                                    id="name"
                                                    name="name"
                                                    class="form-control"
                                                    placeholder="{{__('lang.admin_role_name_placeholder')}}"
                                                    tabindex="-1"
                                                    value="{{$row->name}}"
                                                />
                                            </div>
                                        
                                            <div class="col-12">
                                                <h5>{{__('lang.admin_role_permissions')}}</h5>
                                        
                                                <!-- Permission Selection -->
                                                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-4">
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_List" type="checkbox" value="List" data-permission="List" onclick="selectAllSameData('permission-all-checkbox_List','permission-checkbox_List');"/>
                                                            <label class="form-check-label" for="All"> {{__('lang.admin_permission_all_list')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Add" type="checkbox" value="Add" data-permission="Add" onclick="selectAllSameData('permission-all-checkbox_Add','permission-checkbox_Add');"/>
                                                            <label class="form-check-label" for="Add"> {{__('lang.admin_permission_all_add')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Update" type="checkbox" value="Update" data-permission="Update" onclick="selectAllSameData('permission-all-checkbox_Update','permission-checkbox_Update');"/>
                                                            <label class="form-check-label" for="Update"> {{__('lang.admin_permission_all_update')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Status" type="checkbox" value="Status" data-permission="Status" onclick="selectAllSameData('permission-all-checkbox_Status','permission-checkbox_Status');"/>
                                                            <label class="form-check-label" for="Status"> {{__('lang.admin_permission_all_status_change')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Delete" type="checkbox" value="Delete" data-permission="Delete" onclick="selectAllSameData('permission-all-checkbox_Delete','permission-checkbox_Delete');"/>
                                                            <label class="form-check-label" for="Delete"> {{__('lang.admin_permission_all_delete')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Translation" type="checkbox" value="Translation" data-permission="Translation" onclick="selectAllSameData('permission-all-checkbox_Translation','permission-checkbox_Translation');"/>
                                                            <label class="form-check-label" for="Translation"> {{__('lang.admin_permission_all_translation')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Analytics" type="checkbox" value="Analytics" data-permission="Analytics" onclick="selectAllSameData('permission-all-checkbox_Analytics','permission-checkbox_Analytics');"/>
                                                            <label class="form-check-label" for="Analytics"> {{__('lang.admin_permission_all_analytics')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Notification" type="checkbox" value="Notification" data-permission="Notification" onclick="selectAllSameData('permission-all-checkbox_Notification','permission-checkbox_Notification');"/>
                                                            <label class="form-check-label" for="Notification"> {{__('lang.admin_permission_all_notification')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-all-checkbox_Personalization" type="checkbox" value="Personalization" data-permission="Personalization" onclick="selectAllSameData('permission-all-checkbox_Personalization','permission-checkbox_Personalization');"/>
                                                            <label class="form-check-label" for="Personalization"> {{__('lang.admin_permission_all_personalization')}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                        
                                                <!-- Permission Table -->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>{{__('lang.admin_module')}}</th>
                                                                <th>{{__('lang.admin_permissions')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($permission as $value)
                                                            <tr>
                                                                <td class="fw-semibold">@if($value->module=='Blog'){{__('lang.blog_quote')}} @else {{$value->module}} @endif</td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap">
                                                                        @foreach($value->permission as $detail)
                                                                        <div class="form-check me-3 me-lg-5">
                                                                            @if($detail->permission_name=='Status Change')
                                                                                <input class="form-check-input permission-checkbox_Status" type="checkbox" id="{{ $detail->route_name }}" name="permission[]" value="{{$detail->id}}" {{\Helpers::checkRoleHasPermission($row->id,$detail->id) || $detail->is_default ? 'checked' : ''}} />
                                                                            @else
                                                                                <input class="form-check-input permission-checkbox_{{ $detail->permission_name }}" type="checkbox" id="{{ $detail->route_name }}" name="permission[]" value="{{$detail->id}}" {{\Helpers::checkRoleHasPermission($row->id,$detail->id) || $detail->is_default ? 'checked' : ''}} />
                                                                            @endif
                                                                            <label class="form-check-label" for="{{ $detail->route_name }}"> {{ $detail->permission_name }} </label>
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- Permission Table -->
                                            </div>
                                        
                                            <div class="col-12 text-center mt-4">
                                                <button type="submit" class="btn btn-primary me-sm-3 me-1">{{__('lang.admin_button_save_changes')}}</button>
                                                <button
                                                    type="reset"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"
                                                >
                                                {{__('lang.admin_button_cancel')}}
                                                </button>
                                            </div>
                                        </form>
                                        <!--/ Add role form -->
                                    </div>
                                </div>
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