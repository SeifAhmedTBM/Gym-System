@extends('layouts.admin')
@section('content')
    <div class="row cards">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-success text-white">
                    <h3 class="text-center">Allowed Freeze</h3>
                    <h3 class="text-center">{{ $freezeRequest->membership->service_pricelist->freeze_count .' '.App\Models\Setting::first()->freeze_duration}}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-info text-white">
                    <h3 class="text-center">Remained Freezes</h3>
                    <h3 class="text-center">{{ $freezeRequest->membership->service_pricelist->freeze_count - $diff.' '. App\Models\Setting::first()->freeze_duration}}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body bg-primary ">
                    <h3 class="text-center">Consumed Freezes</h3>
                    <h3 class="text-center">{{ $diff .' '. App\Models\Setting::first()->freeze_duration}}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.edit') }} {{ trans('cruds.freezeRequest.title_singular') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.freeze-requests.update', [$freezeRequest->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" value="{{ $freezeRequest->membership_id }}" id="membership_id" name="membership_id">
                <div class="form-group row">

                    <div class="col-md-6">
                        <label for="freeze">{{ trans('cruds.freezeRequest.fields.freeze') }}</label>
                        <input class="form-control {{ $errors->has('freeze') ? 'is-invalid' : '' }}" type="number"
                            name="freeze" id="freeze" value="{{ old('freeze', $freezeRequest->freeze) }}" step="1"
                            >
                        @if ($errors->has('freeze'))
                            <div class="invalid-feedback">
                                {{ $errors->first('freeze') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.freezeRequest.fields.freeze_helper') }}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="required"
                            for="start_date">{{ trans('cruds.freezeRequest.fields.start_date') }}</label>
                        <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text"
                            name="start_date" id="start_date" value="{{ old('start_date', $freezeRequest->start_date) }}"
                            required>
                        @if ($errors->has('start_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('start_date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.freezeRequest.fields.start_date_helper') }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="required"
                            for="end_date">{{ trans('cruds.freezeRequest.fields.end_date') }}</label>
                        <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text"
                            name="end_date" id="end_date" value="{{ old('end_date', $freezeRequest->end_date) }}"
                            required readonly>
                        @if ($errors->has('end_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('end_date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.freezeRequest.fields.end_date_helper') }}</span>
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#membership_id,#start_date,#freeze').on('change blur keyup', function() {

            var start_date = $('#start_date').val();

            var membership_id = $('#membership_id').val();

            var freeze = $('#freeze').val();

            var url = "{{ route('admin.getMembershipDetails', [':id', ':date', ':freeze']) }}",

                url = url.replace(':id', membership_id);

            url = url.replace(':date', start_date);

            url = url.replace(':freeze', freeze);

            var membership_freeze_request = $('#membership_freeze_request');

            var freeze_remained = $('#freeze_remained');

            var consumed_freeze = $('#consumed_freeze');


            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) {

                    $('.cards').slideDown();
                    $(membership_freeze_request).text(response.membership.service_pricelist
                        .freeze_count);
                    $(freeze_remained).text(response.freeze_remained);
                    $(consumed_freeze).text(response.consumed_freeze);

                    $('#end_date').val(response.end_date);

                    $('#freeze').on('change keyup', function() {
                        if (parseFloat($('#freeze').val()) > parseFloat(response.freeze_remained)) {
                            $('#freeze').removeClass('is-valid').addClass('is-invalid');
                            $('#freeze').val(0);
                        } else {
                            $('#freeze').removeClass('is-invalid').addClass('is-valid');
                            $('#end_date').val(response.end_date);
                        }
                    })
                }
            })
        });
    </script>
@endsection
