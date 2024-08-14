@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-2">
            <a href="{{ asset('reminder_demo.xlsx') }}" class="btn btn-danger btn-block">
                <i class="fa fa-download"></i> {{ trans('global.download_sample') }}
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="POST" action="{{ route('admin.import.leads_and_members') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('global.reminders_managements') }} {{ trans('global.list') }} </h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">{{ trans('global.upload') }}</label>
                                <input type="file" name="Reminder" id="upload_reminder" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">{{ trans('global.due_date').' ( '.trans('global.from').' ) ' }}</label>
                                        <input type="date" name="from" id="due_date" class="form-control" value="{{ date('Y-m-d') }}">   
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">{{ trans('global.due_date').' ( '.trans('global.to').' ) ' }}</label>
                                        <input type="date" name="to" id="due_date" class="form-control" value="{{ date('Y-m-d',strtotime('+7 Day')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info"><i class="fa fa-check-circle"></i> {{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection