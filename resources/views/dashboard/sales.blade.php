    @if (config('domains')[config('app.url')]['employees_schedule'] == true)
        @include('partials.schedule')
    @endif
    
    <h4><i class="fas fa-bullseye"></i> {{ trans('global.sales_achievements') }}</h4>
    @if (isset(Auth()->user()->sales_tier->sales_tier))
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body bg-success text-white text-center">
                        <div>
                            <h5 class="fs-4 fw-semibold">{{ number_format($target) . ' EGP' }}</h5>
                            <h5> {{ trans('global.target') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body bg-primary text-white text-center">
                        <div>
                            <a href="{{ route('admin.reports.sales-report.view', Auth::user()->id) }}"
                                style="color:white;">
                                <h5 class="fs-4 fw-semibold"> {{ number_format($achieved) . ' EGP' }}
                                    ({{ $achieved_per }}%)
                                </h5>
                            </a>
                            <h5>{{ trans('global.achieved') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body bg-warning text-white text-center">
                        <div>
                            <h5 class="fs-4 fw-semibold">{{ $pendingTarget . ' EGP' ?? 0 }}</h5>
                            <h5> {{ trans('global.pending') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body bg-success text-white text-center">
                        <div>
                            <h5 class="fs-4 fw-semibold">{{ number_format($sales_commission) ?? 0 }} EGP
                                ({{ $sales_commission_per ?? 0 }}%)</h5>
                            <h5> {{ trans('global.commission') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h5>Please ask admin to add sales tires to show achieved charts ! </h5>
    @endif

    <hr>

    <h4><i class="fa fa-bell"></i> {{ trans('cruds.reminder.title') }}</h4>
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('admin.reminders.index') }}" class="text-decoration-none" target="_blank">
                <div class="card ">
                    <div class="card-body bg-success text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ $today_reminders->count() }}</h5>
                        <i class="fa fa-bell"></i>
                        {{ trans('global.today_reminders') }}</h5>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <a href="{{ route('admin.reminders.upcomming', ['due_date[from]' => date('Y-m-01'), 'due_date[to]' => date('Y-m-t')]) }}"
                    class="text-decoration-none" target="_blank">
                    <div class="card-body bg-warning text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ $upcomming_reminders->count() }}</h5>
                        <i class="fa fa-bell"></i>
                        {{ trans('cruds.reminder.fields.upcomming_reminders') }}</h5>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <a href="{{ route('admin.reminders.overdue') }}" class="text-decoration-none" target="_blank">
                    <div class="card-body bg-danger text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ $overdue_reminders->count() }}</h5>
                        <i class="fa fa-bell"></i>
                        {{ trans('cruds.reminder.fields.overdue_remiders') }}</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">

        <div class="col-sm-6">
            <div class="card">
                <div class="card-body bg-info text-white text-center">
                    <div>
                        <h5 class="fs-4 fw-semibold"> {{ $monthly_memberships }}</h5>
                        <h5><i class="fa fa-users"></i> {{ trans('global.monthly_memberships') }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <a href="{{ route('admin.invoice.duePayments', Auth()->user()->id) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-body bg-info text-white text-center">
                        <div>
                            <h5 class="fs-4 fw-semibold"> {{ $duePayments->sum('rest') }}
                                ({{ $duePayments->count() }})</h5>
                            <h5><i class="fa fa-users"></i> {{ trans('global.due_payment') }}</h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if ($latest_leads->isNotEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            @foreach ($latest_leads as $key => $leads)
                                <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}"
                                    id="{{ str_replace(' ', '_', $key) }}-tab" data-toggle="pill"
                                    href="#{{ str_replace(' ', '_', $key) }}" role="tab"
                                    aria-controls="{{ str_replace(' ', '_', $key) }}" aria-selected="true">
                                    {{ $key }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="v-pills-tabContent">
                            @foreach ($latest_leads as $key => $leads)
                                <div class="tab-pane fade {{ $loop->iteration == 1 ? 'show active' : '' }}"
                                    id="{{ str_replace(' ', '_', $key) }}" role="tabpanel"
                                    aria-labelledby="{{ $key }}-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table
                                                class="table table-bordered table-striped table-hover zero-configuration">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Sales By</th>
                                                        <th>Source</th>
                                                        <th>Membership</th>
                                                        <th>Paid</th>
                                                        <th>Rest</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($leads as $lead)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                <a href="{{ $lead->type == 'member' ? route('admin.members.show', $lead->id) : route('admin.leads.show', $lead->id) }}"
                                                                    target="_blank">
                                                                    {{ $lead->last_membership ? App\Models\Setting::first()->member_prefix . $lead->member_code : ' ' }}
                                                                    <br>
                                                                    {{ $lead->name }} <br>
                                                                    {{ $lead->phone }} <br>
                                                                </a>
                                                            </td>
                                                            <td>{{ $lead->type }}</td>
                                                            <td>
                                                                {{ $lead->sales_by->name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->source->name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->last_membership->service_pricelist->name ?? '-' }}
                                                                <br>
                                                                {{ $lead->last_membership ? number_format($lead->last_membership->invoice->net_amount) : ' ' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->last_membership ? number_format($lead->last_membership->invoice->payments->sum('amount')) : 0 }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->last_membership ? number_format($lead->last_membership->invoice->rest) : '-' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        NOT FOUND 2
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($reminders_sources->isNotEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            @foreach ($reminders_sources as $key => $reminder_sources)
                                <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}"
                                    id="source_{{ str_replace(' ', '_', $key) }}-tab" data-toggle="pill"
                                    href="#source_{{ str_replace(' ', '_', $key) }}" role="tab"
                                    aria-controls="source_{{ str_replace(' ', '_', $key) }}" aria-selected="true">
                                    {{ $key }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="v-pills-tabContent">
                            @foreach ($reminders_sources as $key => $reminder_sources)
                                <div class="tab-pane fade {{ $loop->iteration == 1 ? 'show active' : '' }}"
                                    id="source_{{ str_replace(' ', '_', $key) }}" role="tabpanel"
                                    aria-labelledby="{{ $key }}-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table
                                                class="table table-bordered table-striped table-hover zero-configuration">
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
                                                            {{ trans('global.details') }}
                                                        </th>
                                                        <th>
                                                            {{ trans('global.due_date') }}
                                                        </th>
                                                        <th>{{ trans('global.notes') }}</th>
                                                        <th>{{ trans('global.action_date') }}</th>
                                                        <th>{{ trans('global.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($reminder_sources as $reminder)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                @if ($reminder->lead->type == 'member')
                                                                    <a href="{{ route('admin.members.show', $reminder->lead_id) }}"
                                                                        target="_blank" class="text-decoration-none">
                                                                        {{ \App\Models\Setting::first()->member_prefix . $reminder->lead->member_code ?? '-' }}
                                                                        <span class="d-block">
                                                                            {{ $reminder->lead->name }}
                                                                        </span>
                                                                        <span class="d-block">
                                                                            {{ $reminder->lead->phone }}
                                                                        </span>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('admin.leads.show', $reminder->lead_id) }}"
                                                                        target="_blank" class="text-decoration-none">
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
                                                            <td>{{ $reminder->notes }}</td>
                                                            <td>{{ $reminder->created_at }}</td>
                                                            <td>
                                                                @can('reminder_delete')
                                                                    <form
                                                                        action="{{ route('admin.reminderHistory.destroy', $reminder->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure?');"
                                                                        style="display: inline-block;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="btn btn-danger btn-sm"
                                                                            type="submit">
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
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif