@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-8">
            <form action="{{ route('admin.members.onhold') }}" method="get">
                <label for="date">{{ trans('cruds.branch.title_singular') }}</label>
                <div class="input-group">
                    <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>All Branches</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="sport_id" id="sport_id" class="form-control">
                        <option value="{{ NULL }}">Select Sport</option>
                        @foreach (\App\Models\Sport::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ request('sport_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-2">
            @can('export_inactive_members')
                <label for="">{{ trans('global.export_excel') }}</label>
                <a href="{{ route('admin.onhold.export',request()->all()) }}" class="btn btn-info"><i class="fa fa-download"></i> {{ trans('global.export_excel') }}</a>
            @endcan
        </div>
        
        <div class="col-md-2">
            <h4 class="text-center">{{ $memberships->count() }}</h4>
            <h4 class="text-center">Onhold Members</h4>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-times"></i> On Hold Members
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('cruds.member.title_singular') }}</th>
                                    <th>{{ trans('cruds.branch.title_singular') }}</th>
                                    <th>{{ trans('cruds.service.title_singular') }}</th>
                                    <th>Sport</th>
                                    <th>{{ trans('global.start_date') }}</th>
                                    <th>{{ trans('global.end_date') }}</th>
                                    <th>{{ trans('global.amount') }}</th>
                                    <th>{{ trans('global.last_attendance') }}</th>
                                    <th>{{ trans('cruds.lead.fields.sales_by') }}</th>
                                    <th>{{ trans('global.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($memberships as $membership)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.show',$membership->member_id) }}" target="_blank">
                                                {{ $membership->member->name ?? '-' }}
                                            </a>
                                            <span class="d-block font-weight-bold">
                                                {{ $membership->member->member_code ?? '-' }}
                                                {{-- {{ \App\Models\Setting::first()->member_prefix.$membership->member->member_code ?? '-' }} --}}
                                            </span>
                                            <span class="d-block font-weight-bold">
                                                {{ $membership->member->phone ?? '-' }}
                                            </span>
                                            
                                        </td>
                                        <td>{{ $membership->member->branch->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.memberships.show',$membership->id) }}" target="_blank">
                                                {{ $membership->service_pricelist->name ?? '-' }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $membership->member->sport->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $membership->start_date ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $membership->end_date ?? '-' }}
                                        </td>
                                        <td>
                                            <a href="{{route('admin.invoices.show',$membership->invoice->id)}}">
                                                <span class="d-block">
                                                    {{ trans('global.total') }} : {{ number_format($membership->invoice->net_amount) ?? 0 }}
                                                </span>
                                                <span class="d-block">
                                                    {{ trans('invoices::invoice.paid') }} : {{ number_format($membership->invoice->payments_sum_amount) ?? 0 }}
                                                </span>
                                                <span class="d-block">
                                                    {{ trans('global.rest') }} : {{ number_format($membership->invoice->rest) ?? 0 }}
                                                </span>
                                            </a>
                                        </td>
                                        <td>
                                            {{-- {{ $membership->lastAttend()->created_at }} --}}
                                            {!! $membership->last_attendance ?? '<span class="badge badge-danger">No attendance</span>' !!}
                                        </td>
                                        <td>
                                            {{ $membership->sales_by->name ?? '-' }}
                                        </td>
                                        <td>
                                            <button type="button" data-toggle="modal" data-target="#takeMemberAction"
                                                onclick="takeMemberAction({{ $membership->member_id }})" class="btn btn-info btn-xs"><i class="fa fa-phone"></i>
                                                &nbsp; {{ trans('cruds.reminder.fields.action') }}</button>
                                        </td>
                                    </tr>
                                @empty
                                    <td colspan="5" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

     {{-- members actions modal --}}
     <div class="modal fade" id="takeMemberAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.reminder.fields.action') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="modalForm2">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">{{ trans('cruds.status.title_singular') }}</label>
                            <select name="status_id" id="status_id" class="form-control">
                                <option>{{ trans('global.pleaseSelect') }}</option>
                                @foreach ($statuses as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info font-weight-bold">
                            <i class="fa fa-exclamation-circle"></i> {{ trans('global.if_empty_due_date') }} .
                        </div>
                        <div class="form-group">
                            <label for="due_date">{{ trans('global.due_date') }}</label>
                            <input type="date" class="form-control" name="due_date" id="due_date">
                        </div>

                        <div class="form-group">
                            <label for="notes">{{ trans('cruds.lead.fields.notes') }}</label>
                            <textarea name="notes" id="notes" rows="7" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function takeMemberAction(id) {
            var id = id;
            var url = "{{ route('admin.reminders.takeMemberAction', ':id') }}";
            url = url.replace(':id', id);
            $(".modalForm2").attr('action', url);
        }

        function formSubmit() {
            $('modalForm2').submit();
        }
    </script>
@endsection