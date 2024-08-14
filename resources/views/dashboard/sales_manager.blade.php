
<h4><i class="fa fa-bell"></i> {{ trans('cruds.reminder.title') }}</h4>
<div class="row">
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('admin.reports.reminders') }}" class="text-decoration-none">
            <div class="card ">
                <div class="card-body bg-info text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $today_reminders->count() }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.today_reminders') }}</h5>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('admin.reports.reminders') }}" class="text-decoration-none">
            <div class="card ">
                <div class="card-body bg-info text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $upcomming_reminders->count() }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('cruds.reminder.fields.upcomming_reminders') }}</h5>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('admin.reports.reminders') }}" class="text-decoration-none">
            <div class="card ">
                <div class="card-body bg-info text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $overdue_reminders->count() }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('cruds.reminder.fields.overdue_remiders') }}</h5>
                </div>
            </div>
        </a>
    </div>
</div>
{{-- =============================== --}}
<hr>

<h4><i class="fa fa-money"></i> {{ trans('global.monthly_sales') }}</h4>
<div class="row">
    <div class="col-sm-6 col-lg-4">
        <a href="#" class="text-decoration-none">
            <div class="card ">
                <div class="card-body bg-success text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ number_format($invoices->sum('net_amount')) }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.total_amount').' (Invoices) ' }}</h5>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="#" class="text-decoration-none">
            <div class="card ">
                <div class="card-body bg-primary text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ number_format($payments->sum('amount')) }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.total_income').' (Payments) ' }}</h5>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="#" class="text-decoration-none">
            <div class="card ">
                <div class="card-body bg-warning text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ number_format($invoices->sum('rest')) }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.pending').' ( Pending Payments this month)' }}</h5>
                </div>
            </div>
        </a>
    </div>
</div>
{{-- =============================== --}}
<hr>
<h4><i class="fas fa-bullseye"></i> {{ trans('global.sales_achievements') }}</h4>
<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-success text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ number_format($manager_target) . ' EGP' }}</h5>
                    <h5> {{ trans('global.target') }}</h5>
                </div>
            </div>
        </div>
    </div>

    

    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-primary text-white text-center">
                <div>
                    {{-- <a href="" style="color:white;"> --}}
                        <h5 class="fs-4 fw-semibold"> 
                            {{ number_format($manager_achieved) . ' EGP' }} ({{ number_format($manager_achieved_per,2) }}%)
                        </h5> 
                    {{-- </a> --}}
                    <h5>{{ trans('global.achieved') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-warning text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ number_format($manager_pending).' EGP' ?? 0 }}</h5>
                    <h5> {{ trans('global.pending') }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ==================================== --}}
<hr>
<h4><i class="fas fa-bullseye"></i> {{ trans('global.total_transfered') }}</h4>
<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-success text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold"> {{ $all_leads->count() }}</h5>
                    <h5><i class="fa fa-users"></i> {{ trans('global.total_leads') }}</h5>
                </div>
            </div>
        </div>
    </div>

    

    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-primary text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold"> {{ $members->count() > 0 ? $members->count() .' ( '. number_format($members_per,1) .' ) %' : 0 .' %' }}</h5>
                    <h5><i class="fa fa-check"></i> {{ trans('global.total_transfered') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card">
            <div class="card-body bg-warning text-white text-center">
                <div>
                    <h5 class="fs-4 fw-semibold">{{ $leads > 0 ? $leads .' ( '. number_format($leads_per,1) .' ) %' : 0 .' %' }}</h5>
                    <h5> <i class="fa fa-spinner fa-spin"></i>  {{ trans('global.pending') }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =============================== --}}
<hr>
{{-- branch sources --}}
<div class="form-group">
    <div class="card">
        <div class="card-header">
            {{ Auth()->user()->employee && Auth()->user()->employee->branch ? Auth()->user()->employee->branch->name : ''}} Sources
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered zero-configuration">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Source</th>
                        <th>Leads</th>
                        <th>Members</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sources as $source)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $source->name }}</td>
                            <td>{{ $source->leads_count }}</td>
                            <td>{{ $source->members_count }}</td>
                            <td>{{ $source->members_count > 0 ? number_format(($source->members_count / $source->leads_count) * 100,2).' %' : '0 %' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- branch sources --}}

{{-- due payments --}}
<div class="form-group">
    <div class="card">
        <div class="card-header">
            Reminders History
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered zero-configuration text-center">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        @foreach (App\Models\LeadRemindersHistory::ACTION as $key => $value)
                            <th class="text-dark">{{ $value }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reminder_sales as $reminder_sale)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $reminder_sale->name ?? '-' }}</td>
                            @foreach (App\Models\LeadRemindersHistory::ACTION as $key => $value)
                                <td class="font-weight-bold">
                                    <a href="{{ route('admin.remindersHistory.index',[
                                            'user_id'           => $reminder_sale->id,
                                            'action'            => $key,  
                                            'due_date[from]'    => date('Y-m-d')
                                        ]) }}" target="_blank">
                                        {{ $reminder_sale->reminders_histories()->whereDate('due_date',date('Y-m-d'))->whereAction($key)->count() ?? 0 }}
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- due payments --}}

