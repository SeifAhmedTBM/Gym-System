@extends('layouts.admin')
@section('content')
    <form action="{{ route('admin.reports.reminders.action') }}" method="get">
        <div class="row form-group pt-2">
            <div class="col-md-3">
                <label for="date">{{ trans('global.date') }}</label>
                <div class="input-group">
                    <input type="date" class="form-control" name="date" value="{{ request('date') ?? date('Y-m-d') }}">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="form-group row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ trans('global.reminders_report') }}
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-bordered zero-configuration">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('cruds.user.title_singular') }}</th>
                                <th>{{ trans('global.today_reminders') }}</th>
                                <th>{{ trans('global.pending') }}</th>
                                <th>{{ trans('global.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sale->name }}</td>
                                    <td>{{ $sale->reminders_count + $sale->reminders_histories_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.reminders.index',[
                                                'user_id[]' => $sale->id,
                                                'due_date' => request('due_date') ?? date('Y-m-d')
                                            ]) }}">
                                            {{ $sale->reminders_count }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.remindersHistory.index',['user_id' => $sale->id]) }}">
                                            {{ $sale->reminders_histories_count }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection