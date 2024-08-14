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
                    'can' => 'employee_access',
                    'title' => 'Employees List',
                    'imageUrl' => 'empolyees.png',
                    'linkUrl' => 'admin.employees.index',
                    'type' => '',
                ],
    
                [
                    'can' => 'view_schedule_template',
                    'title' => 'Schedule Templates',
                    'imageUrl' => 'schedule templates.png',
                    'linkUrl' => 'admin.schedule-templates.index',
                    'type' => '',
                ],
    
                [
                    'can' => 'view_transfer_sales_data',
                    'title' => 'Transfer Sales Data',
                    'imageUrl' => 'transfer sales data.png',
                    'linkUrl' => 'admin.transfer_sales_data.index',
                    'type' => '',
                ],
                [
                    'can' => 'view_attendance_settings',
                    'title' => 'Attendance Settings',
                    'imageUrl' => 'delay rules.png',
                    'linkUrl' => 'admin.attendance-settings.index',
                    'type' => '',
                ],
                [
                    'can' => 'bonu_access',
                    'title' => 'Employees Bonus List',
                    'imageUrl' => 'employee bonuses.png',
                    'linkUrl' => 'admin.bonus.index',
                    'type' => '',
                ],
                [
                    'can' => 'deduction_access',
                    'title' => 'Employees Deductions List',
                    'imageUrl' => 'employee deduction.png',
                    'linkUrl' => 'admin.deductions.index',
                    'type' => '',
                ],
                [
                    'can' => 'loan_access',
                    'title' => 'Employees Loans List',
                    'imageUrl' => 'employee loans.png',
                    'linkUrl' => 'admin.loans.index',
                    'type' => '',
                ],
                [
                    'can' => 'vacation_access',
                    'title' => 'Employees Vacations List',
                    'imageUrl' => 'employee vacations.png',
                    'linkUrl' => 'admin.vacations.index',
                    'type' => '',
                ],
                [
                    'can' => 'document_access',
                    'title' => 'Employees Documents List',
                    'imageUrl' => 'employee documents.png',
                    'linkUrl' => 'admin.documents.index',
                    'type' => '',
                ],
                [
                    'can' => 'view_employee_attendances',
                    'title' => 'Employees Attendance',
                    'imageUrl' => 'employee attendances.png',
                    'linkUrl' => 'admin.employee_attendances',
                    'type' => '',
                ],
                [
                    'can' => 'view_payroll_page',
                    'title' => 'Payroll',
                    'imageUrl' => 'payroll.png',
                    'linkUrl' => 'admin.payroll.get',
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
