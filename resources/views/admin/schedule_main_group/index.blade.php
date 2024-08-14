@extends('layouts.admin')
@section('content')
    <div class="row my-2">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.schedule-main-groups.create') }}">
                {{ trans('global.add') }} Schedule Main Group
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Schedule Main Groups {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Branch">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                ID
                            </th>
                            <th>
                                Name
                            </th>
                            <th>
                                Active
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedule_main_groups as $key => $schedule_main_group)
                            <tr data-entry-id="{{ $schedule_main_group->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $schedule_main_group->id ?? '' }}
                                </td>
                                <td>
                                    {{ $schedule_main_group->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $schedule_main_group->status ?? '-' }}
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-primary"
                                        href="{{ route('admin.schedule-main-groups.show', $schedule_main_group->id) }}">
                                        {{ trans('global.view') }}
                                    </a>

                                    @can('branch_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.schedule-main-groups.edit', $schedule_main_group->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('branch_delete')
                                        <form action="{{ route('admin.schedule-main-groups.destroy', $schedule_main_group->id) }}" method="POST"
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
            @can('branch_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.branches.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
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
            let table = $('.datatable-Branch:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
