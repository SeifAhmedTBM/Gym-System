@extends('layouts.admin')
@section('content')
    @can('schedule_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.schedules.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.schedule.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.modal', [
                    'model' => 'Schedule',
                    'route' => 'admin.schedules.parseCsvImport',
                ])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.schedule.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Schedule">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.session') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.day') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.date') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.timeslot') }}
                                {{ trans('cruds.timeslot.fields.from') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.timeslot') }} {{ trans('cruds.timeslot.fields.to') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.trainer') }}
                            </th>
                            <th>
                                Branch
                            </th>
                            <!-- <th>
                                {{ trans('cruds.schedule.fields.comission_type') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.comission_amount') }}
                            </th> -->
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $key => $schedule)
                            <tr data-entry-id="{{ $schedule->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $schedule->id ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule->session->name ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\Schedule::DAY_SELECT[$schedule->day] ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule->date ?? '' }}
                                </td>
                                <td>
                                    {{ date('g:i A', strtotime($schedule->timeslot->from)) ?? '' }}
                                </td>
                                <td>
                                    {{ date('g:i A', strtotime($schedule->timeslot->to)) ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule->trainer->name ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule->branch->name ?? 'N/A' }}
                                </td>
                                <!-- <td>
                                    {{ $schedule->comission_type ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule->comission_amount ?? '' }}
                                    @if($schedule->comission_type == 'percentage')
                                        %
                                    @else
                                        LE
                                    @endif
                                </td> -->
                                <td>
                                    @can('schedule_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.schedules.show', $schedule->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('schedule_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.schedules.edit', $schedule->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('schedule_delete')
                                        <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger"
                                                value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
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
            @can('schedule_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.schedules.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
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
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Schedule:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
