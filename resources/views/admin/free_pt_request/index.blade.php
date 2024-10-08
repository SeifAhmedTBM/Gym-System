@extends('layouts.admin')
@section('content')
        <div class="form-group row">
            <div class="col-lg-9">
                @can('membership_create')
                    <a class="btn btn-success" href="{{ route('admin.memberships.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.membership.title_singular') }}
                    </a>
                @endcan

               
                @can('export_memberships')
                    <a href="{{ route('admin.memberships.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                    </a>
                @endcan

                {{-- <button type="button" data-toggle="modal" data-target="#sendReminder" class="btn btn-dark"><i
                        class="fa fa-plus-circle"></i> Reminder</button> --}}
            </div>
            @can('membership_counter')
                {{-- <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.membership.title') }}</h2>
                            <h2 class="text-center">{{ $memberships_count }}</h2>
                        </div>
                    </div>
                </div> --}}
            @endcan
        </div>
    
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.membership.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-Membership">
                <thead>
                    <tr>
                     
                        <th>
                            {{ trans('cruds.membership.fields.id') }}
                        </th>
              
                        <th>
                            {{ trans('cruds.membership.fields.member') }}
                        </th>
                   
                        <th>
                            {{ trans('global.gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.start_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.end_date') }}
                        </th>
                        <th>
                           Assigned To Coach
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.service') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                    
                        <th>
                            {{ trans('global.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.sales_by') }}
                        </th>
                       
                        <th>
                            {{ trans('global.last_attendance') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($free_pt_requests as $request)
                    <tr>
                        <td>{{$request->id}}</td>
                        <td>{{$request->membership->member->name}}</td>
                        <td>{{$request->membership->member->gender}}</td>
                        <td>{{$request->membership->start_date}}</td>
                        <td>{{$request->membership->end_date}}</td>
                        <td>{{$request->membership?->assigned_coach?->name}}</td>
                        <td>{{$request->membership->service_pricelist->name}}</td>
                        <td>{{$request->membership->member->branch->name}}</td>
                        <td>{{$request->membership->status}}</td>
                        <td>{{$request->membership->sales_by->name}}</td>
                        <td>{{$request->membership->last_attendance}}</td>
                        <td>{{$request->membership->created_at}}</td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                    Action
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a href="javascript:;" data-toggle="modal" data-target="#exampleModal{{$request->membership->id}}" class="dropdown-item">
                                        <i class="fa fa-exchange"></i> Assign Trainer
                                    </a>
                                    
                                </div>
                                <div class="modal fade" id="exampleModal{{$request->membership->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Assign Coach To {{$request->membership->member->name}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{route('admin.assign_free_pt_coache')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$request->id}}">
                                                <input type="hidden" name="membership_id" value="{{$request->membership->id}}">
                                                <div class="form-group">
                                                    <label for="to_trainer_id">To Trainer</label>
                                                    <select class="form-control" name="assigned_coach">
                                                        <option value="{{ null }}">Trainer</option>
                                                        @foreach ($trainers as $trainer_id => $trainer_name)
                                                            <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                        
                                       
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </form>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    


@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtOverrideGlobals = {
                buttons: [],
                processing: true,
                responsive: true,
                searching:true,
                aaSorting: [],
                orderCellsTop: true,
              
                pageLength: 25,
            };
            let table = $('.datatable-Membership').DataTable(dtOverrideGlobals);
            
            
           
            
        });
        
    </script>
@endsection
