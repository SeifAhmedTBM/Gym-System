@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center">Ratings</h4>
                    <h4 class="text-center">{{ round($trainer->ratings_sum_rate  / $trainer->ratings_count) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center">Ratings Count</h4>
                    <h4 class="text-center">{{ $trainer->ratings_count }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered table-hover zero-configuration">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('cruds.member.title_singular') }}</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trainer->ratings as $rate)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rate->member->name ?? '' }}</td>
                                    <td>{!! App\Models\Rating::STARS[$rate->rate] !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
@endsection