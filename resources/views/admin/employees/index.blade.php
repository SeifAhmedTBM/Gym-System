@extends('layouts.admin')
@section('content')
    
        <div class="row form-group">
            <div class="col-lg-9">
                @can('employee_create')
                    <a class="btn btn-success" href="{{ route('admin.employees.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.employee.title_singular') }}
                    </a>
                @endcan

                @can('employee_filter')

                    @include('admin_includes.filters', [
                    'columns' => [
                        'name'          => ['label' => 'Name', 'type' => 'text'],
                        'phone'         => ['label' => 'Phone', 'type' => 'text'],
                        'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches],
                        'job_status'    => ['label' => 'Job Status', 'type' => 'select' , 'data' => \App\Models\Employee::JOB_STATUS_SELECT],
                        'status'        => ['label' => 'Status', 'type' => 'select' , 'data' => \App\Models\Employee::STATUS_SELECT],
                        'mobile_visibility'        => ['label' => 'Mobile Status', 'type' => 'select' , 'data' => \App\Models\Employee::MOBILE_STATUS_SELECT],
                        'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                        'route' => 'admin.employees.index'
                    ])
                    @include('csvImport.modal', ['model' => 'Employee', 'route' => 'admin.employees.parseCsvImport'])
                @endcan
                
            </div>
            @can('employee_counter')
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.employee.title_singular') }}</h2>
                            <h2 class="text-center">{{ $employees->count() }}</h2>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.employee.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Employee">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.order') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.photo') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.email') }}
                        </th>
                        <th>
                            {{ trans('global.phone') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.national') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.job_status') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.start_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.role.title_singular') }}
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
            @can('employee_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.employees.massDestroy') }}",
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
            @endcan

            let dtOverrideGlobals = {
                buttons:[],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.employees.index',request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        'searchable': false
                    },
                    {
                        data: 'employee_name',
                        name: 'user.name'
                    },
                    {
                        data: 'user_email',
                        name: 'user.email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone'
                    },
                    {
                        data: 'national',
                        name: 'national'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'job_status',
                        name: 'job_status'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'role',
                        name: 'role'
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
            let table = $('.datatable-Employee').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
