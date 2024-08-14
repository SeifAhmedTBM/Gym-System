@extends('layouts.admin')
@section('content')
    @can('timeslot_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.timeslots.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.timeslot.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.timeslot.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Timeslot">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.timeslot.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.timeslot.fields.from') }}
                            </th>
                            <th>
                                {{ trans('cruds.timeslot.fields.to') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timeslots as $key => $timeslot)
                            <tr data-entry-id="{{ $timeslot->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $timeslot->id ?? '' }}
                                </td>
                                <td>
                                    {{ date('g:i A', strtotime($timeslot->from)) ?? '' }}
                                </td>
                                <td>
                                    {{ date('g:i A', strtotime($timeslot->to)) ?? '' }}
                                </td>
                                <td>
                                    @can('timeslot_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.timeslots.show', $timeslot->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('timeslot_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.timeslots.edit', $timeslot->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('timeslot_delete')
                                        <form action="{{ route('admin.timeslots.destroy', $timeslot->id) }}" method="POST"
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
            @can('timeslot_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.timeslots.massDestroy') }}",
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
            let table = $('.datatable-Timeslot:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
