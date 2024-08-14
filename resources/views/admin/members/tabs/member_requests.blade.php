<div class="tab-pane fade" id="memberRequests" role="tabpanel" aria-labelledby="memberRequests-tab">
    <div class="form-group">
        <a class="btn btn-info btn-sm" data-member-id="{{ $member->id }}" data-toggle="modal"
            data-target="#memberRequestModal" onclick="createMemberRequest(this)" href="javascript:void(0)">
            <i class="fas fa-hand-paper"></i> &nbsp; {{ trans('global.member_request') }}
        </a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.subject') }}</th>
                        <th>{{ trans('global.status') }}</th>
                        <th>{{ trans('global.last_comment') }}</th>
                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->memberRequests as $member_request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $member_request->subject ?? '-' }}</td>
                            <td>
                                <span
                                    class="badge px-2 py-2 badge-{{ App\Models\MemberRequest::STATUS[$member_request->status] }}">
                                    {{ Str::ucfirst($member_request->status) }}
                                </span>
                            </td>
                            <td>{!! $member_request->member_request_replies()->latest()->first()->reply ?? '<span class="badge badge-danger">No replies yet !</span>' !!}</td>
                            <td>{{ $member_request->user->name ?? '-' }}</td>
                            <td>{{ $member_request->created_at->toFormattedDateString() . ' , ' . $member_request->created_at->format('g:i A') }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button"
                                        id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('global.action') }}
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <a href="{{ route('admin.member-requests.show', $member_request->id) }}"
                                            class="dropdown-item">
                                            <i class="fa fa-eye"></i> &nbsp;
                                            {{ trans('global.view') }}
                                        </a>

                                        @can('member_requests_approve')
                                            @if ($member_request->status == 'pending')
                                                <button data-toggle="modal" data-target="#updateRequestStatus"
                                                    data-request-id="{{ $member_request->id }}" data-status="approved"
                                                    onclick="updateRequestStatus(this)" class="dropdown-item">
                                                    <i class="fa fa-check-circle"></i> &nbsp;
                                                    {{ trans('global.approve') }}
                                                </button>

                                                <button data-toggle="modal" data-target="#updateRequestStatus"
                                                    data-request-id="{{ $member_request->id }}" data-status="rejected"
                                                    onclick="updateRequestStatus(this)" class="dropdown-item">
                                                    <i class="fa fa-times-circle"></i> &nbsp;
                                                    {{ trans('global.reject') }}
                                                </button>
                                            @endif
                                        @endcan

                                        <a href="{{ route('admin.member-requests.edit', $member_request->id) }}"
                                            class="dropdown-item">
                                            <i class="fa fa-edit"></i> &nbsp;
                                            {{ trans('global.edit') }}
                                        </a>

                                        <form
                                            action="{{ route('admin.member-requests.destroy', $member_request->id) }}"
                                            method="POST"onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa fa-times"></i> &nbsp;
                                                {{ trans('global.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="46" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="memberRequestModal" tabindex="-1" aria-labelledby="memberRequestModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="memberRequestModalLabel">{{ trans('global.create') }}
                    {{ trans('global.member_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.member-requests.store')]) !!}
            <div class="modal-body">
                <input type="hidden" name="member_id" id="member_id_input">
                <div class="form-group">
                    {!! Form::label('subject', trans('global.subject'), ['class' => 'required']) !!}
                    {!! Form::text('subject', old('subject'), ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('comment', trans('global.comment'), ['class' => 'required']) !!}
                    {!! Form::textarea('comment', old('comment'), ['class' => 'form-control', 'rows' => 3]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-check-circle"></i> {{ trans('global.create') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    function createMemberRequest(button) 
    {
        let member_id = $(button).data('member-id');
        $("#member_id_input").val(member_id);
    }

    function updateRequestStatus(button) 
    {
        let request_id = $(button).data('request-id');
        $("#request_status").val($(button).data('status'));
        let url = "{{ route('admin.memberRequest.updateStatus', ':member_request') }}";
        url = url.replace(':member_request', request_id);
        $("#updateRequestStatusForm").attr('action', url);
    }
</script>