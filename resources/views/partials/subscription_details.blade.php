<div class="card">
    <div class="card-header">
        ENTER DETAILS OF THE SUBSCRIPTION
    </div>
    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-3">
                <label for="service_type" class="required">{{ trans('cruds.service.fields.service_type') }}</label>
                <select name="service_type_id" id="service_type_id" class="form-control select2" onchange="getPricelists()">
                    <option value="{{ NULL }}" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                    @foreach (\App\Models\ServiceType::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="required" for="service_pricelist_id">{{ trans('cruds.member.fields.service') }}</label>
                <select onchange="getExpiry()" class="form-control select2 {{ $errors->has('service_pricelist_id') ? 'is-invalid' : '' }}"
                    name="service_pricelist_id" id="service_pricelist_id" required>
                    <option disabled selected hidden>Select Service</option>
                    {{-- @foreach ($pricelists as $pricelist)
                        <option value="{{ $pricelist->id }}" {{ old('service_pricelist_id') == $pricelist->id ? 'selected' : '' }}>
                            {{ $pricelist->name }}  </option>
                    @endforeach --}}
                </select>
                @if ($errors->has('service_pricelist_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('service_pricelist_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.service_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="start_date">{{ trans('cruds.membership.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text"
                    name="start_date" id="start_date" onblur="getExpiry()" value="{{ old('start_date') ?? date('Y-m-d') }}" required>
                @if ($errors->has('start_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.start_date_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="end_date">{{ trans('cruds.membership.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text"
                    name="end_date" id="end_date" value="{{ old('end_date') ?? date('Y-m-d') }}" required 
                    @cannot('editable_end_date') 
                     readonly
                    @endcannot
                    >
                @if ($errors->has('end_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-4">
                <label class="required" for="trainer_id">{{ trans('cruds.member.fields.trainer') }}</label>
                <select class="form-control  {{ $errors->has('trainer_id') ? 'is-invalid' : '' }}" name="trainer_id" id="trainer_id" required>
                    <option value="{{ NULL }}" disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                    @foreach ($trainers as $id => $entry)
                        <option value="{{ $id }}" {{ old('trainer_id') == $id ? 'selected' : '' }}>
                            {{ $entry }}</option>
                    @endforeach
                </select>
                @if ($errors->has('trainer_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('trainer_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.member.fields.trainer_helper') }}</span>
            </div>
            @if (config('domains')[config('app.url')]['sports_option'] == true)
                <div class="col-md-4 form-group">
                    <label for="sport_id" class="required">{{ trans('global.sport') }}</label>
                    <select name="sport_id" id="sport_id" class="select2">
                        <option disabled hidden selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach ($sports as $sport_id => $sport_name)
                            <option value="{{ $sport_id }}">{{ $sport_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if (config('domains')[config('app.url')]['add_to_class_in_invoice'] == true)
                <div class="col-md-4 form-group">
                    <label for="main_schedule_id">Session</label>
                    <select name="main_schedule_id[]" id="main_schedule_id" class="select2 multiple" multiple="multiple">
                        {{-- <option disabled hidden selected value="{{ NULL }}">{{ trans('global.pleaseSelect') }}</option> --}}
                        @foreach ($main_schedules as $main_schedule)
                            <option value="{{ $main_schedule->id }}">{{ ($main_schedule->session->name ?? '').' - '.($main_schedule->trainer->name ?? '').' - '.date('h:i A', strtotime($main_schedule->timeslot->from)) .' -'.$main_schedule->branch->name ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="form-group">
            <label class="" for="subscription_notes">Subscription Notes</label>
            <input class="form-control" id="subscription_notes" name="subscription_notes" value="{{old('subscription_notes')}}" />
            @if ($errors->has('subscription_notes'))
                <div class="invalid-feedback">
                    {{ $errors->first('subscription_notes') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.member.fields.trainer_helper') }}</span>
        </div>
    </div>
</div>