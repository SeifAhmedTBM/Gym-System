@extends('layouts.admin')
@section('content')
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fa fa-plus-circle"></i> {{ trans('global.add') . ' ' . trans('global.trainer_service') }}
    </div>
    {!! Form::open(['method' => 'POST', 'url' => route('admin.trainer-services.store')]) !!}
    <div class="card-body">
        <div class="form-group">
            {!! Form::label('user_id', trans('global.trainer'), ['class' => 'required']) !!}
            <select required name="user_id" id="user_id" class="select2">
                <option disabled hidden>{{ trans('global.pleaseSelect') }}</option>
                @foreach ($trainers as $trainer_id => $trainer_name)
                    <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            {!! Form::label('services', trans('cruds.service.title_singular'), ['class' => 'required']) !!}
            {!! Form::select('services[]', $services, null, ['class' => 'select2', 'multiple', 'onchange' => 'getService(this)', 'required']) !!}
        </div>
        <div class="appendServiceData pt-2"></div>
    </div>
    <div class="card-footer text-right">
        <button type="submit" class="btn btn-success">
            <i class="fa fa-check-circle"></i> {{ trans('global.submit') }}
        </button>
    </div>
    {!! Form::close() !!}
</div>
@endsection


@section('scripts')
    <script>
        function getService(button) {
            let services = $(button).val();
            $(".appendServiceData").html('');
            $.ajax({
                method : "GET",
                url : "{{ route('admin.single-trainer-service.get') }}",
                data : {services : services},
                success: function(response) {
                    console.log(response)
                    for(let i = 0 ; i < response.length ; i++) {
                        $(".appendServiceData").append(`
                            <div class="row mt-3">
                                <div class="col-md-4 form-group">
                                    <label class="required" for="service_name">${response[i].name}</label>
                                    <input class="form-control" readonly value="${response[i].name}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="required" for="commission_type">{{ trans('global.commission_type') }}</label>
                                    <select required class="form-control" name="commission_type[]">
                                        <option value="fixed">{{ trans('global.fixed') }}</option>    
                                        <option value="percentage">{{ trans('global.percentage') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="required" for="commission">{{ trans('global.commission') }}</label>
                                    <input required type="number" class="form-control" name="commission[]">
                                </div>
                            </div>
                        `);
                    }
                }
            })
            
        }
    </script>
@endsection