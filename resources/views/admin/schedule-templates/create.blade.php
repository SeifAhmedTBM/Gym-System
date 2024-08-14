@extends('layouts.admin')
@section('content')
<form action="{{ route('admin.schedule-templates.create',request()->all()) }}" method="get">
    <div class="form-group">
        <label for="date">{{ trans('global.month') }}</label>
        <div class="input-group mb-3">
            <input type="month" name="date" class="form-control" value="{{ request('date') ? request('date') : date('Y-m') }}">
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit" id="button-addon2">{{ trans('global.submit') }}</button>
            </div>
        </div>
    </div>    
</form>

<div class="card">
    <div class="card-header font-weight-bold">
        <i class="fa fa-plus-circle"></i> {{ trans('global.add_schedule_template') }}
    </div>
    {!! Form::open(['method' => 'POST', 'action' => 'Admin\ScheduleTemplateController@store']) !!}
    <div class="card-body">
        <div class="form-group">
            {!! Form::label('name', trans('global.name'), ['class' => 'required']) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
            <small>{{ trans('global.schedule_name_hint') }}</small>
        </div>
        <table class="table text-center table-bordered table-outline">
            <thead>
                <tr>
                    <th>{{ trans('global.days') }}</th>
                    <th>{{ trans('global.offday') }}</th>
                    <th>{{ trans('global.from') }}</th>
                    <th>{{ trans('global.to') }}</th>
                    <th>{{ trans('global.working_hours') }}</th>
                    <th>{{ trans('global.flexible') }}</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= Carbon\Carbon::parse(request('date') ? request('date') : date('Y-m'))->daysInMonth; $i++)
                    <tr>
                        <td>
                            @if (request('date'))
                                {!! Form::hidden('days[]', date(request('date').'-'. ($i < 10 ? '0' . $i : $i))) !!}
                                
                                <b class="d-block">{{ date(request('date').'-'.($i < 10 ? '0' . $i : $i)) }} </b>
                                <span class="badge badge-success px-2 py-2 mt-2">
                                    {{ Carbon\Carbon::parse(date(request('date').'-'.($i < 10 ? '0' . $i : $i)))->format('l') }}
                                </span>
                            @else
                                {!! Form::hidden('days[]', date('Y-m-'.($i < 10 ? '0' . $i : $i))) !!}
                                <b class="d-block">{{ date('Y-m-'.($i < 10 ? '0' . $i : $i)) }} </b>
                                <span class="badge badge-success px-2 py-2 mt-2">
                                    {{ Carbon\Carbon::parse(date('Y-m-'.($i < 10 ? '0' . $i : $i)))->format('l') }}
                                </span>
                            @endif
                            
                        </td>
                        <td>
                            <label class="c-switch c-switch-success shadow-none mt-2">
                                <input type="checkbox" onchange="disableDayTime(this)" data-day="{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}" name="offday[{{ ($i < 10 ? '0' . $i : $i) }}]" value="1" class="c-switch-input">
                                <span class="c-switch-slider shadow-none"></span>
                            </label>
                        </td>
                        <td>
                            {!! Form::time('from['.date('Y-m-' . ($i < 10 ? '0' . $i : $i)).']', '10:00', ['class' => 'form-control', 'id' => 'from_' . date('Y-m-' . ($i < 10 ? '0' . $i : $i))]) !!}
                        </td>
                        <td>
                            {!! Form::time('to['.date('Y-m-' . ($i < 10 ? '0' . $i : $i)).']', '18:00', ['class' => 'form-control', 'id' => 'to_' . date('Y-m-' . ($i < 10 ? '0' . $i : $i))]) !!}
                        </td>
                        <td width="200">
                            {!! Form::number('working_hours['.date('Y-m-' . ($i < 10 ? '0' . $i : $i) ).']', 8, ['class' => 'form-control', 'disabled', 'id' => 'working_hours' . date('Y-m-' . ($i < 10 ? '0' . $i : $i))]) !!}
                        </td>
                        <td>
                            <label class="c-switch c-switch-success mt-2 shadow-none" for="flexible[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]">
                                <input onchange="enableFlexibility(this)" type="checkbox" id="flexible[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]" data-day="{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}" name="flexible[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]" value="1" class="c-switch-input">
                                <span class="c-switch-slider shadow-none"></span>
                            </label>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-check-circle"></i> {{ trans('global.create') }}
        </button>
    </div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
    <script>
        function disableDayTime(checkBox) {
            let day_name = $(checkBox).data('day');
            if(checkBox.checked == true) {
                $("#from_"+day_name).attr('disabled', 'disabled');
                $("#to_"+day_name).attr('disabled', 'disabled');
            }else {
                $("#from_"+day_name).removeAttr('disabled');
                $("#to_"+day_name).removeAttr('disabled');
            }
        }

        function enableFlexibility(checkBox) {
            let day_name = $(checkBox).data('day');
            if(checkBox.checked == true) {
                $("#from_"+day_name).attr('disabled', 'disabled');
                $("#to_"+day_name).attr('disabled', 'disabled');
                $("#working_hours"+day_name).removeAttr('disabled');
            }else {
                $("#from_"+day_name).removeAttr('disabled');
                $("#to_"+day_name).removeAttr('disabled');
                $("#working_hours"+day_name).attr('disabled', 'disabled');
            }
        }
        
    </script>
@endsection