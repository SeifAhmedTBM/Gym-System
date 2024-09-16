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
            box-shadow: 0 0 20px #5ab5b9;
            /* box-shadow: inset 0 0 10px #fff border: 2px solid #c0c0c0c7; */
        }
    </style>
@endsection
@section('content')
    <?php
    $menuItems = [
        [
            'items' => [
                [
                    'can'       => 'branch_access',
                    'title'     => 'Branches List',
                    'imageUrl'  => '03.png',
                    'linkUrl'   => 'admin.branches.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'status_access',
                    'title'     => 'Statuses List',
                    'imageUrl'  => '01.png',
                    'linkUrl'   => 'admin.statuses.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'source_access',
                    'title'     => 'Sources List',
                    'imageUrl'  => '02.png',
                    'linkUrl'   => 'admin.sources.index',
                    'type'      => '',
                ],
    
                [
                    'can'       => 'address_access',
                    'title'     => 'Addresses List',
                    'imageUrl'  => '03.png',
                    'linkUrl'   => 'admin.addresses.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'expenses_category_access',
                    'title'     => 'Expenses Categories List',
                    'imageUrl'  => '04.png',
                    'linkUrl'   => 'admin.expenses-categories.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'external_payment_category_access',
                    'title'     => 'Other Revenue Categories List',
                    'imageUrl'  => '12.png',
                    'linkUrl'   => 'admin.external-payment-categories.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'service_type_access',
                    'title'     => 'Service Types List',
                    'imageUrl'  => '06.png',
                    'linkUrl'   => 'admin.service-types.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'account_access',
                    'title'     => 'Accounts List',
                    'imageUrl'  => '08.png',
                    'linkUrl'   => 'admin.accounts.index',
                    'type'      => '',
                ],
                //
                [
                    'can'       => 'service_access',
                    'title'     => 'Service  List',
                    'imageUrl'  => '07.png',
                    'linkUrl'   => 'admin.services.index',
                    'type'      => '',
                ],
                //
                [
                    'can'       => 'pricelist_access',
                    'title'     => 'Price Lists',
                    'imageUrl'  => '08.png',
                    'linkUrl'   => 'admin.pricelists.index',
                    'type'      => '',
                ],
                //
                [
                    'can'       => 'refund_reason_access',
                    'title'     => 'Refund Reasons List',
                    'imageUrl'  => '09.png',
                    'linkUrl'   => 'admin.refund-reasons.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'sales_tier_access',
                    'title'     => 'Sales Tiers List',
                    'imageUrl'  => '010.png',
                    'linkUrl'   => 'admin.sales-tiers.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'sales_tier_access',
                    'title'     => 'Roles',
                    'imageUrl'  => 'roles.png',
                    'linkUrl'   => 'admin.roles.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'session_list_access',
                    'title'     => 'Session List',
                    'imageUrl'  => 'roles.png',
                    'linkUrl'   => 'admin.session-lists.index',
                    'type'      => '',
                ],
                [
                    'can'       => 'timeslot_access',
                    'title'     => 'TimeSlots',
                    'imageUrl'  => 'clock.png',
                    'linkUrl'   => 'admin.timeslots.index',
                    'type'      => '',
                ],
                // [
                //     'can'       => 'schedule_main_access',
                //     'title'     => 'Schedule Main Group',
                //     'imageUrl'  => 'roles.png',
                //     'linkUrl'   => 'admin.schedule-main-groups.index',
                //     'type'      => '',
                // ],
                // [
                //     'can'       => 'schedule_main_access',
                //     'title'     => 'Schedule Main',
                //     'imageUrl'  => 'roles.png',
                //     'linkUrl'   => 'admin.schedule-mains.index',
                //     'type'      => '',
                // ],
                
                [
                    'can'       => 'schedule_access',
                    'title'     => 'Schedules',
                    'imageUrl'  => 'roles.png',
                    'linkUrl'   => 'admin.schedules.index',
                    'type'      => '',
                ],
              
                [
                    'can'       => 'sports_access',
                    'title'     => 'Sports',
                    'imageUrl'  => 'sports.png',
                    'linkUrl'   => 'admin.sports.index',
                    'type'      => '',
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
