@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-2">
            <a class="btn btn-danger btn-block" href="{{ route('admin.leads.index') }}">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
            </a>
        </div>

        <div class="col-md-2">
            <div class="dropdown">
                <a class="btn btn-primary btn-block dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-expanded="false">
                    {{ trans('global.action') }}
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    @can('lead_edit')
                        <a href="{{ route('admin.leads.edit', $lead->id) }}" class="dropdown-item">
                            <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                        </a>
                    @endcan

                    @can('transfer_to_member')
                        <a href="{{ route('admin.member.transfer', $lead->id) }}" class="dropdown-item">
                            <i class="fas fa-exchange-alt"></i> &nbsp; {{ trans('global.transfer_to_member') }}
                        </a>
                    @endcan

                    {{-- @can('lead_add_note') --}}
                    <a href="{{ route('admin.note.create', $lead->id) }}" class="dropdown-item">
                        <i class="fas fa-plus"></i> &nbsp; {{ trans('cruds.lead.fields.notes') }}
                    </a>
                    {{-- @endcan --}}

                    <button type="button" data-toggle="modal" data-target="#leadAction"
                        onclick="leadAction({{ $lead->id }})" class="dropdown-item"><i class="fa fa-phone"></i>
                        &nbsp; {{ trans('global.add_reminder') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5>{{ trans('global.show') }} {{ trans('cruds.lead.title_singular') }}</h5>
                </div>
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="basic_data-tab" data-toggle="pill" href="#basic_data" role="tab"
                            aria-controls="basic_data" aria-selected="true">{{ trans('global.basic_data') }}</a>

                        <a class="nav-link" id="notes-tab" data-toggle="pill" href="#notes" role="tab"
                            aria-controls="notes" aria-selected="false">
                            {{ trans('global.notes') }}
                        </a>

                        <a class="nav-link" id="reminder-tab" data-toggle="pill" href="#reminder" role="tab"
                            aria-controls="reminder" aria-selected="false">
                            {{ trans('cruds.reminder.title') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-user"></i> {{ $lead->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="basic_data" role="tabpanel"
                            aria-labelledby="basic_data-tab">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ($lead->photo)
                                                <a href="{{ $lead->photo->getUrl() }}" target="_blank"
                                                    style="display: inline-block">
                                                    <img src="{{ $lead->photo->getUrl() }}" class="rounded-circle"
                                                        style="width: 150px;height:150px">
                                                </a>
                                            @else
                                                <a href="{{ asset('images/user.png') }}" target="_blank"
                                                    style="display: inline-block">
                                                    <img src="{{ asset('images/user.png') }}" class="rounded-circle"
                                                        style="width: 150px;height:150px">
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-9">
                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.name') }} </h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->name }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.phone') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->phone }}
                                        </div>
                                    </div>

                                    @if (!is_null($lead->parent_phone))
                                        <div class="row my-2">
                                            <div class="col-md-6">
                                                <h5>Parent Phone </h5>
                                            </div>

                                            <div class="col-md-6">
                                                {{ $lead->parent_phone }}
                                            </div>
                                        </div>
                                    @endif

                                    @if (!is_null($lead->parent_phone_two))
                                        <div class="row my-2">
                                            <div class="col-md-6">
                                                <h5>Parent Phone 2</h5>
                                            </div>

                                            <div class="col-md-6">
                                                {{ $lead->parent_phone_two }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('global.whatsapp') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->whatsapp_number }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.dob') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->dob }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.national') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->national ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5> {{ trans('cruds.lead.fields.address_details') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->address_details ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.status') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->status->name ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.source') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->source->name ?? '-' }}
                                        </div>
                                    </div>



                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.gender') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ App\Models\Lead::GENDER_SELECT[$lead->gender] ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.downloaded_app') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->downloaded_app == 0 ? 'No' : 'Yes' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.sales_by') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->sales_by->name ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>Created At</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->created_at ?? '' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('global.main_notes') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->notes ?? 'No Notes' }}
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <h5>{{ trans('cruds.lead.fields.medical_background') }}</h5>
                                        </div>

                                        <div class="col-md-6">
                                            {{ $lead->medical_background ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="reminder" role="tabpanel" aria-labelledby="reminder-tab">
                            <h4 class="py-2">{{ trans('cruds.reminder.title') }}</h4>

                            <div class="row">
                                <div class="col-md-12">
                                    <table
                                        class="table table-bordered table-striped table-hover text-center zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>
                                                    {{ trans('global.type') }}
                                                </th>
                                                <th>
                                                    {{ trans('cruds.reminder.fields.next_due_date') }}
                                                </th>
                                                <th>
                                                    {{ trans('cruds.reminder.fields.user') }}
                                                </th>
                                                <th>
                                                    {{ trans('global.actions') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lead->leadReminders as $reminder)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ \App\Models\Reminder::TYPE[$reminder->type] ?? '' }}
                                                    </td>
                                                    <td>
                                                        <span class="d-block">
                                                            {{ $reminder->due_date ?? '' }}
                                                        </span>
                                                        <span
                                                            class="badge badge-{{ App\Models\Reminder::ACTION_COLOR[$reminder->action] ?? '' }}">
                                                            {{ App\Models\Reminder::ACTION[$reminder->action] ?? '' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $reminder->user->name ?? '-' }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            @can('reminder_create')
                                                                <button type="button" data-toggle="modal"
                                                                    data-target="#takeMemberAction"
                                                                    onclick="takeMemberAction({{ $reminder->id }})"
                                                                    class="btn btn-dark btn-xs"><i class="fa fa-phone"></i>
                                                                    &nbsp; {{ trans('cruds.reminder.fields.action') }}
                                                                </button>
                                                            @endcan

                                                            @can('assign_reminder')
                                                                <button type="button" data-toggle="modal"
                                                                    data-target="#assignReminder"
                                                                    class="btn btn-sm btn-success shadow-none text-white"
                                                                    onclick="assignReminder({{ $reminder->id . ',' . $reminder->user_id }})"><i
                                                                        class="fa fa-user"></i>
                                                                    Assign
                                                                </button>
                                                            @endcan

                                                            @can('reminder_delete')
                                                                <form
                                                                    action="{{ route('admin.reminders.destroy', $reminder->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                                    style="display: inline-block;">
                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                    <input type="hidden" name="_token"
                                                                        value="{{ csrf_token() }}">
                                                                    <button type="submit" class="btn btn-danger btn-xs">
                                                                        <i class="fa fa-trash"></i> &nbsp;
                                                                        {{ trans('global.delete') }}
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr>
                            <h4 class="py-2">{{ trans('global.reminders_history') }}</h4>

                            <div class="row">
                                <div class="col-md-12">
                                    <table
                                        class="table table-bordered table-striped table-hover text-center zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('global.type') }}</th>
                                                <th>{{ trans('cruds.action.title_singular') }}</th>
                                                <th>{{ trans('global.notes') }}</th>
                                                <th>{{ trans('cruds.reminder.fields.user') }}</th>
                                                <th>{{ trans('global.action_date') }}</th>
                                                <th>{{ trans('global.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lead->reminderHistory as $history)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ \App\Models\LeadRemindersHistory::TYPE[$history->type] ?? '' }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ App\Models\LeadRemindersHistory::ACTION_COLOR[$history->action] ?? '' }}">
                                                            {{ App\Models\LeadRemindersHistory::ACTION[$history->action] ?? '' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $history->notes }}</td>
                                                    <td>{{ $history->user->name ?? '-' }}</td>
                                                    <td>{{ $history->created_at }}</td>
                                                    <td>
                                                        @can('reminder_delete')
                                                            <form
                                                                action="{{ route('admin.reminderHistory.destroy', $history->id) }}"
                                                                method="post" onsubmit="return confirm('Are you sure?');"
                                                                style="display: inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger btn-xs" type="submit">
                                                                    <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                                                                </button>
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

                        <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped table-hover zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('global.notes') }}</th>
                                                <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                                                <th>{{ trans('global.created_at') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lead->Notes as $note)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $note->notes ?? '-' }}</td>
                                                    <td>{{ $note->created_by->name ?? '-' }}</td>
                                                    <td>{{ $note->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


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
                        <div class="alert alert-info font-weight-bold">
                            <i class="fa fa-exclamation-circle"></i> {{ trans('global.if_empty_due_date') }} .
                        </div>
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


    <div class="modal fade" id="leadAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.reminder.fields.action') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="modalForm3">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info font-weight-bold">
                            <i class="fa fa-exclamation-circle"></i> {{ trans('global.if_empty_due_date') }} .
                        </div>
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
                                <option value="{{ null }}">Please Select</option>
                                @foreach (App\Models\User::whereRelation('roles', 'title', 'Sales')->pluck('name', 'id') as $id => $name)
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
    <script>
        function takeMemberAction(id) {
            var id = id;
            var url = '{{ route('admin.reminders.takeLeadAction', ':id') }}';
            url = url.replace(':id', id);
            $(".modalForm2").attr('action', url);
        }

        function formSubmit() {
            $('.modalForm2').submit();
        }
    </script>

    <script>
        function leadAction(id) {
            var id = id;
            var url = "{{ route('admin.leadAction', ':id') }}";
            url = url.replace(':id', id);
            $(".modalForm3").attr('action', url);
        }

        function formSubmit() {
            $('.modalForm3').submit();
        }

        function assignReminder(id, user_id) {
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
