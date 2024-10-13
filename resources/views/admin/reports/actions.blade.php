@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ URL::current() }}" method="get">
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="date">{{ trans('global.filter') }}</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="from"
                                value="{{ request('from') ?? date('Y-m-01') }}">
                            <input type="date" class="form-control" name="to"
                                value="{{ request('to') ?? date('Y-m-t') }}">
                            <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : ''}}>
                                <option value="{{ null }}" selected>All Branches</option>
                                @foreach (App\Models\Branch::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                            <select name="reminder_action" id="reminder_action" class="form-control">
                                <option value="{{ null }}" selected>Action</option>
                                @foreach (App\Models\Reminder::ACTION as $reminder_id => $reminder_action)
                                    <option value="{{ $reminder_id }}"
                                        {{ request('reminder_action') == $reminder_id ? 'selected' : '' }}>{{ $reminder_action }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="sales_by_id" id="sales_by_id" class="form-control" {{ $employee && Auth()->user()->roles[0]->title == 'Sales' ? 'readonly' : '' }}>
                                <option value="{{ null }}" selected>Sales By</option>
                                @if($employee && $employee->branch_id != NULL)
                                    @foreach (App\Models\User::whereRelation('roles','title','Sales')->whereRelation('employee','branch_id',$employee->branch_id)->whereRelation('employee','status','active')->pluck('name', 'id') as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ request('sales_by_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                @else
                                    @foreach (App\Models\User::whereRelation('roles','title','Sales')->whereRelation('employee','status','active')->pluck('name', 'id') as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ request('sales_by_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach

                                @endif
                            </select>
                            <select name="type" id="type" class="form-control">
                                <option value="{{ null }}" selected>Type</option>
                                @foreach (App\Models\Lead::TYPE_SELECT as $key => $value)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend">
                                <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                                <a href="{{ route('admin.reports.actions-report.export',request()->all()) }}" class="btn btn-info">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="form-group">
        <table class="table table-bordered table-striped table-hover zero-configuration">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        {{ trans('cruds.lead.title_singular') }}
                    </th>
                    <th>
                        {{ trans('cruds.branch.title_singular') }}
                    </th>
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
                    <th>
                        {{ trans('cruds.lead.fields.sales_by') }}
                    </th>
                    <th>{{ trans('global.notes') }}</th>
                    <th>{{ trans('global.action_date') }}</th>
                    <th>{{ trans('global.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reminder_actions as $reminder)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($reminder->lead->type == 'member')
                                <a href="{{ route('admin.members.show', $reminder->lead_id) }}" target="_blank"
                                    class="text-decoration-none">
                                    {{ \App\Models\Setting::first()->member_prefix . $reminder->lead->member_code ?? '-' }}
                                    <span class="d-block">
                                        {{ $reminder->lead->name }}
                                    </span>
                                    <span class="d-block">
                                        {{ $reminder->lead->phone }}
                                    </span>
                                </a>
                            @else
                                <a href="{{ route('admin.leads.show', $reminder->lead_id) }}" target="_blank"
                                    class="text-decoration-none">
                                    <span class="d-block">
                                        {{ $reminder->lead->name }}
                                    </span>
                                    <span class="d-block">
                                        {{ $reminder->lead->phone }}
                                    </span>
                                </a>
                            @endif
                            {{ $reminder->lead->type ?? '-' }}
                        </td>
                        <td>{{ $reminder->lead->branch->name ?? '-' }}</td>
                        <td>
                            {{ \App\Models\Reminder::TYPE[$reminder->type] ?? '' }}
                        </td>
                        <td>
                            {{ \App\Models\Reminder::ACTION[$reminder->action] ?? '' }}
                        </td>
                        <td>
                            <span class="d-block">
                                {{ $reminder->membership->service_pricelist->name ?? '-' }}
                            </span>
                            @if ($reminder->type == 'due_payment')
                                <span class="d-block">
                                    {{ trans('global.total') }} :
                                    {{ $reminder->membership->invoice->net_amount ?? 0 }}
                                </span>
                                <span class="d-block">
                                    Paid :
                                    {{ $reminder->membership->invoice->payments_sum_amount ?? 0 }}
                                </span>
                                <span class="d-block">
                                    {{ trans('global.rest') }} :
                                    {{ $reminder->membership->invoice->rest ?? 0 }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $reminder->due_date ?? '' }}</td>
                        <td>{{ $reminder->user->name ?? '-' }}</td>
                        <td>{{ $reminder->notes }}</td>
                        <td>{{ $reminder->created_at }}</td>
                        <td>
                            @can('reminder_delete')
                                <form action="{{ route('admin.reminderHistory.destroy', $reminder->id) }}" method="post"
                                    onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        <i class="fa fa-trash"></i>
                                        {{ trans('global.delete') }}
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
