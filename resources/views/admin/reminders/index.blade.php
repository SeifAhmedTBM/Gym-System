@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-6">
            @include('admin_includes.filters', [
                'columns' => [
                    'name'          => ['label' => 'Name', 'type' => 'text', 'related_to' => 'lead'],
                    'phone'         => ['label' => 'Member Phone', 'type' => 'number', 'related_to' => 'lead'],
                    'type'          => ['label' => 'Type', 'type' => 'select', 'data' => \App\Models\Reminder::TYPE],
                    'user_id'       => ['label' => 'User', 'type' => 'select', 'data' => $sales],
                    'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true],
                ],
                    'route' => 'admin.reminders.index'
                ])

            <a href="{{ route('admin.reminders.export',request()->all()) }}" class="btn btn-info">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </a>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.reminder.fields.today_reminders') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-hover zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            {{ trans('cruds.lead.title_singular') }}
                                        </th>
                                        <th>
                                            {{ trans('global.type') }}
                                        </th>
                                        <th>
                                            {{ trans('global.details') }}
                                        </th>
                                        <th>
                                            {{ trans('global.due_date') }}
                                        </th>
                                        <th>{{ trans('global.notes') }}</th>
                                        <th>{{ trans('cruds.reminder.fields.user') }}</th>
                                        <th>{{ trans('global.action_date') }}</th>
                                        <th>{{ trans('global.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reminders as $reminder)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($reminder->lead->type == 'member')
                                                    <a href="{{ route('admin.members.show',$reminder->lead_id) }}" target="_blank" class="text-decoration-none">
                                                        {{ \App\Models\Setting::first()->member_prefix.$reminder->lead->member_code ?? '-' }}
                                                        <span class="d-block">
                                                            {{ $reminder->lead->name }}
                                                        </span>
                                                        <span class="d-block">
                                                            {{ $reminder->lead->phone }}
                                                        </span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.leads.show',$reminder->lead_id) }}" target="_blank" class="text-decoration-none">
                                                        <span class="d-block">
                                                            {{ $reminder->lead->name }}
                                                        </span>
                                                        <span class="d-block">
                                                            {{ $reminder->lead->phone }}
                                                        </span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \App\Models\Reminder::TYPE[$reminder->type] ?? '' }}
                                            </td>
                                            <td>
                                                <span class="d-block">
                                                    {{ $reminder->membership->service_pricelist->name ?? '-' }}
                                                </span>
                                                @if ($reminder->type == 'due_payment')
                                                    <span class="d-block">
                                                        {{ trans('global.total') }} : {{ $reminder->membership->invoice->net_amount ?? 0 }}
                                                    </span>
                                                    <span class="d-block">
                                                        {{ trans('invoices::invoice.paid') }} : {{ $reminder->membership->invoice->payments->sum('amount') ?? 0 }}
                                                    </span>
                                                    <span class="d-block">
                                                        {{ trans('global.rest') }} : {{ $reminder->membership->invoice->rest ?? 0 }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="d-block">
                                                    {{ $reminder->due_date ?? '' }} 
                                                </span>
                                                <span class="d-block">
                                                    {{ App\Models\Reminder::ACTION[$reminder->action] ?? '-' }} 
                                                </span>
                                            </td>
                                            <td>{{ $reminder->notes }}</td>
                                            <td>{{ $reminder->user->name ?? '-' }}</td>
                                            <td>{{ $reminder->created_at }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    @can('reminder_create')
                                                        <button type="button" data-toggle="modal" data-target="#takeLeadAction" class="btn btn-sm btn-secondary shadow-none text-white" onclick="takeLeadAction({{$reminder->id}})"><i class="fa fa-phone"></i>
                                                            {{ trans('cruds.reminder.fields.action') }}
                                                        </button>
                                                    @endcan
                                                    @can('assign_reminder')
                                                        <button type="button" data-toggle="modal" data-target="#assignReminder" class="btn btn-sm btn-success shadow-none text-white" onclick="assignReminder({{ $reminder->id.','.$reminder->user_id }})"><i class="fa fa-user"></i>
                                                            Assign
                                                        </button>
                                                    @endcan
                                                    @can('reminder_delete')
                                                        <form action="{{ route('admin.reminderHistory.destroy',$reminder->id) }}" method="post" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm" type="submit">
                                                                <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <td colspan="9" class="text-center">No data Available</td>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- leads actions modal --}}
    <div class="modal fade" id="takeLeadAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.reminder.fields.action') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="modalForm">
                    @csrf
                    <div class="modal-body">
                        @livewire('reminder-actions')
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

    {{-- assign reminder  --}}
    <div class="modal fade" id="assignReminder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Reminder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="assignForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="user_id" class="form-control">
                                <option value="{{ NULL }}">Please Select</option>
                                @foreach (App\Models\User::whereRelation('roles','title','Sales')->pluck('name','id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
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
    {{-- assign reminder  --}}
@endsection
@section('scripts')
    @parent
    <script>
        function takeLeadAction(id) {
            var id = id;
            var url = '{{ route('admin.reminders.takeLeadAction', ':id') }}';
            url = url.replace(':id', id);
            $(".modalForm").attr('action', url);
        }

        function formSubmit() {
            $('.modalForm').submit();
        }

        function assignReminder(id,user_id) {
            var id = id;
            var user_id = user_id;
            var url = '{{ route('admin.reminder.assign', ':id') }}';
            url = url.replace(':id', id);
            $('select[name="user_id"]').val(user_id);
            $(".assignForm").attr('action', url);
        }

        function formSubmit() {
            $('.assignForm').submit();
        }
    </script>
@endsection
