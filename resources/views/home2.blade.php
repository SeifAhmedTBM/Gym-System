@extends('layouts.admin')
@section('styles')
<style>
.dark-theme .card{
    transition: all .1s ease-in-out; 
    --cui-card-bg: #515050;
    --cui-card-border-color: rgb(47 47 47);
    --cui-card-cap-bg: #414244;
    border-radius: 11px;
  }

  .dark-theme .card:hover{
    transform: scale(1.01);
    --cui-card-bg: #b6931c;
    box-shadow: inset 0 0 10px #fff
    border: 2px solid #c0c0c0c7;
  }
  </style>
@endsection
@section('content')
    <?php
    $menuItems = [
        [
            'label' => 'Hotkeys',
            'items' => [
                [
                    'title' => 'Add Guestcard',
                    'imageUrl' => 'guestcard_icon.png',
                    'linkUrl' => 'admin.leads.create',
                    'type' => '' ,
                ],
    
                [
                    'title' => 'Add Member',
                    'imageUrl' => 'members-icons.png',
                    'linkUrl' => 'admin.students.create',
                    'type'  => ''
                ],
    
                [
                    'title' => 'Add Membership',
                    'imageUrl' => 'membership_icon.png',
                    'linkUrl' => 'admin.instructor.create',
                    'type'  => ''
                ],
                [
                    'title' => 'Add Freeze',
                    'imageUrl' => 'freeze_icon.png',
                    'linkUrl' => 'admin.classrooms.create',
                    'type'  => ''
                ],
                [
                    'title' => 'Take Membership Attendance',
                    'imageUrl' => 'attendance.png',
                    'linkUrl' => 'admin.expenses.create',
                    'type'  => ''
                ],
                [
                    'title' => 'Add Refund Request',
                    'imageUrl' => 'refund_icon.png',
                    'linkUrl' => 'admin.external-payments.create',
                    'type'  => ''
                ],
                [
                    'title' => 'Add Other revenue',
                    'imageUrl' => 'other_icons.png',
                    'linkUrl' => 'admin.external-payments.create',
                    'type'  => ''
                ],
                [
                    'title' => 'Daily Report',
                    'imageUrl' => 'daily-report_icon.png',
                    'linkUrl' => 'admin.external-payments.create',
                    'type'  => ''
                ],
                [
                    'title' => 'Due Payments Report',
                    'imageUrl' => 'due.png',
                    'linkUrl' => 'admin.external-payments.create',
                    'type'  => ''
                ],
                [
                    'title' => 'My Tasks',
                    'imageUrl' => 'tasks_icons.png',
                    'linkUrl' => 'admin.external-payments.create',
                    'type'  => ''
                ],
            ],
        ]    
    ];
    ?>


<div class="row">
    {{-- <div class="col-md-1">
        <a href="{{route('admin.home')}}">
        <img src="{{ asset('images/dashboard/hot-sale.png')}}" width="80" height="80"
        alt="" style="padding: 16px;
        /* border: 1px solid white; */
        border-radius: 45px;
        background-color: #1dadeb99;"> 
        </a>
        <br>
        <br>
        <a href="{{route('admin.home')}}">
        <img src="{{ asset('images/dashboard/gear.png')}}" width="80" height="80"
        alt="" style="padding: 16px;"> 
        </a>
        <br>
        <img src="{{ asset('images/dashboard/help-desk.png')}}" width="80" height="80"
        alt="" style="padding: 16px;">
        <br>
        <img src="{{ asset('images/dashboard/logout.png')}}" width="80" height="80"
        alt="" style="padding: 16px;"> 
    </div> --}}
    <div class="col-md-12">
        @foreach ($menuItems as $section)
        <div class="row">
            @foreach ($section['items'] as $item)
                <div class="col-md-3">
                    <div class="card">
                        <a class="text-decoration-none text-success" href="#">
                            <center>
                                <img src="{{ asset('images/dashboard/' . $item['imageUrl']) }}" width="120" height="120"
                                    alt="" style="padding: 16px;">
                                <br>
                                <h6 style="color:#dadada;font-weight:bold;">{{ $item['title'] }}</h6>
                            </center>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
    </div> 
</div>
@endsection
