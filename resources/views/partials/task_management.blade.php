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
                // [
                //     'can' => 'task_access',
                //     'title' => 'My Created Tasks',
                //     'imageUrl' => 'tasks_icons.png',
                //     'linkUrl' => 'admin.tasks.created-tasks',
                //     'type' => '',
                // ],
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
