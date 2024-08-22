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
        <form method="get" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group col-lg-4" style="padding:50px;">
                    <div class="row">
                    <label for="" >{{ trans('global.employee')}}</label>
                    <div class="col-lg-10">
                        <select name="employee_id" class="form-control select2" id="">
                            <option value="" selected>{{ trans('global.select_employee')}}</option> 
                            @foreach($employees as $employee)
                            @if(isset($tasks[0]))
                            <option value="{{$employee->user_id}}" {{ $tasks[0]->to_user_id == $employee->user_id ? 'selected' : ' ' }}>{{$employee->name}}</option>
                            @else
                            <option value="{{$employee->user_id}}" >{{$employee->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary">{{ trans('global.submit')}}</button>

                    </div>
                    </div>
                    
                </div>
             
            </div>
           
        </form>
        <div class="card-header">
            <h5>Tasks{{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Task">
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
                            Supervisor
                        </th>
                        <th>
                            Done At
                        </th>
                        <th>
                            Confirmation At
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
          

            const urlParams = new URLSearchParams(window.location.search);
            const emp_id = urlParams.get('employee_id') ?? '';
            let dtOverrideGlobals = {
            buttons: [dtButtons],
            processing: true,
            serverSide: true,
            retrieve: true,
            searching: true,
            aaSorting: [],
            ajax: "{{ route('admin.tasks.index') }}?employee_id="+emp_id,
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
                        data: 'supervisor_name',
                        name: 'supervisor_name'
                    },
                    {
                        data: 'done_at',
                        name: 'done_at'
                    },
                    {
                        data: 'confirmation_at',
                        name: 'confirmation_at'
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
            let table = $('.datatable-Task').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
