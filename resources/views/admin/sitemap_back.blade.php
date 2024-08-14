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
                                {{-- @can('view_inactive_members') --}}
                                <li>
                                    <a href="{{ route('admin.members.onhold') }}">On hold Members</a>
                                </li>
                                {{-- @endcan --}}
                                @can('invitations_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.invitations.index') }}">{{ trans('global.invitations_report') }}</a>
                                    </li>
                                @endcan

                                <li>
                                    <a href="{{ route('admin.reports.customer-invitation') }}">Customer Invitation
                                        Report</a>
                                </li>

                                @can('view_inactive_members')
                                    <li>
                                        <a
                                            href="{{ route('admin.members.inactive') }}">{{ trans('global.inactive_members') }}</a>
                                        <span class="badge badge-danger badge-sm">NEW !</span>
                                    </li>
                                @endcan

                                <li>
                                    <a href="{{ route('admin.reports.dayuse') }}">Dayuse Members</a>
                                    <span class="badge badge-danger badge-sm">NEW !</span>
                                </li>

                                <li>
                                    <a href="{{ route('admin.reports.daily-task-report') }}">Daily Task Report</a>
                                    <span class="badge badge-danger badge-sm">NEW !</span>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h3>Membership Reports</h3>
                            <ul class="list-unstyled mt-3">
                                @can('expired_membership_access')
                                    {{-- <li>
                                    <a href="{{ route('admin.membership.expired') }}">Main {{ trans('global.expired_memberships') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.membership.expiredExtra') }}">PT {{ trans('global.expired_memberships') }}</a>
                                </li> --}}
                                    <li>
                                        <a href="{{ route('admin.reports.expired') }}">Main
                                            {{ trans('global.expired_memberships') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.reports.expired-extra') }}">PT
                                            {{ trans('global.expired_memberships') }}</a>
                                    </li>
                                @endcan
                                @can('expiring_membership_access')
                                    <li>
                                        <a
                                            href="{{ route('admin.membership.expiring-expired') }}">{{ trans('global.expiring_memberships') }}</a>
                                    </li>
                                @endcan
                                @can('view_freezes')
                                    <li>
                                        <a href="{{ route('admin.freeze.index') }}">{{ trans('global.freezes') }}</a>
                                    </li>
                                @endcan
                                @can('view_reminders_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.reminders') }}">{{ trans('global.reminders_report') }}</a>
                                    </li>
                                @endcan
                                {{-- @can('view_reminders_actions_report')
                                <li>
                                    <a href="{{ route('admin.reports.reminders.action') }}">{{ trans('global.reminders_action_report') }}</a> <span class="badge badge-danger">NEW</span>
                                </li>
                            @endcan
                            @can('view_schedule_timeline')
                                <li>
                                    <a href="{{ route('admin.reports.schedule.timeline') }}">{{ trans('global.schedule_timeline') }}</a>
                                </li>
                            @endcan
                            @can('view_expired_memberships_attendances')
                                <li>
                                    <a href="{{ route('admin.reports.expired-membership-attendances') }}">{{ trans('global.exp_attendances') }}</a> <span class="badge badge-danger">NEW</span>
                                </li>
                            @endcan --}}
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h3>Sales Reports</h3>
                            <ul class="list-unstyled mt-3">
                                @can('view_sales_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.sales-report') }}">{{ trans('global.sales_report') }}</a>
                                    </li>
                                @endcan
                                <li>
                                    <a href="{{ route('admin.reports.revenue') }}">{{ trans('global.revenue') }}</a>
                                </li>
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
                                @can('view_trainers_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.trainers-report') }}">{{ trans('global.trainers_report') }}</a>
                                    </li>

                                    <li>
                                        <a href="{{ route('admin.reports.coaches') }}">Coaches Report</a>
                                    </li>
                                @endcan
                                {{-- @can('view_freelancers_report')
                                <li>
                                    <a href="{{ route('admin.reports.freelancers-report') }}">{{ trans('global.freelancers_report') }}</a>
                                </li>
                            @endcan
                            @can('view_current_memberships_report')
                                <li>
                                    <a href="{{ route('admin.reports.current-memberships') }}">{{ trans('global.current_memberships') }}</a> <span class="badge badge-danger">NEW</span>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('admin.reports.trainerCommissions.report') }}">{{ trans('global.trainer_commissions') }}</a> <span class="badge badge-danger">NEW</span>
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
                                @can('view_member_source_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.membersSource') }}">{{ trans('global.members_source_report') }}</a>
                                    </li>
                                @endcan
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
                                {{-- @can('refund_access')
                                <li>
                                    <a href="{{ route('admin.refunds.index') }}">{{ trans('cruds.refund.title') }}</a>
                                </li>
                            @endcan --}}
                                @can('view_refund_reasons_report')
                                    <li>
                                        <a
                                            href="{{ route('admin.reports.refundReasons.report') }}">{{ trans('global.refund_reason_report') }}</a>
                                    </li>
                                @endcan
                                <li>
                                    <a href="{{ route('admin.reports.external-payment-categories.report') }}">Other Revenue
                                        Categories Report</a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.reports.tax-accountant') }}">Tax Accountant</a>
                                    <span class="badge badge-danger badge-sm">NEW !</span>
                                </li>

                                <li>
                                    <a href="{{ route('admin.reports.assigned-coaches.report') }}">Assigned Coaches </a>
                                    <span class="badge badge-danger badge-sm">NEW !</span>
                                </li>

                                <li>
                                    <a href="{{ route('admin.reports.sessions-revenue') }}">Sessions Revenue ( Heat map ) </a>
                                    <span class="badge badge-danger badge-sm">NEW !</span>
                                </li>
                                {{-- @can('expense_access')
                                <li>
                                    <a href="{{ route('admin.expenses.index') }}">{{ trans('cruds.expense.title') }}</a>
                                </li>
                            @endcan --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
