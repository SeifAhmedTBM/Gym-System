    {{-- @if (config('domains')[config('app.url')]['employees_schedule'] == true)
        @include('partials.schedule')
    @endif --}}

    @if (config('domains')[config('app.url')]['timeline_schedule'] == true)
    <h4><i class="fa fa-calendar"></i> {{ trans('global.schedule_timeline') }}</h4>
    @include('partials.schedule')
    @endif
    {{-- @include('partials.searchMember') --}}

    @if (config('domains')[config('app.url')]['profile_attendance_dashboard'] == true)
        @include('partials.profile_attendance')
    @endif

    <h4><i class="fa fa-money"></i> {{ trans('cruds.finance.title') }}</h4>
    @isset($daily_income)
        <div class="row py-2">
            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('admin.invoices.index', ['created_at' => ['from' => date('Y-m-d')]]) }}"
                    class="text-decoration-none text-white">
                    <div class="card">
                        <div class="card-body bg-primary text-white text-center">
                            <h5 class="fs-4 fw-semibold">{{ number_format($daily_income) }} EGP</h5>
                            <h5><i class="fa fa-money"></i>
                                {{ trans('global.daily_income') }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-4">
                <a href="{{ route('admin.expenses.index', ['created_at' => ['from' => date('Y-m-d')]]) }}"
                    class="text-decoration-none text-white">
                    <div class="card">
                        <div class="card-body bg-danger text-white text-center">
                            <h5 class="fs-4 fw-semibold">{{ $daily_outcome }} EGP</h5>
                            <h5><i class="fa fa-money"></i>
                                {{ trans('global.daily_outcome') }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body bg-success text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ $daily_net }} EGP</h5>
                        <h5><i class="fa fa-money"></i>
                            {{ trans('global.daily_total') }}</h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>
    @endisset
    
    @isset($monthly_income)
        <div class="row">
            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body bg-primary text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ number_format($monthly_income) }} EGP</h5>
                        <h5><i class="fa fa-dollar"></i>
                            {{ trans('global.monthly_income') }} </h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body bg-danger text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ number_format($monthly_outcome) }} EGP</h5>
                        <h5><i class="fa fa-dollar"></i>
                            {{ trans('global.monthly_outcome') }}</h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body bg-success text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ number_format($monthly_net) }} EGP</h5>
                        <h5><i class="fa fa-dollar"></i>
                            {{ trans('global.monthly_total') }}</h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>
    @endisset

    <hr>

    @if(isset($monthly_income) && $total_targets > 0)
        <h4><i class="fas fa-bullseye"></i> {{ trans('global.sales_achievements') }}</h4>
        <div class="row py-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body  text-center">
                        <h5 class="fs-4 fw-semibold">
                            {{ number_format($total_targets) }} EGP
                        </h5>
                        {{ trans('global.sales_target') }}</h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body  text-center">
                        <h5 class="fs-4 fw-semibold">
                            {{ number_format($monthly_income) }} EGP ( {{ ( round(($monthly_income / $total_targets ) * 100, 2)) }} % ) 
                        </h5>
                        {{ trans('global.sales_achievements') }}</h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->

            <div class="col-sm-6 col-lg-4">
                <div class="card ">
                    <div class="card-body  text-center">
                        <h5 class="fs-4 fw-semibold">
                            {{ number_format($total_targets - $monthly_income) }} EGP ( {{ ( round((($total_targets - $monthly_income) / $total_targets ) * 100, 2))  }} % )
                        </h5>
                        {{ trans('global.rest') }}</h5>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>
    @endif

    <hr>

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

    <hr>

    {{-- <h4><i class="fas fa-fingerprint"></i> {{ trans('cruds.membershipAttendance.title') }}</h4>
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('admin.membership-attendances.index') }}" class="text-decoration-none text-white">
                <div class="card">
                    <div class="card-body bg-success text-white text-center">
                        <h5 class="fs-4 fw-semibold">{{ $daily_attendances }}</h5>
                        <i class="fa fa-bell"></i>
                        {{ trans('global.daily_attendances') }}</h5>
                    </div>
                </div>
            </a>
        </div>
       
        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body bg-warning text-white text-center">
                    <h5 class="fs-4 fw-semibold">0</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.expiry_attendances') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body bg-danger text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $expired_attendances }}</h5>
                    <i class="fa fa-bell"></i>
                    {{ trans('global.expired_attendances') }}</h5>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <hr> --}}

    {{-- <div class="row">
        <div class="col-sm-6 col-lg-6">
            <div class="card ">
                <div class="card-header">
                    Best 5 Services
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('cruds.service.title_singular') }}</th>
                                <th>{{ trans('global.count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->memberships_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-6">
            <div class="card ">
                <div class="card-header">
                    Best 5 Offers
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Offer</th>
                                <th>{{ trans('global.count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pricelists as $pricelist)
                                <tr>
                                    <td>{{ $pricelist->name }}</td>
                                    <td>{{ $pricelist->memberships_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa fa-recycle"></i> {{ trans('cruds.transactions.title') }}
                        {{ trans('global.list') }}</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-statement">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.transactions.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.transactions.fields.account') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.transactions.fields.type') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.lead.fields.notes') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.transactions.fields.amount') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.transactions.fields.created_by') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $key => $transaction)
                                    <tr
                                        class="{{ \App\Models\Transaction::color[$transaction->transactionable_type] }}">
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            {{ $transaction->account->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ \App\Models\Transaction::type[$transaction->transactionable_type] }}
                                        </td>
                                        <td>
                                            @switch(\App\Models\Transaction::type[$transaction->transactionable_type])
                                                @case('Expenses')
                                                    <a href="{{ route('admin.expenses.show',$transaction->transactionable_id) }}">
                                                        {{ $transaction->transactionable_type::find($transaction->transactionable_id)->name }}
                                                    </a>
                                                    @break
                                                @case('Refunds')
                                                    <a href="{{ route('admin.refunds.show',$transaction->transactionable_id) }}">
                                                        {{ App\Models\Setting::first()->invoice_prefix.' '.$transaction->transactionable_type::find($transaction->transactionable_id)->invoice_id }}</a>
                                                    @break
                                                @case('External Payments')
                                                    <a href="{{ route('admin.external-payments.show',$transaction->transactionable_id) }}">
                                                        {{ $transaction->transactionable_type::find($transaction->transactionable_id)->title }}
                                                    </a>    
                                                    @break
                                                @case('Payments')
                                                    <a href="{{ route('admin.invoices.show',$transaction->transactionable_type::find($transaction->transactionable_id)->invoice_id) }}">
                                                        {{ App\Models\Setting::first()->invoice_prefix.' '.$transaction->transactionable_type::find($transaction->transactionable_id)->invoice_id }}
                                                    </a>
                                                    @break
                                                @case('Withdrawal')
                                                    <a href="{{ route('admin.withdrawals.show',$transaction->transactionable_id) }}">
                                                        {{ $transaction->transactionable_type::find($transaction->transactionable_id)->notes }}
                                                    </a>
                                                    @break
            
                                                @case('Transfer')
                                                    {{ trans('global.from') }}
                                                    {{ $transaction->transactionable_type::find($transaction->transactionable_id)->fromAccount->name ?? '-' }} 
                                                    {{ trans('global.to') }}
                                                    {{ $transaction->transactionable_type::find($transaction->transactionable_id)->toAccount->name ?? '-' }}
                                                    @break
                                                @default
                                                    
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $transaction->amount ?? '' }}
                                        </td>
                                        <td>
                                            {{ $transaction->createdBy->name ?? '' }}
                                        </td>
                                    </tr>
                                @empty
                                    <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm" style="display: none;">
        <div class="card-header">
            <h5><i class="fa fa-user"></i> {{ trans('global.attendance_data') }}</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover text-center zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('cruds.member.fields.photo') }}</th>
                        <th>
                            {{ trans('cruds.lead.fields.member_code') }}
                        </th>
                        <th>{{ trans('global.name') }}</th>
                        <th>{{ trans('cruds.service.title_singular') }}</th>
                        <th>{{ trans('cruds.membershipAttendance.fields.sign_in') }}</th>
                        @if (isset(\App\Models\Setting::first()->has_lockers) && \App\Models\Setting::first()->has_lockers == true)
                            <th>{{ trans('cruds.membershipAttendance.fields.sign_out') }}</th>
                        @endif
                        <th>{{ trans('global.end_date') }}</th>
                        <th>{{ trans('global.attendance_count') }}</th>
                        <th>{{ trans('cruds.status.title_singular') }}</th>
                        @if (isset(\App\Models\Setting::first()->has_lockers) && \App\Models\Setting::first()->has_lockers == true)
                            <th>{{ trans('cruds.locker.title_singular') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($today_attendants as $attendant)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($attendant->membership->member->photo)
                                    <a href="{{ $attendant->membership->member->photo->getUrl() }}" target="_blank"
                                        style="display: inline-block">
                                        <img src="{{ $attendant->membership->member->photo->getUrl() }}"
                                            class="rounded-circle" style="width: 50px;height:50px">
                                    </a>
                                @else
                                    <a href="{{ asset('images/user.png') }}" target="_blank"
                                        style="display: inline-block">
                                        <img src="{{ asset('images/user.png') }}" class="rounded-circle"
                                            style="width: 50px;height:50px">
                                    </a>
                                @endif
                            </td>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.members.show', $attendant->membership->member->id) }}"
                                    target="_blank">
                                    {{ \App\Models\Setting::first()->member_prefix . $attendant->membership->member->member_code }}
                                </a>
                            </td>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.members.show', $attendant->membership->member->id) }}"
                                    target="_blank">
                                    {{ $attendant->membership->member->name }}
                                </a>
                            </td>
                            <td>
                                {{ $attendant->membership->service_pricelist->name ?? '-' }}
                            </td>
                            <td>{{ date('g:i A', strtotime($attendant->sign_in)) }}</td>
                            @if (isset(\App\Models\Setting::first()->has_lockers) && \App\Models\Setting::first()->has_lockers == true)
                                <td>{{ date('g:i A', strtotime($attendant->sign_out)) }}</td>
                            @endif
                            <td>
                                {{ $attendant->membership->end_date ?? '-' }}
                            </td>
                            <td>
                                {{ ($attendant->membership->service_pricelist->service->service_type->session_type == "sessions") || ($attendant->membership->service_pricelist->service->service_type->session_type == "group_sessions") ? $attendant->membership->attendances_count ."/".$attendant->membership->service_pricelist->session_count : $attendant->membership->attendances_count }}
                            </td>
                            <th>
                                <span
                                    class="badge badge-{{ \App\Models\Membership::STATUS[$attendant->membership_status] }} p-2">
                                    <i class="fa fa-recycle"></i> {{ ucfirst($attendant->membership_status ) ?? '-' }}
                                </span>
                            </th>
                            @if (isset(\App\Models\Setting::first()->has_lockers) && \App\Models\Setting::first()->has_lockers == true)
                                <th>
                                    {!! $attendant->locker ?? '<span class="badge badge-danger">No locker</span>' !!}
                                </th>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- <div class="card-footer">
            <span class="float-right">
                {{ $today_attendants->appends(request()->all())->links() }}
            </span>
        </div> --}}
    </div>