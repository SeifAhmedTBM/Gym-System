<div class="tab-pane fade" id="memberships" role="tabpanel" aria-labelledby="memberships-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover zero-configuration">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pricelist</th>
                        @if (config('domains')[config('app.url')]['sports_option'] == true)
                            <th>{{ trans('global.sport') }}</th>
                        @endif
                        <th>{{ trans('cruds.membership.fields.start_date') }}</th>
                        <th>{{ trans('cruds.membership.fields.end_date') }}</th>
                        <th>{{ trans('cruds.membership.fields.trainer') }}</th>
                        <th>{{ trans('global.notes') }}</th>
                        <th>{{ trans('cruds.status.title') }}</th>
                        <th>Coach Assigned</th>
                        <th>Assign Date</th>
                        <th>{{ trans('cruds.membership.fields.sales_by') }}</th>
                        <th>{{ trans('cruds.membership.fields.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->memberships as $membership)
                        <tr
                            class="{{ $membership->service_pricelist->service->service_type->main_service == true ? 'table-info' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.memberships.show', $membership->id) }}"
                                    class="text-decoration-none">
                                    <span class="font-weight-bold">
                                        {{ $membership->service_pricelist->name ?? '-' }}
                                    </span>
                                    <span class="font-weight-bold d-block">
                                        fees :
                                        {{ number_format($membership->service_pricelist->amount) ?? '-' }}
                                    </span>
                                    @if ($membership->service_pricelist->service->service_type->main_service == 1)
                                        <span class="font-weight-bold d-block py-2">
                                            <div class="badge badge-info">
                                                Main Service
                                            </div>
                                        </span>
                                    @endif

                                    <span class="font-weight-bold d-block">
                                        {{ trans('cruds.serviceType.title_singular') }} : {{ $membership->service_pricelist->service->service_type->name ?? '-' }}
                                    </span>
                                    
                                    <span class="font-weight-bold d-block">
                                        @if ($membership->service_pricelist->service->service_type->session_type == 'non_sessions')
                                        @else
                                            {{ $membership->attendances->count() }} /
                                            {{ $membership->service_pricelist->session_count }}
                                            ({{ $membership->service_pricelist->session_count - $membership->attendances->count() }}
                                            Sessions Left)
                                        @endif
                                    </span>
                                    <span
                                        class="badge badge-{{ \App\Models\Membership::MEMBERSHIP_STATUS_COLOR[$membership->membership_status] }}">
                                        {{ \App\Models\Membership::MEMBERSHIP_STATUS[$membership->membership_status] }}
                                    </span>
                                </a>
                            </td>
                            @if (config('domains')[config('app.url')]['sports_option'] == true)
                                <td>
                                    {{ $membership->sport->name ?? '-' }}
                                </td>
                            @endif
                            <td>{{ $membership->start_date }}</td>
                            <td>{{ $membership->end_date }}</td>
                            <td>{{ $membership->trainer->name ?? '-' }}</td>
                            <td>{{ $membership->notes ?? '-' }}</td>

                            <td>
                                <span
                                    class="badge badge-{{ \App\Models\Membership::STATUS[$membership->status] }} p-2">
                                    <i class="fa fa-recycle"></i> {{ $membership->status }}
                                </span>
                            </td>
                            <td>{{ $membership->assigned_coach->name ?? '-' }}</td>
                            <td>{{ $membership->assign_date ?? '-' }}</td>
                            <td>{{ $membership->sales_by->name ?? '-' }}</td>
                            <td>{{ $membership->created_at->toFormattedDateString() . ' , ' . $membership->created_at->format('g:i A') }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button"
                                        id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('global.action') }}
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        @can('membership_show')
                                            <a href="{{ route('admin.memberships.show', $membership->id) }}"
                                                class="dropdown-item">
                                                <i class="fa fa-eye"> </i> &nbsp;
                                                {{ trans('global.view') }}
                                            </a>
                                        @endcan
                                        @can('take_attendance')
                                            <a href="{{ route('admin.memberships.manual_attend', $membership->id) }}"
                                                class="dropdown-item">
                                                <i class="fas fa-fingerprint"> </i> &nbsp;
                                                {{ trans('global.manual_attend') }}
                                            </a>
                                        @endcan


                                        @can('membership_edit')
                                            <a href="{{ route('admin.memberships.edit', $membership->id) }}"
                                                class="dropdown-item">
                                                <i class="fa fa-edit"></i> &nbsp; Edit
                                            </a>
                                        @endcan

                                        @if ($membership->service_pricelist->service->service_type->session_type == 'non_sessions')
                                            <a href="javascript:;" data-toggle="modal" data-target="#assignTrainerNonPt"
                                                class="dropdown-item"
                                                onclick="assignTrainerNonPt({{ $membership->id }})">
                                                <i class="fa fa-exchange"></i> Assign Trainer
                                            </a>
                                        @else
                                            <a href="javascript:;" data-toggle="modal" data-target="#assignTrainer"
                                                class="dropdown-item" onclick="assignTrainer({{ $membership->id }})">
                                                <i class="fa fa-exchange"></i> Assign Trainer
                                            </a>
                                        @endif
                                        {{-- @can('membership_delete')
                                        <a href="{{ route('admin.memberships.destroy', $membership->id) }}" class="dropdown-item">
                                            <i class="fa fa-times"></i> &nbsp; Delete
                                        </a>
                                        @endcan --}}

                                        @if ($membership->status == 'current' || $membership->status == 'expiring')
                                            @can('freeze_request_create')
                                                <a href="{{ route('admin.membership.freezeRequests', $membership->id) }}"
                                                    class="dropdown-item"><i class="fa fa-minus-circle"></i>
                                                    &nbsp; {{ trans('cruds.freezeRequest.title') }}
                                                </a>
                                            @endcan
                                        @endif



                                        @if ($membership->status !== 'refunded')
                                            @if ($membership->status == 'expired')
                                                @can('renew_membership')
                                                    <a href="{{ route('admin.membership.renew', $membership->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-plus-circle"></i> &nbsp;
                                                        {{ trans('cruds.membership.fields.renew') }}
                                                    </a>
                                                @endcan
                                            @endif

                                            @if ($membership->status == 'current' || $membership->status == 'expiring')
                                                @can('upgrade_membership')
                                                    <a href="{{ route('admin.membership.upgrade', $membership->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-arrow-up"></i> &nbsp;
                                                        {{ trans('cruds.membership.fields.upgrade') }}
                                                    </a>
                                                @endcan
                                                @can('downgrade_membership')
                                                    <a href="{{ route('admin.membership.downgrade', $membership->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-arrow-down"></i>
                                                        &nbsp;
                                                        {{ trans('cruds.membership.fields.downgrade') }}
                                                    </a>
                                                @endcan
                                            @endif

                                            @can('transfer_membership')
                                                <a href="{{ route('admin.membership.transfer', $membership->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fa fa-exchange"></i>&nbsp;
                                                    {{ trans('global.transfer_to_member') }}
                                                </a>
                                            @endcan
                                        @endif

                                        @can('refund_create')
                                            @if ($membership->invoice && $membership->invoice->status !== 'refund')
                                                <a href="{{ route('admin.invoice.refund', $membership->invoice->id) }}"
                                                    class="dropdown-item"> <i class="fas fa-recycle"></i>&nbsp;
                                                    &nbsp; {{ trans('cruds.refund.title') }}</a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="10" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<div id="assignTrainer" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Trainer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="pt">
                    <div class="form-group">
                        <label for="to_trainer_id">To Trainer</label>
                        <select class="form-control" name="to_trainer_id">
                            <option value="{{ null }}">Trainer</option>
                            @foreach ($trainers as $trainer_id => $trainer_name)
                                <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-times-circle"></i> Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle"></i>
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="assignTrainerNonPt" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Trainer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="type" value="non_pt">
                        <label for="to_trainer_id">To Trainer</label>
                        <select class="form-control" name="to_trainer_id_non_pt">
                            <option value="{{ null }}">Trainer</option>
                            @foreach ($trainers as $trainer_id => $trainer_name)
                                <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-times-circle"></i> Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle"></i>
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function assignTrainer(id) {
        var id = id;
        var url = "{{ route('admin.assign-coach-to-membership.memberships', ':id') }}",
            url = url.replace(':id', id);
        $.ajax({
            method: 'GET',
            url: url,
            success: function(response) {
                var route = "{{ route('admin.assign-trainer', ':membership_id') }}";

                route = route.replace(':membership_id', response.id);

                //  alert(route);
                $('form').attr('action', route)
            }
        });
    }

    function assignTrainerNonPt(id) {
        var id = id;
        var url = "{{ route('admin.assign-coach-to-membership.memberships', ':id') }}",
            url = url.replace(':id', id);
        $.ajax({
            method: 'GET',
            url: url,
            success: function(response) {
                var route = "{{ route('admin.assign-trainer', ':membership_id') }}";

                route = route.replace(':membership_id', response.id);

                //  alert(route);
                $('form').attr('action', route)
            }
        });
    }
</script>
