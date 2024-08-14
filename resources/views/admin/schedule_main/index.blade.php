@extends('layouts.admin')
@section('content')
    @can('schedule_main_create')
        <div class="row form-group">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.schedule-mains.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.schedule.title_singular') }} Main
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.schedule.title_singular') }} Main {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Schedule-main">
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
                                Days
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
                                {{ trans('cruds.schedule.fields.comission_type') }}
                            </th>
                            <th>
                                {{ trans('cruds.schedule.fields.comission_amount') }}
                            </th>
                            <th>
                                Schedule Group
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Branch
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedule_mains as $key => $schedule_main)
                            <tr data-entry-id="{{ $schedule_main->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $schedule_main->id ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule_main->session->name ?? '' }}
                                </td>
                                <td>
                                    @foreach ($schedule_main->schedules as $schedule)
                                        <span class="badge badge-success">{{ $schedule->day }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $schedule_main->date }}
                                </td>
                                <td>
                                    {{ date('g:i A', strtotime($schedule_main->timeslot->from)) ?? '' }}
                                </td>
                                <td>
                                    {{ date('g:i A', strtotime($schedule_main->timeslot->to)) ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule_main->trainer->name ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule_main->commission_type ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule_main->commission_amount ?? '' }}
                                    @if ($schedule_main->commission_type == 'percentage')
                                        %
                                    @else
                                        LE
                                    @endif
                                </td>
                                <td>{{ $schedule_main->schedule_main_group->name ?? '-' }}</td>
                                <td>{{ $schedule_main->status }}</td>
                                <td>{{ $schedule_main->branch->name ?? '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        @can('schedule_main_show')
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('admin.schedule-mains.show', $schedule_main->id) }}">
                                                {{ trans('global.view') }}
                                            </a>
                                        @endcan

                                        @can('schedule_main_edit')
                                            @if ($schedule_main->status == 'active')
                                                <form action="{{ route('admin.schedule-mains.change-status', $schedule_main->id) }}" method="POST"
                                                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-danger btn-sm text-white">
                                                        <i class="fa fa-times"></i> Inactive
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.schedule-mains.change-status', $schedule_main->id) }}" method="POST"
                                                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                       <i class="fa fa-check"></i> Active
                                                    </button>
                                                </form>
                                            @endif

                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('admin.schedule-mains.edit', $schedule_main->id) }}">
                                                {{ trans('global.edit') }}
                                            </a>
                                        @endcan

                                        @can('schedule_main_delete')
                                            <form action="{{ route('admin.schedule-mains.destroy', $schedule_main->id) }}"
                                                method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="submit" class="btn btn-sm btn-danger"
                                                    value="{{ trans('global.delete') }}">
                                            </form>
                                        @endcan
                                    </div>
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
            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Schedule-main:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
