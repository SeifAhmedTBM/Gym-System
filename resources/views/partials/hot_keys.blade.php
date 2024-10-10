@extends('layouts.admin')
@section('styles')
    <style>
        .dark-theme .card {
            transition: all .1s ease-in-out;
            --cui-card-bg: #515050;
            --cui-card-border-color: rgb(47 47 47);
            --cui-card-cap-bg: #414244;
            border-radius: 11px;
        }

        .dark-theme .card:hover {
            transform: scale(1.01);
            --cui-card-bg: #5ab5b9;
            box-shadow: inset 0 0 10px #fff border: 2px solid #c0c0c0c7;
        }
    </style>
@endsection
@section('content')
    <?php
    $menuItems = [
        [
            'items' => [
                [
                    'can' => 'lead_create',
                    'title' => 'Add Guestcard',
                    'imageUrl' => 'guestcard_icon.png',
                    'linkUrl' => 'admin.leads.create',
                    'type' => '',
                ],
    
                [
                    'can' => 'member_create',
                    'title' => 'Add Member',
                    'imageUrl' => 'members-icons.png',
                    'linkUrl' => 'admin.members.create',
                    'type' => '',
                ],
    
                [
                    'can' => 'membership_create',
                    'title' => 'Add Membership',
                    'imageUrl' => 'membership_icon.png',
                    'linkUrl' => 'admin.memberships.create',
                    'type' => '',
                ],
                [
                    'can' => 'freeze_request_create',
                    'title' => 'Add Freeze',
                    'imageUrl' => 'freeze_icon.png',
                    'linkUrl' => 'admin.freeze-requests.create',
                    'type' => '',
                ],
                [
                    'can' => 'membership_attendance_create',
                    'title' => 'Take Membership Attendance',
                    'imageUrl' => 'attendance.png',
                    'linkUrl' => 'admin.membership-attendances.create',
                    'type' => '',
                ],
                [
                    'can' => 'membership_attendance_create',
                    'title' => 'Take sessions/clasess Attendance',
                    'imageUrl' => 'attendance.png',
                    'linkUrl' => 'attendance_take.index',
                    'type' => '',
                ],
                [
                    'can' => 'refund_create',
                    'title' => 'Add Refund Request',
                    'imageUrl' => 'refund_icon.png',
                    'linkUrl' => 'admin.refunds.create',
                    'type' => '',
                ],
                [
                    'can' => 'external_payment_create',
                    'title' => 'Add Other revenue',
                    'imageUrl' => 'other_icons.png',
                    'linkUrl' => 'admin.external-payments.create',
                    'type' => '',
                ],
                [
                    'can' => 'expense_create',
                    'title' => 'Add Expense',
                    'imageUrl' => '15.png',
                    'linkUrl' => 'admin.expenses.create',
                    'type' => '',
                ],
                [
                    'can' => 'view_daily_report',
                    'title' => 'Daily Report',
                    'imageUrl' => 'daily-report_icon.png',
                    'linkUrl' => 'admin.reports.daily.report',
                    'type' => '',
                ],
                [
                    'can' => 'view_monthly_report',
                    'title' => 'Monthly report',
                    'imageUrl' => 'daily-report_icon.png',
                    'linkUrl' => 'admin.reports.monthly.report',
                    'type' => '',
                ],
                [
                    'can' => 'due_payments_report',
                    'title' => 'Due Payments Report',
                    'imageUrl' => 'due.png',
                    'linkUrl' => 'admin.reports.due-payments-report',
                    'type' => '',
                ],
                // [
                //     'can' => 'sms_access',
                //     'title' => 'SMS',
                //     'imageUrl' => 'sms.png',
                //     'linkUrl' => 'admin.marketing.sms.index',
                //     'type' => '',
                // ],
                [
                    'can' => 'employee_access',
                    'title' => 'Employee Attendance',
                    'imageUrl' => 'empolyees.png',
                    'linkUrl' => 'admin.employee_attendances',
                    'type' => '',
                ],
                [
                    'can' => 'view_transfer_sales_data',
                    'title' => 'Transfer Sales Data ',
                    'imageUrl' => 'empolyees.png',
                    'linkUrl' => 'admin.transfer_sales_data.index',
                    'type' => '',
                ],
                [
                    'can' => 'all_tasks_access',
                    'title' => 'Tasks List',
                    'imageUrl' => 'tasks_icons.png',
                    'linkUrl' => 'admin.tasks.index',
                    'type' => '',
                ],
                [
                    'can' => 'task_access',
                    'title' => 'My Tasks',
                    'imageUrl' => 'tasks_icons.png',
                    'linkUrl' => 'admin.tasks.my-tasks',
                    'type' => '',
                ],
                [
                    'can' => 'view_transfer_sales_data',
                    'title' => 'Notifications',
                    'imageUrl' => 'smartphone.png',
                    'linkUrl' => 'admin.notification.index',
                    'type' => '',
                ],
               

            ],
        ],
    ];
    ?>



    <div class="row">
        <div class="col-md-12">
            @foreach ($menuItems as $section)
                <div class="row">
                    @foreach ($section['items'] as $item)
                        @can($item['can'])
                            <div class="col-md-3">
                                <div class="card">
                                    <a class="text-decoration-none text-success" href="{{ route($item['linkUrl']) }}">
                                        <center>
                                            <img src="{{ asset('images/dashboard/' . $item['imageUrl']) }}" width="120"
                                                height="120" alt="" style="padding: 16px;">
                                            <br>
                                            <h6 style="color:#dadada;font-weight:bold;">{{ $item['title'] }}</h6>
                                        </center>
                                    </a>
                                </div>
                            </div>
                        @endcan
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    {{-- {{route($item['linkUrl'])}} --}}
@endsection
