@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-6">
            @include('admin_includes.filters', [
                'columns' => [
                    'phone'         => ['label' => 'Member Phone', 'type' => 'number', 'related_to' => 'lead'],
                    'member_code'   => ['label' => 'Member Code', 'type' => 'number', 'related_to' => 'lead'],
                    'type'          => ['label' => 'Type', 'type' => 'select', 'data' => \App\Models\LeadRemindersHistory::TYPE],
                    'due_date'      => ['label' => 'Due Date', 'type' => 'date', 'from_and_to' => true],
                    'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true],
                ],
                    'route' => 'admin.remindersHistory.index'
                ])
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ trans('global.reminders_history') }}
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
                                            {{ trans('cruds.action.title_singular') }}
                                        </th>
                                        <th>
                                            {{ trans('cruds.source.title_singular') }}
                                        </th>
                                        <th>
                                            {{ trans('global.created_at') }} ( Lead )
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
                                    @forelse ($histories as $history)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($history->lead->type == 'member')
                                                    <a href="{{ route('admin.members.show',$history->lead_id) }}" target="_blank" class="text-decoration-none">
                                                        {{ \App\Models\Setting::first()->member_prefix.$history->lead->member_code ?? '-' }}
                                                        <span class="d-block">
                                                            {{ $history->lead->name }}
                                                        </span>
                                                        <span class="d-block">
                                                            {{ $history->lead->phone }}
                                                        </span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.leads.show',$history->lead_id) }}" target="_blank" class="text-decoration-none">
                                                        <span class="d-block">
                                                            {{ $history->lead->name }}
                                                        </span>
                                                        <span class="d-block">
                                                            {{ $history->lead->phone }}
                                                        </span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \App\Models\LeadRemindersHistory::TYPE[$history->type] ?? '' }}
                                            </td>
                                            <td>
                                                {{ \App\Models\LeadRemindersHistory::ACTION[$history->action] ?? '' }}
                                            </td>
                                            <td>
                                                {{ $history->membership->member->source->name ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $history->membership->member->created_at ?? '-' }}
                                            </td>
                                            <td>
                                                <span class="d-block">
                                                    {{ $history->membership->service_pricelist->name ?? '-' }}
                                                </span>
                                                @if ($history->type == 'due_payment')
                                                    <span class="d-block">
                                                        {{ trans('global.total') }} : {{ $history->membership->invoice->net_amount ?? 0 }}
                                                    </span>
                                                    <span class="d-block">
                                                        {{ trans('invoices::invoice.paid') }} : {{ $history->membership->invoice->payments->sum('amount') ?? 0 }}
                                                    </span>
                                                    <span class="d-block">
                                                        {{ trans('global.rest') }} : {{ $history->membership->invoice->rest ?? 0 }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $history->due_date ?? '' }}</td>
                                            <td>{{ $history->notes }}</td>
                                            <td>{{ $history->user->name ?? '-' }}</td>
                                            <td>{{ $history->created_at }}</td>
                                            <td>
                                                @can('reminder_delete')
                                                    <form action="{{ route('admin.reminderHistory.destroy',$history->id) }}" method="post" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
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
            </div>
        </div>
    </div>

@endsection