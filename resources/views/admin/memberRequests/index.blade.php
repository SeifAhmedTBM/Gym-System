@extends('layouts.admin')
@section('content')
<div class="card shadow">
    <div class="card-header">
        <i class="fas fa-hand-paper"></i> {{ trans('global.member_requests') }}
    </div>
    <div class="card-body">
        <div class="mb-2">
            @include('admin_includes.filters', [
            'columns' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'related_to' => 'member'],
                'phone' => ['label' => 'Phone', 'type' => 'number', 'related_to' => 'member'],
                'member_code' => ['label' => 'Member Code', 'type' => 'number', 'related_to' => 'member'],
                'status' => ['label' => 'Status', 'type' => 'select', 'data' => ['approved' => 'Approved', 'rejected' => 'Rejected', 'pending' => 'Pending']],
                'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true],
            ],
                'route' => 'admin.member-requests.index'
            ])

            @can('export_member_requests')
                <a href="{{ route('admin.member-requests-sheet.export', request()->all()) }}" class="btn btn-dribbble text-white">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
            
            <h3 class="float-right">
                {{ trans('global.total_requests') . ' : ' }} <span class="text-danger font-weight-bold">{{ $member_requests->count() }}</span>
            </h3>
        </div>
        <table class="table table-bordered table-outline table-hover zero-configuration">
            <thead class="thead-light">
                <tr>
                    <th class="text-dark font-weight-bold">#</th>
                    <th class="text-dark font-weight-bold">{{ trans('cruds.member.title_singular') }}</th>
                    <th class="text-dark font-weight-bold">{{ trans('global.subject') }}</th>
                    <th class="text-dark font-weight-bold">{{ trans('global.last_comment') }}</th>
                    <th class="text-dark font-weight-bold">{{ trans('cruds.status.title_singular') }}</th>
                    <th class="text-dark font-weight-bold">{{ trans('cruds.bonu.fields.created_by') }}</th>
                    <th class="text-dark font-weight-bold">{{ trans('global.created_at') }}</th>
                    <th class="text-dark font-weight-bold">{{ trans('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($member_requests as $member_request)
                    <tr>
                        <td>#{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('admin.members.show',$member_request->member_id) }}">
                                <i class="fa fa-user-circle"></i> 
                                <span class="font-weight-bold"> {{ $member_request->member->name ?? '-' }} </span>
                            </a> <br>
                            <i class="fa fa-phone"></i> <span class="font-weight-bold">{{ $member_request->member->phone }}</span> <br>
                            <span class="font-weight-bold">{{ App\Models\Setting::first()->member_prefix ?? '' }}{{ $member_request->member->member_code }}</span> <br>
                            <i class="fas fa-venus-mars"></i> <span class="font-weight-bold">{{ Str::ucfirst($member_request->member->gender) }}</span>
                        </td>
                        <td>{{ $member_request->subject }}</td>
                        <td>{!! $member_request->member_request_replies()->latest()->first()->reply ?? '<span class="badge badge-danger">No replies yet !</span>' !!}</td>
                        
                        <td>
                            <span class="badge px-2 py-2 badge-{{ App\Models\MemberRequest::STATUS[$member_request->status] }}">
                                {{ Str::ucfirst($member_request->status) }}
                            </span>
                        </td>
                        @if($member_request->user)
                        <td>{{ $member_request->user->name }}</td>
                        @else
                        <td> - </td>
                        @endif
                        <td>{{ $member_request->created_at->toFormattedDateString() }}</td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                    aria-expanded="false">
                                    {{ trans('global.action') }}
                                </a>
                            
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a href="{{ route('admin.member-requests.show', $member_request->id) }}" class="dropdown-item">
                                        <i class="fa fa-eye"></i> &nbsp;  {{ trans('global.view') }}
                                    </a>

                                    @can('member_requests_approve')
                                        @if ($member_request->status == 'pending')
                                            <button data-toggle="modal" data-target="#updateRequestStatus" data-request-id="{{ $member_request->id }}" data-status="approved" onclick="updateRequestStatus(this)" class="dropdown-item">
                                                <i class="fa fa-check-circle"></i> &nbsp;  {{ trans('global.approve') }}
                                            </button>
        
                                            <button data-toggle="modal" data-target="#updateRequestStatus" data-request-id="{{ $member_request->id }}" data-status="rejected" onclick="updateRequestStatus(this)" class="dropdown-item">
                                                <i class="fa fa-times-circle"></i> &nbsp;  {{ trans('global.reject') }}
                                            </button>
                                        @endif
                                    @endcan

                                    <a href="{{ route('admin.member-requests.edit', $member_request->id) }}" class="dropdown-item">
                                        <i class="fa fa-edit"></i> &nbsp;  {{ trans('global.edit') }}
                                    </a>

                                    <form action="{{ route('admin.member-requests.destroy', $member_request->id) }}" method="POST"onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa fa-times"></i> &nbsp; {{ trans('global.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <td colspan="7" class="text-center">{{ trans('global.no_data_available') }}</td>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $member_requests->appends(request()->all())->links() }}
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="updateRequestStatus" tabindex="-1" aria-labelledby="updateRequestStatusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateRequestStatusLabel">{{ trans('global.update_request_status') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'PUT', 'id' => 'updateRequestStatusForm']) !!}
            <div class="modal-body">
                <h3 class="text-center text-primary font-weight-bold my-3">
                    {{ trans('global.update_request_status?') }}
                </h3>
            </div>
            <input type="hidden" name="status" id="request_status">
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-check-circle"></i> {{ trans('global.submit') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function updateRequestStatus(button) {
            let request_id = $(button).data('request-id');
            $("#request_status").val($(button).data('status'));
            let url = "{{ route('admin.memberRequest.updateStatus', ':member_request') }}";
            url = url.replace(':member_request', request_id);
            $("#updateRequestStatusForm").attr('action', url);
        }
    </script>
@endsection