<div class="tab-pane fade" id="trainer_reminders" role="tabpanel" aria-labelledby="reminder-tab">
    <h4 class="py-2">Trainer Reminders</h4>
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
                    @forelse ($member->trainer_reminders as $trainer_reminder)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ \App\Models\Reminder::TYPE[$trainer_reminder->type] ?? '' }}
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ $trainer_reminder->membership->service_pricelist->name ?? '-' }}
                                </span>
                                @if ($trainer_reminder->type == 'due_payment')
                                    <span class="d-block">
                                        {{ trans('global.total') }} :
                                        {{ $trainer_reminder->membership->invoice->net_amount ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('invoices::invoice.paid') }} :
                                        {{ $trainer_reminder->membership->invoice->payments->sum('amount') ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('global.rest') }} :
                                        {{ $trainer_reminder->membership->invoice->rest ?? 0 }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $trainer_reminder->due_date ?? '' }}</td>
                            <td>{{ $trainer_reminder->user->name ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    @can('reminder_create')
                                        <button type="button" data-toggle="modal" data-target="#takeMemberAction"
                                            onclick="takeMemberAction({{ $trainer_reminder->id }})" class="btn btn-dark btn-sm"><i
                                                class="fa fa-phone"></i>
                                            &nbsp; {{ trans('cruds.reminder.fields.action') }}
                                        </button>
                                    @endcan

                                    @can('assign_reminder')
                                        <button type="button" data-toggle="modal" data-target="#assignReminder"
                                            class="btn btn-sm btn-success shadow-none text-white"
                                            onclick="assignReminder({{ $trainer_reminder->id . ',' . $trainer_reminder->user_id }})"><i
                                                class="fa fa-user"></i>
                                            Assign
                                        </button>
                                    @endcan

                                    @can('reminder_delete')
                                        <form action="{{ route('admin.reminders.destroy', $trainer_reminder->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
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
                    @forelse ($member->trainer_reminder_histories as $trainer_reminder_history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ \App\Models\LeadRemindersHistory::TYPE[$trainer_reminder_history->type] ?? '' }}
                            </td>
                            <td>
                                {{ \App\Models\LeadRemindersHistory::ACTION[$trainer_reminder_history->action] ?? '' }}
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ $trainer_reminder_history->membership->service_pricelist->name ?? '-' }}
                                </span>
                                @if ($trainer_reminder_history->type == 'due_payment')
                                    <span class="d-block">
                                        {{ trans('global.total') }} :
                                        {{ $trainer_reminder_history->membership->invoice->net_amount ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('invoices::invoice.paid') }} :
                                        {{ $trainer_reminder_history->membership->invoice->payments->sum('amount') ?? 0 }}
                                    </span>
                                    <span class="d-block">
                                        {{ trans('global.rest') }} :
                                        {{ $trainer_reminder_history->membership->invoice->rest ?? 0 }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $trainer_reminder_history->due_date ?? '' }}</td>
                            <td>{{ $trainer_reminder_history->notes }}</td>
                            <td>{{ $trainer_reminder_history->user->name ?? '-' }}</td>
                            <td>{{ $trainer_reminder_history->created_at }}</td>
                            <td>
                                @can('reminder_delete')
                                    <form action="{{ route('admin.reminderHistory.destroy', $trainer_reminder_history->id) }}"
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