{{-- due payments --}}
<div class="form-group">
    <div class="card">
        <div class="card-header">
            {{ Auth()->user()->employee && Auth()->user()->employee->branch ? Auth()->user()->employee->branch->name : ''}} Due Payments
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered zero-configuration text-center">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        <th class="text-dark">{{ trans('global.total') }}</th>
                        <th class="text-dark">{{ trans('global.count') }}</th>
                        <th class="text-dark">{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $key => $sale)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $sale->name ?? '-' }}</td>
                            <td class="font-weight-bold">{{ number_format($sale->invoices->sum('rest')) }} EGP</td>
                            <td class="font-weight-bold">{{ $sale->invoices_count }}</td>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.invoice.duePayments',$sale->id) }}" class="btn font-weight-bold btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> {{ trans('cruds.invoice.title') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- due payments --}}

<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-file"></i> {{ trans('global.today_reminders') }}</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('cruds.user.title_singular') }}</th>
                        <th>{{ trans('global.today_reminders') }}</th>
                        <th>{{ trans('global.pending') }}</th>
                        <th>{{ trans('global.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales_reminders as $key => $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $sale->name }}</td>
                            <td>{{ $sale->reminders_count + $sale->reminders_histories_count }}</td>
                            <td>
                                <a href="{{ route('admin.reminders.index',[
                                        'user_id[]' => $sale->id,
                                        'due_date' => request('due_date') ?? date('Y-m-d')
                                    ]) }}">
                                    {{ $sale->reminders_count }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.remindersHistory.index',['user_id[]' => $sale->id]) }}">
                                    {{ $sale->reminders_histories_count }}
                                </a>
                            </td>
                        </tr> 
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- =============================== --}}
<hr>
{{-- =============================== --}}

<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-file"></i> {{ trans('global.sales_report') }}</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.name') }}</th>
                        <th>{{ trans('global.memberships') }}</th>
                        <th>{{ trans('global.target') }}</th>
                        <th>{{ trans('cruds.payment.title') }}</th>
                        <th>{{ trans('global.achieved') }}</th>
                        <th>{{ trans('global.commission') }} ( A )</th>
                        <th>{{ trans('global.commission') }} ( % )</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $key => $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $sale->name }}</td>
                            <td>{{ $sale->memberships_count }}</td>
                            <td>{{ number_format($sale->employee->target_amount ?? 0) }} EGP</td>
                            <td>
                                {{ number_format($sale->payments->sum('amount')) ?? 0 }} EGP ({{ $sale->payments->count() }})
                            </td>
                            <td>
                                @if(isset($sale->payments) && $sale->employee->target_amount > 0)
                                    @isset($sale->sales_tier->sales_tier)
                                    {{ round(($sale->payments->sum('amount') / $sale->employee->target_amount) * 100) }}
                                        %
                                    @else
                                        {{ trans('global.there_is_no_sales_tier') }}
                                    @endisset
                                @endif
                            </td>
                            <td>
                                
                                @isset($sale->payments)
                                    @isset($sale->sales_tier->sales_tier)
                                    @php
                                        $sales_payments = $sale->payments->sum('amount');
                                        if ($sales_payments && $sale->employee->target_amount > 0) 
                                        {
                                            $achieved = ($sales_payments / $sale->employee->target_amount) *100;
                                            
                                            $sales_sales_tier_amount = ($sales_payments * $sale->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved)->orderBy('range_from','desc')->first()->commission) / 100;
                                        }
                                    @endphp
                                    {{ $sales_payments ? $sales_sales_tier_amount : 0 }} EGP
                                    @else
                                        {{ trans('global.there_is_no_sales_tier') }}
                                    @endisset
                                @endisset
                            </td>
                            <td>
                                @isset($sale->payments)
                                    @isset($sale->sales_tier->sales_tier)
                                        @php
                                            $sales_payments = $sale->payments->sum('amount');
                                            if ($sales_payments && $sale->employee->target_amount > 0) 
                                            {
                                                $achieved = ($sales_payments / $sale->employee->target_amount) *100;
                                                $sales_sales_tier_commission = ($sale->sales_tier->sales_tier->sales_tiers_ranges()->where('range_from', '<=', $achieved)->orderBy('range_from','desc')->first()->commission ?? 0);
                                            }
                                            
                                        @endphp
                                        {{ $sales_payments ? $sales_sales_tier_commission  : 0 }} %
                                    @else
                                        {{ trans('global.there_is_no_sales_tier') }}
                                    @endisset
                                @endisset
                            </td>
                            <td>
                                <a href="{{ route('admin.reports.sales-report.view',[$sale->id,'date='.request()->date]) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> {{ trans('global.view') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- =============================== --}}
<hr>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.offers_report') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.service.fields.name') }}</th>
                                <th class="text-dark">{{ trans('global.count') }}</th>
                                <th class="text-dark">{{ trans('global.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($offers as $key => $offer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $key }}</td>
                                    <td>{{ $offer->count() }}</td>
                                    <td>{{ number_format($offer->sum('amount')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Chart
            </div>
            <div class="card-body">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>
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

    {{-- @if ($reminders_sources->isNotEmpty())
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
    @endif --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [
                @php 
                    foreach($offers as $key => $offer){
                        echo "'".$key."'" . ',';
                    }
                @endphp
            ],
            datasets: [{
                label: '# of Votes',
                data: [@php 
                    foreach($offers as $key => $offer){
                        echo "'".$offer->sum('amount')."'" . ',';
                    }
                @endphp],
                
                backgroundColor: [
                    
                    @php 
                    function random_color_part() {
                        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
                    }

                    function random_color() {
                        return random_color_part() . random_color_part() . random_color_part();
                    }

                    foreach($offers as $key => $offer){
                           echo "'#" . random_color() . "',";
                        }
                    @endphp
                ],
                
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>