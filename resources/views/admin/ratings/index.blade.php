@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ trans('global.trainer_ratings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped table-bordered table-hover zero-configuration">
                                <thead>
                                    <th>#</th>
                                    <th>{{ trans('cruds.user.fields.name') }}</th>
                                    <th>Rate</th>
                                    <th>
                                        {{ trans('global.action') }}
                                    </th>
                                </thead>
                                <tbody>
                                    @foreach ($trainers as $trainer)
                                        <tr>    
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $trainer->name }}</td>
                                            <td>{!! App\Models\Rating::STARS[round($trainer->ratings_sum_rate  / $trainer->ratings_count)] !!}</td>
                                            <td>
                                                <a href="{{ route('admin.ratings.show',$trainer->id) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> {{ trans('global.show') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection