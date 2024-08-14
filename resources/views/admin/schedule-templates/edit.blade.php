@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <i class="fa fa-edit"></i> {{ trans('global.edit') }} <b>" {{ $schedule_template->name }} "</b>
    </div>
    {!! Form::open(['method' => 'PUT', 'action' => ['Admin\ScheduleTemplateController@update', $schedule_template->id]]) !!}
    <div class="card-body">
        <div class="form-group">
            {!! Form::label('name', trans('global.name'), ['class' => 'required']) !!}
            {!! Form::text('name', $schedule_template->name, ['class' => 'form-control']) !!}
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
                @php
                if (request('date')) 
                {
                    $day = $schedule_template->days()->where('day', date(request('date').'-'. ($i < 10 ? '0' . $i : $i)))->first();
                }else {
                    $day = $schedule_template->days()->where('day', date('Y-m-' . ($i < 10 ? '0' . $i : $i)))->first();
                }
                    
                @endphp
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
                            @if (request('date'))
                                <label class="c-switch mt-2 c-switch-success shadow-none">
                                    <input 
                                    {{ $day->is_offday ? 'checked' : '' }}
                                    type="checkbox" onchange="disableDayTime(this)" data-day="{{ date(request('date').'-'. ($i < 10 ? '0' . $i : $i)) }}" name="offday[{{ date(request('date').'-'. ($i < 10 ? '0' . $i : $i)) }}]" value="1" class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            @else
                                <label class="c-switch mt-2 c-switch-success shadow-none">
                                    <input 
                                    {{ $day->is_offday ? 'checked' : '' }}
                                    type="checkbox" onchange="disableDayTime(this)" data-day="{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}" name="offday[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]" value="1" class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            @endif
                            
                        </td>
                        <td>
                            @if (request('date'))
                                {!! Form::time('from['.date(request('date').'-'. ($i < 10 ? '0' . $i : $i)).']', $day->from, [
                                    'class' => 'form-control',
                                    'id' => 'from_' . date(request('date').'-'.($i < 10 ? '0' . $i : $i)),
                                    $day->is_offday || $day->flexible ? 'disabled' : ''
                                ]) !!}
                            @else
                                {!! Form::time('from['.date('Y-m-' . ($i < 10 ? '0' . $i : $i)).']', $day->from, [
                                    'class' => 'form-control',
                                    'id' => 'from_' . date('Y-m-' . ($i < 10 ? '0' . $i : $i)),
                                    $day->is_offday || $day->flexible ? 'disabled' : ''
                                ]) !!}
                            @endif
                        </td>
                        <td>
                            @if (request('date'))
                                {!! Form::time('to['.date(request('date').'-'.($i < 10 ? '0' . $i : $i)).']', $day->to, ['class' => 'form-control', 'id' => 'to_' . date(request('date').'-'.($i < 10 ? '0' . $i : $i)),
                                $day->is_offday || $day->flexible ? 'disabled' : '']) !!}
                            @else
                                {!! Form::time('to['.date('Y-m-' . ($i < 10 ? '0' . $i : $i)).']', $day->to, ['class' => 'form-control', 'id' => 'to_' . date('Y-m-' . ($i < 10 ? '0' . $i : $i)),
                                $day->is_offday || $day->flexible ? 'disabled' : '']) !!}
                            @endif
                        </td>
                        <td width="200">
                            @if (request('date'))
                                {!! Form::number('working_hours['.date(request('date').'-'.($i < 10 ? '0' . $i : $i) ).']', $day->working_hours, ['class' => 'form-control', $day->flexible ? '' : 'disabled', 'id' => 'working_hours' . date('Y-m-' . ($i < 10 ? '0' . $i : $i))]) !!}
                            @else
                                {!! Form::number('working_hours['.date('Y-m-' . ($i < 10 ? '0' . $i : $i) ).']', $day->working_hours, ['class' => 'form-control', $day->flexible ? '' : 'disabled', 'id' => 'working_hours' . date('Y-m-' . ($i < 10 ? '0' . $i : $i))]) !!}
                            @endif
                        </td>
                        <td>
                            @if (request('date'))
                                <label class="c-switch c-switch-success mt-2 shadow-none" for="flexible[{{ date(request('date').'-'.($i < 10 ? '0' . $i : $i)) }}]">
                                    <input onchange="enableFlexibility(this)" type="checkbox" id="flexible[{{ date(request('date').'-'.($i < 10 ? '0' . $i : $i)) }}]" data-day="{{ date(request('date').'-'.($i < 10 ? '0' . $i : $i)) }}" name="flexible[{{ date(request('date').'-'.($i < 10 ? '0' . $i : $i)) }}]" value="1" {{ $day->flexible ? 'checked' : '' }} class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            @else
                                <label class="c-switch c-switch-success mt-2 shadow-none" for="flexible[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]">
                                    <input onchange="enableFlexibility(this)" type="checkbox" id="flexible[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]" data-day="{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}" name="flexible[{{ date('Y-m-' . ($i < 10 ? '0' . $i : $i)) }}]" value="1" {{ $day->flexible ? 'checked' : '' }} class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success">
            <i class="fa fa-check-circle"></i> {{ trans('global.update') }}
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
            } else {
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