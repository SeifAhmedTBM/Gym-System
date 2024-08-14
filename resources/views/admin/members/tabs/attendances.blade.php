<div class="tab-pane fade" id="attendances" role="tabpanel" aria-labelledby="attendances-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover zero-configuration">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('cruds.membership.title') }}</th>
                        <th>{{ trans('cruds.membershipAttendance.fields.sign_in') }}</th>
                        {{-- @if (\App\Models\Setting::first()->has_lockers == true) --}}
                        <th>{{ trans('cruds.membershipAttendance.fields.sign_out') }}</th>
                        {{-- @endif --}}
                        <th>{{ trans('cruds.membershipAttendance.fields.date') }}</th>
                        <th> Status </th>
                        {{-- <th>{{ trans('cruds.membershipAttendance.fields.locker_status') }}
                        </th> --}}
                        @if (\App\Models\Setting::first()->has_lockers == true)
                            <th>{{ trans('cruds.membershipAttendance.fields.locker') }}</th>
                        @endif
                        <th> Options </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($member->membership_attendances as $attend)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.memberships.show', $attend->membership->id) }}">
                                    {{ $attend->membership->service_pricelist->name ?? '-' }}
                                    -
                                    {{ $attend->membership->service_pricelist->service->name ?? '-' }}
                                </a>
                            </td>
                            <td>{{ date('g:i A', strtotime($attend->sign_in)) }}</td>
                            <td>
                                {!! !is_null($attend->sign_out)
                                    ? date('g:i A', strtotime($attend->sign_out))
                                    : '<span class="badge badge-danger">Not Found</span>' !!}
                            </td>
                            <td>{{ date('Y-m-d', strtotime($attend->created_at)) }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ \App\Models\Membership::STATUS[$attend->membership_status] }} p-2">
                                    <i class="fa fa-recycle"></i>
                                    {{ ucfirst($attend->membership_status) ?? '-' }}
                                </span>
                            </td>
                            @if (\App\Models\Setting::first()->has_lockers == true)
                                <td>{!! $attend->locker ?? '<span class="badge badge-danger">Not Found</span>' !!}</td>
                            @endif
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button"
                                        id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('global.action') }}
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                        @can('membership_attendance_edit')
                                            {{-- <a href="{{ route('admin.membership-attendances.edit', $attend->id) }}"
                                            class="dropdown-item">
                                            <i class="fa fa-edit"> </i> &nbsp; {{ trans('global.edit') }}
                                            </a> --}}
                                        @endcan

                                        @can('membership_attendance_delete')
                                            <form action="{{ route('admin.membership-attendances.destroy', $attend->id) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?');"
                                                style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i> &nbsp; Delete
                                                </button>
                                            </form>
                                        @endcan

                                        <a class="dropdown-item" data-target="#editSigninAndSignoutModal"
                                            data-toggle="modal" href="javascript:void(0)"
                                            onclick="editSigninAndSignout(this)" data-locker="{{ $attend->locker }}"
                                            data-sign-in="{{ $attend->sign_in }}"
                                            data-sign-out="{{ $attend->sign_out }}"
                                            data-update="{{ route('admin.membership-attendances.update', $attend->id) }}"
                                            data-get-url="{{ route('admin.membership-attendances.edit', $attend->id) }}">
                                            <i class="fa fa-edit"></i> &nbsp;
                                            {{ trans('global.edit_sign_in_and_out') }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editSigninAndSignoutModal" tabindex="-1" aria-labelledby="editSigninAndSignoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSigninAndSignoutModalLabel">{{ trans('global.edit') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'PUT', 'id' => 'editSigninAndSignoutForm']) !!}
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-md-4 form-group">
                        {!! Form::label('sign_in', trans('cruds.membershipAttendance.fields.sign_in')) !!}
                        {!! Form::time('sign_in', null, ['class' => 'form-control', 'id' => 'edit_sign_in']) !!}
                    </div>
                    <div class="col-md-4 form-group">
                        {!! Form::label('sign_out', trans('cruds.membershipAttendance.fields.sign_out')) !!}
                        {!! Form::time('sign_out', null, ['class' => 'form-control', 'id' => 'edit_sign_out']) !!}
                    </div>
                    <div class="col-md-4 form-group">
                        {!! Form::label('locker', trans('cruds.membershipAttendance.fields.locker')) !!}
                        {!! Form::text('locker', null, ['class' => 'form-control', 'id' => 'edit_locker']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    {{ trans('global.close') }}
                </button>
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    function editSigninAndSignout(button) 
    {
        let sign_in = $(button).data('sign-in');
        let sign_out = $(button).data('sign-out');
        let locker = $(button).data('locker');
        let formURL = $(button).data('update');
        let getURL = $(button).data('get-url');
        $("#editSigninAndSignoutForm").attr('action', formURL);
        $.ajax({
            method: "GET",
            url: getURL,
            success: function(response) {
                $("#edit_sign_in").val(response.sign_in);
                $("#edit_sign_out").val(response.sign_out);
                $("#edit_locker").val(response.membership_attendances.locker);
            }
        })
    }
</script>
