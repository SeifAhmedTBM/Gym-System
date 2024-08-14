@extends('layouts.admin')
@section('content')
    @can('task_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.tasks.create') }}">
                    {{ trans('global.add') }} Task
                </a>


            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>My Created Tasks {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Source">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.source.fields.id') }}
                        </th>
                        <th>
                            Title
                        </th>
                        <th>
                            Description
                        </th>
                        <th>
                            Created By
                        </th>
                        <th>
                            To User
                        </th>
                        <th>
                            To Role
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Task Date
                        </th>
                        <th>
                            Done At
                        </th>

                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)


            let dtOverrideGlobals = {
                buttons: [dtButtons],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching: true,
                aaSorting: [],
                ajax: "{{ route('admin.tasks.created-tasks') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'to_user',
                        name: 'to_user'
                    },
                    {
                        data: 'to_role',
                        name: 'to_role'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'task_date',
                        name: 'task_date'
                    },
                    {
                        data: 'done_at',
                        name: 'done_at'
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
            let table = $('.datatable-Source').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
