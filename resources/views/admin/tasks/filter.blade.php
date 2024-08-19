@extends('layouts.admin')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css" />
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        padding:0px !important;
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
        <form action="{{route('admin.filter-tasks')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group col-lg-4" style="padding:50px;">
                    <div class="row">
                    <label for="" >{{ trans('global.employee')}}</label>
                    <div class="col-lg-10">
                        <select name="employee_id" class="form-control" id="">
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
            <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-Task" id="myTable">
                <thead>
                    <tr>
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
                <tbody>
                    
                    @foreach($tasks as $task)
                    <tr>
                        <td>{{$task->id}}</td>
                        <td>{{$task->title}}</td>
                        <td>{{$task->description}}</td>
                        <td>{{$task->created_by?->name}}</td>
                        <td>{{$task->to_user?->name}}</td>
                        <td>{{$task->to_role?->name}}</td>
                        <td> <span class="badge">{{ $task->status }}</span></td>
                        <td>{{$task->created_at->toFormattedDateString() , $task->created_at->format('g:i A')}}</td>
                        <td>{{$task->supervisor?->name}}</td>
                        <td>{{$task->done_at}}</td>
                        <td>{{$task->confirmation_at}}</td>
                        <td>

                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                Action
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="/admin/tasks/{{$task->id}}" target="__blank">
                                    <i class="fa fa-eye"></i> &nbsp; View
                                </a>
                                <a class="dropdown-item" href="/admin/tasks/{{$task->id}}/edit" target="__blank">
                                    <i class="fa fa-edit"></i> &nbsp; Edit
                                </a>                
                                <form action="/admin/tasks/{{$task->id}}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="Qlfpnuhvr1qwGs37Vy5YHIDYK3MHLelVOW1oTKu4">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa fa-trash"></i> &nbsp; Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
         
        </div>
    </div>
    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready( function () {
    $('#myTable').DataTable();
} );
</script>
@endsection

