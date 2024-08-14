@extends('layouts.admin')
@section('content')
    <div class="row my-2">
        <div class="col-md-10">
            @include('admin_includes.filters', [
            'columns' => [
                // 'start_date' => ['label' => trans('global.start_date'), 'type' => 'date', 'from_and_to' => true, 'related_to' => 'memberships'],
                'member_code' => ['label' => 'Member Code', 'type' => 'number', 'related_to' => 'member'],
                'sales_by_id' => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales],
                'trainer_id' => ['label' => 'Trainer', 'type' => 'select', 'data' => $trainers],
                'branch_id'   => ['label' => 'Branch', 'type' => 'select', 'data' => \App\Models\Branch::pluck('name','id'),'related_to' => 'member'],
                'start_date' => ['label' => trans('global.start_date'), 'type' => 'date', 'from_and_to' => true],
                'end_date' => ['label' => trans('global.end_date'), 'type' => 'date', 'from_and_to' => true, 'related_to' => 'memberships'],
                // 'created_at' => ['label' => trans('global.created_at'), 'type' => 'date', 'from_and_to' => true]
            ],
                'route' => 'admin.membership.expiredExtra'
            ])

            @can('export_expired_memberships')
                <a href="{{ route('admin.export.expired.membershipsExtra',request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
            {{-- <button type="button" data-toggle="modal" data-target="#sendReminder" class="btn btn-dark"><i  class="fa fa-plus-circle"></i> Reminder</button> --}}
        </div>
        <div class="col-md-2">
            <h2 class="text-center">{{ $memberships->total() }}</h2>
            <h2 class="text-center">{{ trans('cruds.membership.title_singular') }}</h2>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-user-times"></i> {{ trans('global.expired_memberships') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                                <th>#</th>

                                <th>
                                    {{ trans('cruds.membership.fields.member') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.service') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.start_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.end_date') }}
                                </th>
                                <th>
                                    {{ trans('global.amount') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.trainer') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.sales_by') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.created_at') }}
                                </th>
                                <th>
                                    {{ trans('cruds.action.title_singular') }}
                                </th>
                            </thead>
                            <tbody>
                                @forelse ($memberships as $membership)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @if($membership->member)
                                            <td> <a href="{{route('admin.members.show',$membership->member->id)}}" target="_blank">{{  $setting->member_prefix.''.$membership->member->member_code }} </a> <br> <b>{{ $membership->member->name }}</b> <br> <b> {{ $membership->member->phone }}</b>  </td>
                                        @else
                                            <td> - </td>
                                        @endif
                                        <td>
                                            {{ $membership->service_pricelist && $membership->service_pricelist->service ? $membership->service_pricelist->name : '-' }}
                                            <br>
                                            <span class="badge badge-info">
                                                {{ $membership->service_pricelist->service->service_type->name  ?? ''}}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $membership->start_date  ?? ''}}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">
                                                {{ $membership->end_date  ?? ''}}
                                            </span>
                                        </td>
                                        <td>
                                           
                                               
                                            <a href="{{route('admin.invoices.show',$membership->invoice->id)}}" target="_blank" >
                                                <span class="d-block">
                                                    {{ trans('global.total') }} : {{ $membership->invoice->net_amount ?? 0 }}
                                                </span>
                                                <span class="d-block">
                                                    {{ trans('invoices::invoice.paid') }} : {{ $membership->invoice->payments->sum('amount') ?? 0 }}
                                                </span>
                                                <span class="d-block">
                                                    {{ trans('global.rest') }} : {{ $membership->invoice->rest ?? 0 }}
                                                </span>
                                            </a>
                                        </td>
                                        <td>
                                            {!! $membership->trainer->name ?? '<span class="badge badge-danger">No Trainer</span>' !!}
                                        </td>
                               
                                        <td>{{ $membership->sales_by->name ?? '-' }}</td>
                                        <td>{{ $membership->created_at->toFormattedDateString() ?? ''}}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ trans('global.action') }}
                                                </a>
                                            
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a href="{{ route('admin.membership.renew', $membership->id) }}" class="dropdown-item">
                                                        <i class="fa fa-plus-circle"></i> &nbsp; {{ trans('cruds.membership.fields.renew') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <td colspan="9" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <span class="float-right">
                {{ $memberships->appends(request()->all())->links() }}
            </span>
        </div>
    </div>

    <div class="modal fade" id="sendReminder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form action="{{ route('admin.reminder.expiredMemberships') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.reminder.fields.action').' '}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="member_status_id">{{ trans('cruds.status.title_singular') }}</label>
                            <select name="member_status_id" id="member_status_id" class="form-control" onchange="getStatus()">
                                <option >Select</option>
                                @foreach ($memberStatuses as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="due_date">{{ trans('global.due_date') }}</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" value="">
                        </div>

                        <div class="form-group">
                            <label for="member_ids">{{ trans('cruds.member.title_singular') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select name="member_ids[]" id="member_ids" class="form-control select2" multiple="">
                                @foreach ($memberships as $membership)
                                    <option value="{{ $membership->member_id }}" selected>{{ $membership->member->name ?? '-'}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">{{ trans('cruds.lead.fields.notes') }}</label>
                            <textarea name="notes" id="notes" rows="7" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> Confirm</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
@section('scripts')
    <script>
        function getStatus()
        {
            var status_id = $('#member_status_id').val();
            var url = "{{ route('admin.getMemberStatus',[':id', ':date']) }}",
            url = url.replace(':id', status_id);
            url = url.replace(':date', "{{date('Y-m-d')}}");

            $.ajax({
                method : 'GET',
                url : url,
                success:function(response)
                {
                    $('#due_date').val(response.due_date);
                }
            });
        } 
    </script>
@endsection