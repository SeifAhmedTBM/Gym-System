<div class="tab-pane fade" id="session_attendances" role="tabpanel" aria-labelledby="session_attendances-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover zero-configuration">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('cruds.membership.title') }}</th>
                        <th>{{ trans('cruds.membershipAttendance.fields.date') }}</th>
                        <th> Options </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($member->trainer_attendants as $t_attend)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.memberships.show', $t_attend->membership_id) }}">
                                    {{ $t_attend->membership->service_pricelist->name ?? '-' }}
                                    -
                                    {{ $t_attend->membership->service_pricelist->service->name ?? '-' }}
                                </a>
                            </td>
                            <td>{{ date('Y-m-d', strtotime($t_attend->created_at)) }}</td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button"
                                        id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('global.action') }}
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        @can('membership_attendance_delete')
                                            <form action="{{ route('admin.trainer-attendances.destroy', $t_attend->id) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?');"
                                                style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fa fa-trash"></i> &nbsp; Delete
                                                </button>
                                            </form>
                                        @endcan
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
