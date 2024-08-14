<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            @if (isset(App\Models\Setting::first()->menu_logo))
                <img src="{{ asset('images/' . App\Models\Setting::first()->menu_logo) }}" width="100" alt="">
            @endif
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route('admin.home') }}" class="c-sidebar-nav-link" target="">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>

        @can('take_attendance')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('attendance_take.index') }}" class="c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">
                    </i>
                    {{ trans('global.take_attendance') }}
                </a>
            </li>
        @endcan

        @can('view_employee_attendances')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.employee_attendances') }}" class="c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">
                    </i>
                    {{ trans('global.employee_attendances') }}
                </a>
            </li>
        @endcan

        @can('user_management_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/permissions*') ? 'c-show' : '' }} {{ request()->is('admin/roles*') ? 'c-show' : '' }} {{ request()->is('admin/users*') ? 'c-show' : '' }} {{ request()->is('admin/audit-logs*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.permissions.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('audit_log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.audit-logs.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/audit-logs') || request()->is('admin/audit-logs/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.auditLog.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        @can('master_data_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.master-data.index') }}"
                    class="c-sidebar-nav-link {{ request()->is('admin/master-data') ? 'c-active font-weight-bold' : '' }}">
                    <i class="fa-fw fas fa-cog c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.masterData.title') }}
                </a>
            </li>
        @endcan

        @can('operations_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/leads*') ? 'c-show' : '' }} {{ request()->is('admin/members*') ? 'c-show' : '' }} {{ request()->is('admin/memberships*') ? 'c-show' : '' }} {{ request()->is('admin/membership-attendances*') ? 'c-show' : '' }} {{ request()->is('admin/freeze-requests*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-id-card c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.subscription.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('lead_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.leads.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/leads') || request()->is('admin/leads/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.lead.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('member_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.members.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/members') || request()->is('admin/members/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.member.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('membership_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.memberships.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/memberships') || request()->is('admin/memberships/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-fingerprint c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.membership.title') }}
                            </a>
                        </li>
                    @endcan



                    @can('membership_attendance_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.membership-attendances.index', [
                                'created_at[from]' => date('Y-m-01'),
                                'created_at[to]' => date('Y-m-t'),
                            ]) }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/membership-attendances') || request()->is('admin/membership-attendances/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-address-book c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.membershipAttendance.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('freeze_request_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.freeze-requests.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/freeze-requests') || request()->is('admin/freeze-requests/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-minus-circle c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.freezeRequest.title') }}
                            </a>
                        </li>
                    @endcan



                    @can('member_requests_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.member-requests.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/member-requests') || request()->is('admin/member-requests/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-hand-paper c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.member_requests') }}
                            </a>
                        </li>
                    @endcan



                    {{-- <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.member-suggestion.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/member-suggestion") || request()->is("admin/member-suggestion/*") ? "c-active font-weight-bold" : "" }}">
                            <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                            </i>
                            {{ trans('cruds.member_suggestion.title') }}
                        </a>
                    </li> --}}
                    @can('ratings_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.ratings.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/ratings') || request()->is('admin/ratings/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.trainer_ratings') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        @can('finance_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/expenses*') ? 'c-show' : '' }} {{ request()->is('admin/invoices*') ? 'c-show' : '' }} {{ request()->is('admin/payments*') ? 'c-show' : '' }} {{ request()->is('admin/refunds*') ? 'c-show' : '' }} {{ request()->is('admin/accounts*') ? 'c-show' : '' }} {{ request()->route()->getName() == 'admin.invoices.partial'? 'c-show': '' }} {{ request()->is('admin/external-payments*') ? 'c-show' : '' }} {{ request()->is('admin/withdrawals*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw far fa-credit-card c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.finance.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('expense_access')
                        @if (!in_array(
                            'Admin',
                            auth()->user()->roles->pluck('title')->toArray()))
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route('admin.expenses.index') }}"
                                    class="c-sidebar-nav-link {{ request()->is('admin/expenses') || request()->is('admin/expenses/*') ? 'c-active font-weight-bold' : '' }}">
                                    <i class="fa-fw fas fa-money-bill-alt c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.expense.title') }}
                                </a>
                            </li>
                        @else
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route('admin.expenses.index') }}"
                                    class="c-sidebar-nav-link {{ request()->is('admin/expenses') || request()->is('admin/expenses/*') ? 'c-active font-weight-bold' : '' }}">
                                    <i class="fa-fw fas fa-money-bill-alt c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.expense.title') }}
                                </a>
                            </li>
                        @endif

                    @endcan
                    @can('loan_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.loans.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/loans') || request()->is('admin/loans/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-hand-holding-usd c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.loan.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('  ')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.invoices.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/invoices') || request()->is('admin/invoices/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-file-invoice-dollar c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.invoice.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('partial_invoice_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.invoices.partial') }}"
                                class="c-sidebar-nav-link {{ request()->route()->getName() == 'admin.invoices.partial'? 'c-active font-weight-bold': '' }}">
                                <i class="fa-fw fas fa-file-invoice-dollar c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.partial_invoices') }}
                            </a>
                        </li>
                    @endcan
                    @can('settlement_invoice_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.invoices.settlement') }}"
                                class="c-sidebar-nav-link {{ request()->route()->getName() == 'admin.invoices.settlement'? 'c-active font-weight-bold': '' }}">
                                <i class="fa-fw fas fa-file-invoice-dollar c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.settlement_invoices') }}
                            </a>
                        </li>
                    @endcan
                    @can('payment_access')
                        {{-- ['created_at[from]' => date('Y-m-01'),'created_at[to]'  => date('Y-m-t')] --}}
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.payments.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/payments') || request()->is('admin/payments/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-credit-card c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.payment.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('refund_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.refunds.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/refunds') || request()->is('admin/refunds/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-exchange-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.refund.title') }}
                            </a>
                        </li>

                        {{-- <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.refund.requests") }}" class="c-sidebar-nav-link {{ request()->is("admin/refund.requests") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-exchange-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.refund_requests') }}
                            </a>
                        </li> --}}
                    @endcan

                    @can('account_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.accounts.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/accounts') || request()->is('admin/accounts/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-file-invoice-dollar c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.account.title') }}
                            </a>
                        </li>
                        @if (Auth()->user()->roles[0]->title == 'Developer')
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route('admin.transactions.index') }}"
                                    class="c-sidebar-nav-link {{ request()->is('admin/transactions') || request()->is('admin/transactions/*') ? 'c-active font-weight-bold' : '' }}">
                                    <i class="fa-fw fas fa-window-minimize c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.transactions.title') }}
                                </a>
                            </li>
                        @endif
                    @endcan
                    @can('external_payment_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.external-payments.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/external-payments') || request()->is('admin/external-payments/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-plus-circle c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.externalPayment.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('withdrawal_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.withdrawals.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/withdrawals') || request()->is('admin/withdrawals/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-window-minimize c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.withdrawal.title') }}
                            </a>
                        </li>
                    @endcan


                    {{-- <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.assets-maintenances.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/assets-maintenances") || request()->is("admin/assets-maintenances/*") ? "c-active font-weight-bold" : "" }}">
                            <i class="fa-fw fas fa-window-minimize c-sidebar-nav-icon"></i>
                            {{ trans('cruds.assetsMaintenance.title_singular') }}
                        </a>
                    </li> --}}
                </ul>
            </li>
        @endcan

        @can('hr_management_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/employees*') ? 'c-show' : '' }} {{ request()->is('admin/bonus*') ? 'c-show' : '' }} {{ request()->is('admin/deductions*') ? 'c-show' : '' }} {{ request()->is('admin/loans*') ? 'c-show' : '' }} {{ request()->is('admin/vacations*') ? 'c-show' : '' }} {{ request()->is('admin/documents*') ? 'c-show' : '' }} {{ request()->is('admin/employee-settings*') ? 'c-show' : '' }} {{ request()->is('admin/schedule-templates*') ? 'c-show' : '' }} {{ request()->is('admin/attendance-settings*') ? 'c-show' : '' }}  ">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fab fa-black-tie c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.hrManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('view_schedule_template')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.schedule-templates.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/schedule-templates') || request()->is('admin/schedule-templates/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.schedule_templates') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_transfer_sales_data')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.transfer_sales_data.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/transfer_sales_data.index') || request()->is('admin/transfer_sales_data.index/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-minus-circle c-sidebar-nav-icon"></i>
                                {{ trans('global.transfer_sales_data') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_attendance_settings')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.attendance-settings.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/attendance-settings') || request()->is('admin/attendance-settings/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-minus-circle c-sidebar-nav-icon"></i>
                                {{ trans('global.delay_rules') }}
                            </a>
                        </li>
                    @endcan
                    @can('employee_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.employees.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/employees') || request()->is('admin/employees/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-user-tie c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.employee.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('bonu_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.bonus.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/bonus') || request()->is('admin/bonus/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-user-plus c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.bonu.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('deduction_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.deductions.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/deductions') || request()->is('admin/deductions/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-user-minus c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.deduction.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('loan_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.loans.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/loans') || request()->is('admin/loans/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-hand-holding-usd c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.loan.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('vacation_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.vacations.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/vacations') || request()->is('admin/vacations/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-umbrella-beach c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.vacation.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('document_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.documents.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/documents') || request()->is('admin/documents/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.document.title') }}
                            </a>
                        </li>
                    @endcan
                    {{-- @can('employee_setting_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.employee-settings.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/employee-settings") || request()->is("admin/employee-settings/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-cogs c-sidebar-nav-icon"></i>
                                {{ trans('cruds.employeeSetting.title') }}
                            </a>
                        </li>
                    @endcan --}}
                    @can('view_employee_attendances')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.employee-attendance.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/employee-attendance') || request()->is('admin/employee-attendance/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-fingerprint c-sidebar-nav-icon"></i>
                                {{ trans('global.employee_attendances') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_payroll_page')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.payroll.get') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/payroll') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fa fa-money-bill c-sidebar-nav-icon"></i>
                                {{ trans('global.payroll') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        @can('mobile_app_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin/hotdeals*') ? 'c-show' : '' }} {{ request()->is('admin/gallery-sections*') ? 'c-show' : '' }} {{ request()->is('admin/galleries*') ? 'c-show' : '' }} {{ request()->is('admin/video-sections*') ? 'c-show' : '' }} {{ request()->is('admin/videos*') ? 'c-show' : '' }} {{ request()->is('admin/newssections*') ? 'c-show' : '' }} {{ request()->is('admin/news*') ? 'c-show' : '' }} {{ request()->is('admin/reasons*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-mobile c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.mobileApp.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('hotdeal_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.hotdeals.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/hotdeals') || request()->is('admin/hotdeals/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-fire c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.hotdeal.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('gallery_section_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.gallery-sections.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/gallery-sections') || request()->is('admin/gallery-sections/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-images c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.gallerySection.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('gallery_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.galleries.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/galleries') || request()->is('admin/galleries/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-image c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.gallery.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('video_section_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.video-sections.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/video-sections') || request()->is('admin/video-sections/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-video c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.videoSection.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('video_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.videos.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/videos') || request()->is('admin/videos/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-file-video c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.video.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('newssection_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.newssections.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/newssections') || request()->is('admin/newssections/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw far fa-newspaper c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.newssection.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('news_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.news.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/news') || request()->is('admin/news/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-newspaper c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.news.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('reason_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reasons.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/reasons') || request()->is('admin/reasons/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-images c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.reason.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        {{--
        @can('faq_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/faq-categories*") ? "c-show" : "" }} {{ request()->is("admin/faq-questions*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-question c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.faqManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('faq_category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.faq-categories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/faq-categories") || request()->is("admin/faq-categories/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.faqCategory.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('faq_question_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.faq-questions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/faq-questions") || request()->is("admin/faq-questions/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-question c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.faqQuestion.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan --}}

        {{-- @can('markting_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("marketing/whatsapp*") ? "c-show" : "" }} 
                {{ request()->is("marketing/whatsapp*") || request()->is("marketing/mail*") || request()->is("marketing/sms*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-fire c-sidebar-nav-icon">

                    </i>
                    {{ trans('global.marketing') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.marketing.settings.index") }}" class="c-sidebar-nav-link {{ request()->is("marketing/settings") ? "c-active font-weight-bold" : "" }}">
                            <i class="fa-fw fas fa-cogs c-sidebar-nav-icon"></i>
                            {{ trans('global.marketing_settings') }}
                        </a>
                    </li>

                    @can('access_campaigns')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.marketing.campaigns.index") }}" class="c-sidebar-nav-link {{ request()->is("marketing/campaigns") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-image c-sidebar-nav-icon"></i>
                                {{ trans('global.campaigns') }}
                            </a>
                        </li>
                    @endcan

                    @if (App\Models\Marketing::where('service', 'whatsapp')->first())
                    @can('whatsapp_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.marketing.whatsapp.index") }}" class="c-sidebar-nav-link {{ request()->is("marketing/whtasapp") || request()->is("marketing/whtasapp/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fab fa-whatsapp c-sidebar-nav-icon"></i>
                                {{ trans('global.whatsapp') }}
                            </a>
                        </li>
                    @endcan
                    @endif

                    @if (App\Models\Marketing::where('service', 'sms')->first())
                    @can('sms_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.marketing.sms.index") }}" class="c-sidebar-nav-link {{ request()->is("marketing/sms") || request()->is("marketing/sms/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-comments c-sidebar-nav-icon"></i>
                                {{ trans('global.sms') }}
                            </a>
                        </li>
                    @endcan
                    @endif

                    @if (App\Models\Marketing::where('service', 'smtp')->first())
                    @can('email_campaigns_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.marketing.mails.index") }}" class="c-sidebar-nav-link {{ request()->is("marketing/mails") || request()->is("marketing/mails/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-envelope c-sidebar-nav-icon"></i>
                                {{ trans('global.email_campaigns') }}
                            </a>
                        </li>
                    @endcan
                    @endif
                </ul>
            </li>
        @endcan --}}

        {{-- @can('inventory_access')
        <li class="c-sidebar-nav-dropdown 
            {{ request()->is("admin/warehouses") || request()->is("admin/warehouses/*") ? "c-show" : "" }} {{ request()->is("admin/products") || request()->is("admin/products/*") ? "c-show" : "" }} {{ request()->is("admin/warehouse-products") || request()->is("admin/warehouse-products/*") ? "c-show" : "" }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-fire c-sidebar-nav-icon"> </i>
                {{ trans('global.inventory') }}
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @can('warehouse_access')
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.warehouses.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/warehouses") || request()->is("admin/warehouses/*") ? "c-active" : "" }}">
                            <i class="fa-fw fas fa-warehouse c-sidebar-nav-icon">

                            </i>
                            {{ trans('cruds.warehouse.title') }}
                        </a>
                    </li>
                @endcan
                @can('product_access')
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.products.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/products") || request()->is("admin/products/*") ? "c-active" : "" }}">
                            <i class="fa-fw fab fa-product-hunt c-sidebar-nav-icon">

                            </i>
                            {{ trans('cruds.product.title') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcan --}}

        @can('reports_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.reports') }}"
                    class="c-sidebar-nav-link {{ request()->is('admin/members/active') ? 'c-active font-weight-bold' : '' }}">
                    <i class="c-sidebar-nav-icon fas fa-list"></i>
                    {{ trans('global.reports') }}
                </a>
            </li>
            {{-- <li class="c-sidebar-nav-dropdown {{ request()->is("reports*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                    {{ trans('global.reports') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('view_active_members')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.members.active") }}" class="c-sidebar-nav-link {{ request()->is("admin/members/active") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-user-check c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.active_members') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_inactive_members')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.members.inactive") }}" class="c-sidebar-nav-link {{ request()->is("admin/members/inactive") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-user-times c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.inactive_members') }}
                            </a>
                        </li>
                    @endcan

                    @can('expired_membership_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.membership.expired") }}" class="c-sidebar-nav-link {{ request()->is("admin/membership.expired") || request()->is("admin/membership/expired") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-user-times c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.expired_memberships') }}
                            </a>
                        </li>
                    @endcan


                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.membership.expiring") }}" class="c-sidebar-nav-link {{ request()->is("admin/membership/expiring") ? "c-active font-weight-bold" : "" }}">
                            <i class="fa-fw fas fa-user-times c-sidebar-nav-icon">

                            </i>
                            {{ trans('global.expiring_memberships') }}
                        </a>
                    </li> 
                    
                    @can('view_freezes')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.freeze.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/freeze") || request()->is("admin/freeze/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-minus-circle c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.freezes') }}
                            </a>
                        </li>
                    @endcan
                    @can('invitations_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.invitations.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/invitations") || request()->is("admin/invitations/*") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-plus-circle c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.invitations_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_reminders_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.reminders") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/reminders") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-money-bill c-sidebar-nav-icon"></i>
                                {{ trans('global.reminders_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_revenue_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.revenue") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/revenue") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-money-bill c-sidebar-nav-icon"></i>
                                {{ trans('global.revenue') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_coaches_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.coaches") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/coaches") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fa fa-users c-sidebar-nav-icon"></i>
                                {{ trans('global.coaches') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_sales_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.sales-report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/sales-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-file c-sidebar-nav-icon"></i>
                                {{ trans('global.sales_report') }}
                            </a>
                        </li>
                    @endcan
                    @can('due_payments_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.due-payments-report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/due-payments-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-file c-sidebar-nav-icon"></i>
                                {{ trans('global.due_payments_report') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_freelancers_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.freelancers-report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/freelancers-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-users c-sidebar-nav-icon"></i>
                                {{ trans('global.freelancers_report') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_trainers_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.trainers-report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/trainers-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-dumbbell c-sidebar-nav-icon"></i>
                                {{ trans('global.trainers_report') }}
                            </a>
                        </li>
                    @endcan
                    @can('view_schedule_timeline')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.schedule.timeline") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/schedule-timeline") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.schedule_timeline') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_services_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.services") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/services") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.services_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_offers_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.offers") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/offers") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.offers_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_leads_source_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.leadsSource") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/leads-source") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.leads_source_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_member_source_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.membersSource") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/members-source") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.members_source_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_expenses_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.expenses.report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/expenses-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.expenses_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_daily_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.daily.report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/daily-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.daily_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_monthly_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.monthly.report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/monthly-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.monthly_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_yearly_finance_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.yearlyFinance.report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/yearly-finance-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.yearly_finance_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_monthly_finance_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.monthlyFinance.report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/monthly-finance-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.monthly_finance_report') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_refund_reasons_report')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.reports.refundReasons.report") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/refund-reasons-report") ? "c-active font-weight-bold" : "" }}">
                                <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                                {{ trans('global.refund_reason_report') }}
                            </a>
                        </li>
                    @endcan

                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.reports.sessionsAttendancesReport") }}" class="c-sidebar-nav-link {{ request()->is("admin/reports/sessions-attendances") ? "c-active font-weight-bold" : "" }}">
                            <i class="fa-fw fas fa-table c-sidebar-nav-icon"></i>
                            {{ trans('global.sessions_attendances') }}
                        </a>
                    </li>
                </ul>
            </li> --}}
        @endcan

        {{-- @can('user_alert_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.user-alerts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "c-active font-weight-bold" : "" }}">
                    <i class="fa-fw fas fa-bell c-sidebar-nav-icon"></i>
                    {{ trans('cruds.userAlert.title') }}
                </a>
            </li>
        @endcan --}}

        @can('reminder_access')
            <li
                class="c-sidebar-nav-dropdown {{ request()->is('admin.reminders.index*') ? 'c-show' : '' }} 
                {{ request()->is('admin.reminders.index*') ? 'c-show' : '' }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.reminder.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">

                    @can('view_reminders_management')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reminders.management') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/reminders-management') || request()->is('admin/reminders/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.reminders_managements') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_today_reminders')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reminders.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/reminders') || request()->is('admin/reminders/*') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.today_reminders') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_upcomming_reminders')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reminders.upcomming') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/upcomming-reminders') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.reminder.fields.upcomming_reminders') }}
                            </a>
                        </li>
                    @endcan

                    @can('view_overdue_reminders')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.reminders.overdue') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/overdue-reminders') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.reminder.fields.overdue_remiders') }}
                            </a>
                        </li>
                    @endcan

                    @can('reminders_history')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route('admin.remindersHistory.index') }}"
                                class="c-sidebar-nav-link {{ request()->is('admin/reminders-histories') ? 'c-active font-weight-bold' : '' }}">
                                <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                                </i>
                                {{ trans('global.reminders_history') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        {{-- @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif --}}

        @can('help_center')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.helpcenter.index') }}" class="c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-users"></i>
                    {{ trans('global.help_center') }}
                </a>
            </li>
        @endcan
        {{-- 
        @can('settings_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.settings.index') }}" class="c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-cogs"></i>
                    {{ trans('global.settings') }}
                </a>
            </li>
        @endcan --}}

        {{-- @can('data_migration')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.migration.index') }}" class="c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-database"></i>
                    {{ trans('global.data_migration') }}
                </a>
            </li>
        @endcan --}}

        {{-- @can('update_invoice_date')
            <li class="c-sidebar-nav-item">
                <a href="{{ route('admin.changeInvoice') }}" class="c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-recycle"></i>
                    {{ trans('global.update_invoice_date') }}
                </a>
            </li>
        @endcan --}}
    </ul>

</div>
