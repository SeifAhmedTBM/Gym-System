@extends('layouts.admin')
@section('styles')
    <style>
        .list-unstyled li {
            font-size: 18px;
            margin-bottom: 7px;
            font-weight: bold;
            padding-left: 20px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>System reports</h3>
                </div>
                <div class="card-body">
                    <div class="row py-3">
                        <div class="col-md-4">
                            <h3>Members Reports</h3>
                            <ul class="list-unstyled mt-3">
                                @can('view_active_members')
                                    <li>
                                        <a href="{{ route('admin.members.active') }}">{{ trans('global.active_members') }}</a>
                                    </li>
                                @endcan
                                @can('view_onhold_members')
                                    <li>
                                        <a href="{{ route('admin.members.onhold') }}">On hold Members</a>
                                    </li>
                                @endcan
                                {{-- @can('invitations_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.invitations.index') }}">{{ trans('global.invitations_report') }}</a>
                                    </li>
                                @endcan --}}

                                @can('view_customer_invitations_report')
                                    <li>
                                        <a href="{{ route('admin.reports.customer-invitation') }}">
                                            Customer Invitation Report
                                        </a>
                                    </li>
                                @endcan

                                @can('view_inactive_members')
                                    <li>
                                        <a href="{{ route('admin.members.inactive') }}">{{ trans('global.inactive_members') }}</a>
                                    </li>
                                @endcan

                                @can('view_dayuse_members_report')
                                    <li>
                                        <a href="{{ route('admin.reports.dayuse') }}">Dayuse Members</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h3>Membership Reports</h3>
                            <ul class="list-unstyled mt-3">
                                {{-- @can('expired_membership_access') --}}
                                {{-- <li>
                                    <a href="{{ route('admin.membership.expired') }}">Main {{ trans('global.expired_memberships') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.membership.expiredExtra') }}">PT {{ trans('global.expired_memberships') }}</a>
                                </li> --}}
                                @can('view_main_expired_memberships_report')
                                    <li>
                                        <a href="{{ route('admin.reports.main-expired',[
                                            'relations[memberships][end_date][from]'    => date('Y-m-01'),
                                            'relations[memberships][end_date][to]'      => date('Y-m-t'),
                                        ]) }}">
                                            Main {{ trans('global.expired_memberships') }}
                                        </a>
                                    </li>
                                @endcan

                                @can('view_pt_expired_memberships_report')
                                    <li>
                                        <a href="{{ route('admin.reports.pt-expired',[
                                            'relations[memberships][end_date][from]'    => date('Y-m-01'),
                                            'relations[memberships][end_date][to]'      => date('Y-m-t'),
                                        ]) }}">
                                            PT {{ trans('global.expired_memberships') }}
                                        </a>
                                    </li>
                                @endcan

                                @can('expiring_membership_access')
                                    <li>
                                        <a href="{{ route('admin.membership.expiring-expired',[
                                            'end_date[from]'    => date('Y-m-01'),
                                            'end_date[to]'      => date('Y-m-t'),
                                        ]) }}">
                                            Expiring & Expired Memberships
                                        </a>
                                    </li>
                                @endcan

                                @can('view_freezes')
                                    <li>
                                        <a href="{{ route('admin.freeze.index') }}">{{ trans('global.freezes') }}</a>
                                    </li>
                                @endcan
                                {{--
                            @can('view_schedule_timeline')
                                <li>
                                    <a href="{{ route('admin.reports.schedule.timeline') }}">{{ trans('global.schedule_timeline') }}</a>
                                </li>
                            @endcan
                            @can('view_expired_memberships_attendances')
                                <li>
                                    <a href="{{ route('admin.reports.expired-membership-attendances') }}">{{ trans('global.exp_attendances') }}</a>
                                </li>
                            @endcan --}}
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h3>Sales Reports</h3>
                            <ul class="list-unstyled mt-3">
                                <li>
                                    <a href="{{ route('admin.reports.sales-daily-report') }}">
                                        Sales Branch Report
                                    </a>
                                </li>
                                @can('view_sales_report')
                                    <li>
                                        <a href="{{ route('admin.reports.sales-report') }}">{{ trans('global.sales_report') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('view_sales_report')
                                    <li>
                                        <a href="{{ route('admin.reports.previous-month-reports') }}">{{ trans('global.analysis_report') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('view_guest_log_report')
                                    <li>
                                        <a href="{{ route('admin.reports.guest-log-report') }}">Guest Log Report</a>
                                    </li>
                                @endcan

                                @can('view_daily_task_report')
                                    <li>
                                        <a href="{{ route('admin.reports.daily-task-report') }}">Sales Task Report</a>
                                    </li>
                                @endcan

                                @can('view_actions_report')
                                    <li>
                                        <a href="{{ route('admin.reports.actions-report') }}">Actions Report</a>
                                    </li>
                                @endcan

                                @can('view_reminders_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.reminders') }}">{{ trans('global.reminders_report') }}</a>
                                    </li>
                                @endcan
                                @can('view_reminders_actions_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.reminders.action') }}">{{ trans('global.reminders_action_report') }}</a>
                                    </li>
                                @endcan

                                {{-- <li>
                                    <a href="{{ route('admin.reports.revenue') }}">{{ trans('global.revenue') }}</a>
                                </li> --}}
                                {{-- @can('view_revenue_report')
                                <li>
                                    <a href="{{ route('admin.reports.revenue') }}">{{ trans('global.revenue') }}</a>
                                </li>
                            @endcan
                            @can('view_coaches_report')
                                <li>
                                    <a href="{{ route('admin.reports.coaches') }}">{{ trans('global.coaches') }}</a>
                                </li>
                            @endcan --}}
                                {{-- @can('view_freelancers_report')
                                <li>
                                    <a href="{{ route('admin.reports.freelancers-report') }}">{{ trans('global.freelancers_report') }}</a>
                                </li>
                            @endcan
                            @can('view_current_memberships_report')
                                <li>
                                    <a href="{{ route('admin.reports.current-memberships') }}">{{ trans('global.current_memberships') }}</a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('admin.reports.trainerCommissions.report') }}">{{ trans('global.trainer_commissions') }}</a>
                            </li> --}}

                            </ul>
                        </div>
                    </div>

                    <div class="row py-3">
                        <div class="col-md-4">
                            <h3>Markting Reports</h3>
                            <ul class="list-unstyled mt-3">
                                @can('view_leads_source_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.leadsSource') }}">{{ trans('global.leads_source_report') }}</a>
                                    </li>
                                @endcan
                                {{-- @can('view_member_source_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.membersSource') }}">{{ trans('global.members_source_report') }}</a>
                                    </li>
                                @endcan --}}
                                @can('view_offers_report')
                                    <li>
                                        <a href="{{ route('admin.reports.offers') }}">{{ trans('global.offers_report') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h3>Finance Analysis Reports</h3>
                            <ul class="list-unstyled mt-3">
                                @can('view_daily_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.daily.report') }}">{{ trans('global.daily_report') }}</a>
                                    </li>
                                @endcan
                                @can('view_monthly_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.monthly.report') }}">{{ trans('global.monthly_report') }}</a>
                                    </li>
                                @endcan
                                @can('view_yearly_finance_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.yearlyFinance.report') }}">{{ trans('global.yearly_finance_report') }}</a>
                                    </li>
                                @endcan
                                @can('view_monthly_finance_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.monthlyFinance.report') }}">{{ trans('global.monthly_finance_report') }}</a>
                                    </li>
                                @endcan
                                @can('due_payments_report')
                                    {{-- <li>
                                        <a
                                            href="{{ route('admin.reports.due-payments-report') }}">{{ trans('global.due_payments_report') }}</a>
                                    </li> --}}

                                    <li>
                                        <a href="{{ route('admin.reports.all-due-payments') }}">All Due Payments</a>
                                    </li>

                                @endcan

                                    @can('view_sales_report')
                                        <li>
                                            <a href="{{ route('admin.reports.sales_due_payments') }}">Sales Due Payments</a>

                                        </li>
                                    @endcan
                                    @can('due_payments_report')

                                        <li>
                                            <a href="{{ route('admin.reports.trainer_due_payments') }}">Trainer Due Payments</a>
                                        </li>
                                    @endcan

                                {{-- @can('refund_access')
                                <li>
                                    <a href="{{ route('admin.refunds.index') }}">{{ trans('cruds.refund.title') }}</a>
                                </li>
                            @endcan --}}
                                {{-- @can('view_refund_reasons_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.refundReasons.report') }}">{{ trans('global.refund_reason_report') }}</a>
                                    </li>
                                @endcan --}}
                                @can('view_other_revenue_categroies_report')
                                    <li>
                                        <a href="{{ route('admin.reports.external-payment-categories.report') }}">
                                            Other Revenue Categories Report</a>
                                    </li>
                                @endcan

                                @can('view_tax_accountant_report')
                                    <li>
                                        <a href="{{ route('admin.reports.tax-accountant') }}">Tax Accountant</a>
                                    </li>
                                @endcan
                                {{--
                                <li>
                                    <a href="{{ route('admin.reports.assigned-coaches.report') }}">Assigned Coaches </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.reports.sessions-revenue') }}">Sessions Revenue ( Heat map ) </a>
                                </li> --}}
                                @can('expense_access')
                                    <li>
                                        <a href="{{ route('admin.expenses_categories') }}">Expenses Categories</a>
                                    </li>
                                @endcan 
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h3>Trainers Reports</h3>
                            <ul class="list-unstyled mt-3">
                                @can('view_daily_trainer_report')
                                    <li>
                                        <a href="{{ route('admin.reports.trainer-daily-report') }}">
                                            Branch Trainer Report
                                        </a>
                                    </li>
                                @endcan

                                @can('view_fitness_manager_report')
                                    <li>
                                        <a href="{{ route('admin.reports.fitness-manager-report') }}">
                                            Fitness Managers Report
                                        </a>
                                    </li>
                                @endcan

                                @can('view_trainers_report')
                                    <li>
                                        <a href="{{ route('admin.reports.trainers-report') }}">
                                            {{ trans('global.trainers_report') }}
                                        </a>
                                    </li>
                                @endcan

                                @can('view_coaches_report')
                                    <li>
                                        <a href="{{ route('admin.reports.coaches') }}">Coaches Report</a>
                                    </li>
                                @endcan

                                @can('view_trainer_reminders_report')
                                    <li>
                                        <a href="{{ route('admin.reports.trainers-reminders') }}">
                                            Trainers Reminders
                                        </a>
                                    </li>
                                @endcan

                                @can('view_trainers_reminders_actions_report')
                                    <li>
                                        <a href="{{ route('admin.reports.trainers-reminder-actions') }}">
                                            Trainer Reminders Actions
                                        </a>
                                    </li>
                                @endcan

                                @can('view_trainer_reminders_histories_report')
                                    <li>
                                        <a href="{{ route('admin.reports.trainers-reminder-history-actions') }}">
                                            Trainer Reminders Histories
                                        </a>
                                    </li>
                                @endcan

                                @can('view_pt_attendances')
                                    <li>
                                        <a href="{{ route('admin.reports.pt-attendances-report') }}">
                                            PT Attendances Report
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </div>

                    

                </div>
            </div>
        </div>
    </div>
@endsection
