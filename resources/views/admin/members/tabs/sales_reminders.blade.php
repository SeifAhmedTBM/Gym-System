<div class="tab-pane fade" id="sales_reminders" role="tabpanel" aria-labelledby="sales-reminders-tab">
    <h4 class="py-2">Sales Reminders</h4>
    <div class="form-group">
        <button type="button" data-toggle="modal" data-target="#takeAction" onclick="takeAction({{ $member->id }})"
            class="btn btn-info btn-xs"><i class="fa fa-plus"></i>
            &nbsp; {{ trans('global.add_reminder') }}
        </button>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            {{ trans('global.type') }}
                        </th>
                        <th>
                            {{ trans('global.details') }}
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
                    @forelse ($member->sales_reminders as $sales_reminder)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ \App\Models\Reminder::TYPE[$sales_reminder->type] ?? '' }}
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ $sales_reminder->membership->service_pricelist->name ?? '-' }}
                                </span>
                                @if ($sales_reminder->type == 'due_payment')
                                    <span class="d-block">
                                        {{ trans('global.total') }} :
                                        {{ $sales_reminder->membership->invoice->net_amount ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('invoices::invoice.paid') }} :
                                        {{ $sales_reminder->membership->invoice->payments->sum('amount') ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('global.rest') }} :
                                        {{ $sales_reminder->membership->invoice->rest ?? 0 }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $sales_reminder->due_date ?? '' }}</td>
                            <td>{{ $sales_reminder->user->name ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    @can('reminder_create')
                                        <button type="button" data-toggle="modal" data-target="#takeMemberAction"
                                            onclick="takeMemberAction({{ $sales_reminder->id }})"
                                            class="btn btn-dark btn-sm"><i class="fa fa-phone"></i>
                                            &nbsp; {{ trans('cruds.reminder.fields.action') }}
                                        </button>
                                    @endcan

                                    @can('assign_reminder')
                                        <button type="button" data-toggle="modal" data-target="#assignReminder"
                                            class="btn btn-sm btn-success shadow-none text-white"
                                            onclick="assignReminder({{ $sales_reminder->id . ',' . $sales_reminder->user_id }})"><i
                                                class="fa fa-user"></i>
                                            Assign
                                        </button>
                                    @endcan

                                    
                                    @can('reminder_delete')
                                        <form action="{{ route('admin.reminders.destroy', $sales_reminder->id) }}"
                                            method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i> &nbsp;
                                                {{ trans('global.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="7" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <hr>
    <h4 class="py-2">{{ trans('global.reminders_history') }}</h4>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            {{ trans('global.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.action.title_singular') }}
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
                    @forelse ($member->sales_reminder_histories as $sales_reminder_history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ \App\Models\LeadRemindersHistory::TYPE[$sales_reminder_history->type] ?? '' }}
                            </td>
                            <td>
                                {{ \App\Models\LeadRemindersHistory::ACTION[$sales_reminder_history->action] ?? '' }}
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ $sales_reminder_history->membership->service_pricelist->name ?? '-' }}
                                </span>
                                @if ($sales_reminder_history->type == 'due_payment')
                                    <span class="d-block">
                                        {{ trans('global.total') }} :
                                        {{ $sales_reminder_history->membership->invoice->net_amount ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('invoices::invoice.paid') }} :
                                        {{ $sales_reminder_history->membership->invoice->payments->sum('amount') ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('global.rest') }} :
                                        {{ $sales_reminder_history->membership->invoice->rest ?? 0 }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $sales_reminder_history->due_date ?? '' }}</td>
                            <td>{{ $sales_reminder_history->notes }}</td>
                            <td>{{ $sales_reminder_history->user->name ?? '-' }}</td>
                            <td>{{ $sales_reminder_history->created_at }}</td>
                            <td>
                                @can('reminder_delete')
                                    <form
                                        action="{{ route('admin.reminderHistory.destroy', $sales_reminder_history->id) }}"
                                        method="post" onsubmit="return confirm('Are you sure?');"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <td colspan="8" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- members actions modal --}}
<div class="modal fade" id="takeAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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

<script>
    function takeAction(id) {
        var id = id;
        var url = '{{ route('admin.reminders.takeMemberAction', ':id') }}';
        url = url.replace(':id', id);
        $(".modalForm3").attr('action', url);
    }
</script>
