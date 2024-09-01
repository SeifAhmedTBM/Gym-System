@extends('layouts.admin')
<style>
    .bg-orange{
        background-color:#d5a439 !important;
        color:black !important;
    }
</style>
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
                <div class="form-group col-lg-8" style="padding:50px;">
                    <div class="row">
                    <div class="col-lg-5">
                        <select name="status" class="form-control select2" id="">
                            <option value="" selected>All Status</option> 
                            <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Overdue</option>
                            <option value="done_with_confirm"{{ request('status') == 'done_with_confirm' ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>
                   
                    <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary">{{ trans('global.submit')}}</button>

                    </div>
                    </div>
                    
                </div>
             
            </div>
           
        </form>
        <div class="row" style="padding:20px;">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card {{ request('status') === 'today' ? 'bg-orange' : '' }}">
                <a href="{{ route('admin.tasks.my-tasks', ['status' => 'today' ]) }}"> 
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center"> Today </h2>
                        <h2 class="text-center">{{$todayTasksCount}}</h2>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card {{ request('status') === 'upcoming' ? 'bg-orange' : '' }}">
                <a href="{{ route('admin.tasks.my-tasks', ['status' => 'upcoming' ]) }}"> 
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center"> Upcoming</h2>
                        <h2 class="text-center">{{$upcomingCount}}</h2>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card {{ request('status') === 'pending' ? 'bg-orange' : '' }}">
                    <a href="{{ route('admin.tasks.my-tasks', ['status' => 'pending' ]) }}">                    
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center"> Overdue </h2>
                        <h2 class="text-center">{{$pendingTasksCount}}</h2>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card {{ request('status') === 'done_with_confirm' ? 'bg-orange' : '' }}">
                <a href="{{ route('admin.tasks.my-tasks', ['status' => 'done_with_confirm']) }}">                   
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center"> Done</h2>
                        <h2 class="text-center">{{$doneTasksCount}}</h2>
                    </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-header">
            <h5>My Tasks {{ trans('global.list') }}</h5>
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
            const status = urlParams.get('status') ?? '';

            let dtOverrideGlobals = {
                buttons: [dtButtons],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching: true,
                aaSorting: [],
                ajax: "{{ route('admin.tasks.my-tasks') }}?status="+status,
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
                        name: 'supervisor.name'
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
            let table = $('.datatable-Source').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
