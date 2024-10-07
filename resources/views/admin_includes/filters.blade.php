@php use Illuminate\Support\Carbon; @endphp

<button class="btn btn-primary" data-target="#filterModal" data-toggle="modal" type="button">
    <i class="fa fa-filter"></i> Filter
</button>

<!-- Filter Modal -->
<div class="modal fade bd-example-modal-lg" id="filterModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{ trans('global.filter') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'GET', 'route' => $route]) !!}
            <div class="modal-body">
                @isset($columns)
                    <div class="form-row">
                        @foreach ($columns as $column_name => $data)
                        @isset($data['filter_by'])
                            <input type="hidden" name="filter_by" value="{{ $data['filter_by'] }}">
                        @endisset
                        <div class="form-group col-md-4">
                            @if ($data['type'] != 'select' && $data['type'] != 'date' && $data['type'] != 'category')
                                {!! Form::label($column_name, Str::ucfirst($data['label']), ['class' => 'font-weight-bold']) !!}
                                @isset ($data['related_to'])
                                    <input type="{{ $data['type'] }}" value="{{ request()->get('relations')[$data['related_to']][$column_name] ?? '' }}" name="relations[{{ $data['related_to'] }}][{{ $column_name }}]" class="form-control" placeholder="Type here">
                                @else
                                    <input type="{{ $data['type'] }}" value="{{ request()->get($column_name) }}" name="{{ $column_name }}" class="form-control" placeholder="Type here">
                                @endisset
                            @elseif($data['type'] == 'select' && !isset($data['related_to']) &&$data['label']=='Select Member')
                            {{-- {{dd($column_name,request()->get($column_name))}} --}}
                            {!! Form::label($column_name, Str::ucfirst($data['label']), ['class' => 'font-weight-bold']) !!}
                            <select name="{{ $column_name }}[]" id="Select-Member" class="form-control select2 select2-ajax" {{ $column_name == 'trainer_id' && Auth()->user()->roles[0]->title == 'Trainer' ? 'readonly' : '' }} multiple>
                                @foreach ($data['data'] as $id => $col)
                                        <option  value="{{ $id }}" {{ (request()->get($column_name) ? in_array($id,request()->get($column_name)) : NULL) ? 'selected' : '' }}>{{ $col }}</option>
                                    @endforeach
                                </select>
                            @elseif($data['type'] == 'category')
                                {!! Form::label($column_name, Str::ucfirst($data['label']), ['class' => 'font-weight-bold']) !!}
                                <select name="expenses_category" id="{{ $column_name }}" class="form-control select2" {{ $column_name == 'trainer_id' && Auth()->user()->roles[0]->title == 'Trainer' ? 'readonly' : '' }} readonly>
                                    @foreach ($data['data'] as $id => $col)
                                        <option  value="{{ $id }}" selected >{{ $col }}</option>
                                    @endforeach
                                </select>
                             @elseif($data['type'] == 'select' && !isset($data['related_to']))
                            {{-- {{dd($column_name,request()->get($column_name))}} --}}
                            {!! Form::label($column_name, Str::ucfirst($data['label']), ['class' => 'font-weight-bold']) !!}
                            <select name="{{ $column_name }}[]" id="{{ $column_name }}" class="form-control select2" {{ $column_name == 'trainer_id' && Auth()->user()->roles[0]->title == 'Trainer' ? 'readonly' : '' }} multiple>
                                {{-- <option disabled selected>{{ $data['label'] }}</option> --}}
                                @foreach ($data['data'] as $id => $col)
                                        <option  value="{{ $id }}" {{ (request()->get($column_name) ? in_array($id,request()->get($column_name)) : NULL) ? 'selected' : '' }}>{{ $col }}</option>
                                    @endforeach
                                </select>
                            @elseif ($data['type'] == 'select' && isset($data['related_to'])&&$data['label']=='Account')
                                {!! Form::label($column_name, Str::ucfirst($data['label']), ['class' => 'font-weight-bold']) !!}
                                {{-- {{ dd(request()->all()) }} --}}
                                <select name="relations[{{ $data['related_to'] }}][{{ $column_name }}][]" id="{{ $column_name }}" class=" form-control select2" {{ $column_name == 'trainer_id' && Auth()->user()->roles[0]->title == 'Trainer' ? 'readonly' : '' }}>
                                    @foreach ($data['data'] as $id => $col)
                                        <option value="{{ $id }}" {{ (request()->get('relations') && isset(request()->get('relations')[$data['related_to']][$column_name]) ? in_array($id,request()->get('relations')[$data['related_to']][$column_name]) : '') ? 'selected' : '' }}>
                                            {{ $col }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif ($data['type'] == 'select' && isset($data['related_to']))
                                {!! Form::label($column_name, Str::ucfirst($data['label']), ['class' => 'font-weight-bold']) !!}
                                {{-- {{ dd(request()->all()) }} --}}
                                <select name="relations[{{ $data['related_to'] }}][{{ $column_name }}][]" id="{{ $column_name }}" class=" form-control select2" {{ $column_name == 'trainer_id' && Auth()->user()->roles[0]->title == 'Trainer' ? 'readonly' : '' }} multiple>
                                    @foreach ($data['data'] as $id => $col)
                                        <option value="{{ $id }}" {{ (request()->get('relations') && isset(request()->get('relations')[$data['related_to']][$column_name]) ? in_array($id,request()->get('relations')[$data['related_to']][$column_name]) : '') ? 'selected' : '' }}>
                                            {{ $col }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @foreach ($columns as $col_name => $col_data)
                        @if($col_data['type'] == 'date' && $col_data['from_and_to'] == true)
                            @if (isset($data['related_to']))
                                {!! Form::label($col_name, Str::ucfirst($col_data['label']), ['class' => 'font-weight-bold']) !!}
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <small class="text-success font-weight-bold">
                                                <i class="fa fa-exclamation-triangle"></i> From
                                            </small>
                                            <input type="date" value="{{ request()->get('relations')[$data['related_to']][$column_name]['from'] ?? '' }}" name="relations[{{ $data['related_to'] }}][{{ $column_name }}][from]" id="date_from" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <small class="text-success font-weight-bold">
                                                <i class="fa fa-exclamation-triangle"></i> To
                                            </small>
                                            <input type="date" value="{{ request()->get('relations')[$data['related_to']][$column_name]['to'] ?? '' }}" name="relations[{{ $data['related_to'] }}][{{ $column_name }}][to]" id="date_to" class="form-control">
                                        </div>
                                    </div>
                            @elseif(request()->has('month'))
                                @php
                                    $startdate = Carbon::createFromFormat('Y-m', request()->month)->startOfMonth()->format('Y-m-d');
                                    $enddate = Carbon::createFromFormat('Y-m', request()->month)->endOfMonth()->format('Y-m-d');
                                @endphp
                                {!! Form::label($col_name, Str::ucfirst($col_data['label']), ['class' => 'font-weight-bold']) !!}
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <small class="text-success font-weight-bold">
                                            <i class="fa fa-exclamation-triangle"></i> From
                                        </small>
                                        <input type="date" value="{{ $startdate }}" name="{{ $col_name }}[from]" id="date_from" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <small class="text-success font-weight-bold">
                                            <i class="fa fa-exclamation-triangle"></i> To
                                        </small>
                                        <input type="date" value="{{ $enddate }}" name="{{ $col_name }}[to]" id="date_to" class="form-control">
                                    </div>
                                </div>
                            @else
                                {!! Form::label($col_name, Str::ucfirst($col_data['label']), ['class' => 'font-weight-bold']) !!}
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <small class="text-success font-weight-bold">
                                                <i class="fa fa-exclamation-triangle"></i> From
                                            </small>
                                            <input type="date" value="{{ request()->get($col_name)['from'] ?? '' }}" name="{{ $col_name }}[from]" id="date_from" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <small class="text-success font-weight-bold">
                                                <i class="fa fa-exclamation-triangle"></i> To
                                            </small>
                                            <input type="date" value="{{ request()->get($col_name)['to'] ?? '' }}" name="{{ $col_name }}[to]" id="date_to" class="form-control">
                                        </div>
                                    </div>
                            @endif

                        @elseif($col_data['type'] == 'date' && $col_data['from_and_to'] == false)
                            {!! Form::label($col_name, Str::ucfirst($col_data['label']), ['class' => 'font-weight-bold']) !!}
                            <div class="form-group">
                                <input type="date" value="{{ request()->get($col_name)['to'] ?? '' }}" name="{{ $col_name }}" id="{{ Str::ucfirst($col_data['label']) }}" class="form-control">
                            </div>
                        @endif
                    @endforeach
                @else
                <div class="text-center alert alert-danger font-weight-bold">
                    Please , send " <code>columns</code> " parameter to the include directive
                </div>
                @endisset
            </div>
            <div class="modal-footer">

                <a href="{{ route($route) }}" class="btn btn-warning @if(isset($status) && $status) d-none @endif ">
                    <i class="fa fa-arrow-circle-left"></i> Reset
                </a>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times"></i> {{ trans('global.cancel') }}
                </button>
                @isset($columns)
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check"></i> {{ trans('global.filter') }}
                    </button>
                @endisset
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>



