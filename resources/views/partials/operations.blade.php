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
            --cui-card-bg: #5ab5b9 !important;
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
                    'can' => 'lead_access',
                    'title' => 'Leads List',
                    'imageUrl' => '1.png',
                    'linkUrl' => 'admin.leads.index',
                    'type' => '',
                ],
    
                [
                    'can' => 'member_access',
                    'title' => 'Members List',
                    'imageUrl' => '2.png',
                    'linkUrl' => 'admin.members.index',
                    'type' => '',
                ],
    
                [
                    'can' => 'membership_access',
                    'title' => 'Memberships List',
                    'imageUrl' => '3.png',
                    'linkUrl' => 'admin.memberships.index',
                    'type' => '',
                ],
                [
                    'can' => 'assigned_membership',
                    'title' => 'Memberships List ( Coach Assigned )',
                    'imageUrl' => '3.png',
                    'linkUrl' => 'admin.assigned-memberships',
                    'type' => '',
                ],
    
                //
                [
                    'can' => 'membership_attendance_access',
                    'title' => 'Memberships Attendance List',
                    'imageUrl' => 'Attends-List.png',
                    'linkUrl' => 'admin.membership-attendances.index',
                    'type' => '',
                ],

                [
                    'can' => 'membership_attendance_access',
                    'title' => 'Free Pt Requests',
                    'imageUrl' => 'Attends-List.png',
                    'linkUrl' => 'admin.free-requests.index',
                    'type' => '',
                ],

                //
                [
                    'can' => 'freeze_request_access',
                    'title' => 'Freeze List',
                    'imageUrl' => '7.png',
                    'linkUrl' => 'admin.freeze-requests.index',
                    'type' => '',
                ],
                //
                [
                    'can' => 'invoice_access',
                    'title' => 'Invoices List',
                    'imageUrl' => '9.png',
                    'linkUrl' => 'admin.invoices.index',
                    'type' => '',
                ],
    
                [
                    'can' => 'payment_access',
                    'title' => 'Payments List',
                    'imageUrl' => 'Payments.png',
                    'linkUrl' => 'admin.payments.index',
                    'type' => '',
                ],
                [
                    'can' => 'refund_access',
                    'title' => 'Refunds List',
                    'imageUrl' => '11.png',
                    'linkUrl' => 'admin.refunds.index',
                    'type' => '',
                ],
                [
                    'can' => 'settlement_invoice_access',
                    'title' => 'Settellements List',
                    'imageUrl' => '12.png',
                    'linkUrl' => 'admin.invoices.settlement',
                    'type' => '',
                ],
                [
                    'can' => 'external_payment_access',
                    'title' => 'Other Payments List',
                    'imageUrl' => '14.png',
                    'linkUrl' => 'admin.external-payments.index',
                    'type' => '',
                ],
                [
                    'can' => 'expense_access',
                    'title' => 'Expenses List',
                    'imageUrl' => '15.png',
                    'linkUrl' => 'admin.expenses.index',
                    'type' => '',
                    
                ],
                [
                    'can' => 'member_access',
                    'title' => 'Member Suggestions',
                    'imageUrl' => '2.png',
                    'linkUrl' => 'admin.member-suggestion.index',
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
@endsection
