@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-6">
            <form action="{{ route('admin.membership-schedule.attendances',$schedule->id) }}" method="get">
                <label for="">Date</label>
                <div class="input-group">
                    <input type="date" name="from" id="from" class="form-control" value="{{ request('from') ?? date('Y-m-01') }}">
                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') ?? date('Y-m-t') }}">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {{ trans('global.search') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="form-group">
        <div class="card">
            <div class="card-header">
               Attendance List
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover datatable datatable-MembershipAttendance">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Member</th>
                            <th>Membership</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Sessions</th>
                            <th>Status</th>
                            <th>Attendance Date</th>
                            <th>
                                {{ trans('global.actions') }}
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@parent
<script>
    $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.trainer-attendances.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                return entry.id
                });
            
                if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
            
                return
                }
            
                if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                headers: {'x-csrf-token': _token},
                method: 'POST',
                url: config.url,
                data: { ids: ids, _method: 'DELETE' }})
                .done(function () { location.reload() })
                }
                }
                }
                dtButtons.push(deleteButton)
            

            let dtOverrideGlobals = {
                buttons:dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{{ route('admin.membership-schedule.attendances',[$schedule->id,'from' => request('from'),'to' => request('to')]) }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'member_name',
                        name: 'member_name'
                    },
                    {
                        data: 'membership_name',
                        name: 'membership.service_pricelist.name'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'sessions',
                        name: 'sessions'
                    },
                    {
                        data: 'membership_status',
                        name: 'membership_status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 50,
            };
            let table = $('.datatable-MembershipAttendance').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
</script>
@endsection
