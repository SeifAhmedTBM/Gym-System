@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.pricelist.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.pricelists.update", [$pricelist->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="form-group row">

                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.pricelist.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $pricelist->name) ?? '' }}"  required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.name_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="service_id">{{ trans('cruds.pricelist.fields.service') }}</label>
                    <select class="form-control select2 {{ $errors->has('service') ? 'is-invalid' : '' }}" name="service_id" id="service_id" required>
                        @foreach($services as $id => $entry)
                            <option value="{{ $id }}" {{ (old('service_id') ? old('service_id') : $pricelist->service->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('service'))
                        <div class="invalid-feedback">
                            {{ $errors->first('service') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.service_helper') }}</span>
                </div>
                @if($pricelist->service->service_type->id == $class_service_id)
                <div class="col-md-6">
                    <label class="required" for="max_count">{{ trans('cruds.pricelist.fields.max_count') }}</label>
                    <input class="form-control {{ $errors->has('max_count') ? 'is-invalid' : '' }}" type="text" name="max_count" id="max_count" value="{{ old('max_count', $pricelist->max_count) }}"  required>
                    @if($errors->has('max_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('max_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.name_helper') }}</span>
                </div>
            @endif

                
            </div>
            
            <div class="form-group row">

                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.pricelist.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', $pricelist->amount) ?? 0 }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.amount_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="order">{{ trans('cruds.pricelist.fields.order') }}</label>
                    <input class="form-control {{ $errors->has('order') ? 'is-invalid' : '' }}" type="number" name="order" id="order" value="{{ old('order', $pricelist->order) ?? 0 }}" step="1" required>
                    @if($errors->has('order'))
                        <div class="invalid-feedback">
                            {{ $errors->first('order') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.order_helper') }}</span>
                </div>

              
            </div>

            <div class="row form-group">

                <div class="col-md-6">
                    <label class="required" for="freeze_count">{{ trans('cruds.pricelist.fields.freeze_count') }}</label>
                    <input class="form-control {{ $errors->has('freeze_count') ? 'is-invalid' : '' }}" type="number" name="freeze_count" id="freeze_count" value="{{ old('freeze_count', $pricelist->freeze_count) ?? 0 }}"  required>
                    @if($errors->has('freeze_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('freeze_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.freeze_count_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="invitation">{{ trans('cruds.pricelist.fields.invitation_count') }}</label>
                    <input class="form-control {{ $errors->has('invitation') ? 'is-invalid' : '' }}" type="number" name="invitation" id="invitation" value="{{ old('invitation') ?? $pricelist->invitation }}"  required>
                    @if($errors->has('invitation'))
                        <div class="invalid-feedback">
                            {{ $errors->first('invitation') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.freeze_count_helper') }}</span>
                </div>

               
            </div>
            
            <div class="row">
                
                <div class="col-md-6">
                    <label class="required" for="free_sessions">{{ trans('cruds.pricelist.fields.free_sessions') }}</label>
                    <input class="form-control {{ $errors->has('free_sessions') ? 'is-invalid' : '' }}" type="number" name="free_sessions" id="free_sessions" value="{{ old('free_sessions') ?? $pricelist->free_sessions }}"  required>
                    @if($errors->has('free_sessions'))
                        <div class="invalid-feedback">
                            {{ $errors->first('free_sessions') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.free_sessions_helper') }}</span>
                </div>

                @foreach ($serviceOptions as $serviceOption)
                    <input type="hidden" name="service_ids[]" value="{{ $serviceOption->id }}">
                    <div class="form-group col-md-6">
                        <label class="required" for="{{ $serviceOption->name.'_count' }}">{{ $serviceOption->name }}</label>
                        <input class="form-control" type="number" name="count[]" id="{{ $serviceOption->name.'_count' }}" value="{{  $pricelist->serviceOptionsPricelist()->where('service_option_id',$serviceOption->id)->first()->count ?? 0}}"  required>
                    </div>
                @endforeach
            </div>
           
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="session_count">{{ trans('cruds.pricelist.fields.session_count') }}</label>
                    <input class="form-control {{ $errors->has('session_count') ? 'is-invalid' : '' }}" type="number" name="session_count" id="session_count" value="{{ old('session_count',$pricelist->session_count) ?? 0  }}"  required>
                    @if($errors->has('session_count'))
                        <div class="invalid-feedback">
                            {{ $errors->first('session_count') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.session_count_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="status">{{ trans('cruds.pricelist.fields.status') }}</label>
                    <select class="form-control select2 {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                        <option value="active" {{ (old('status') ? old('status') : $pricelist->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('status') ? old('status') : $pricelist->status ?? '') == 'inactive' ? 'selected' : '' }}>Inctive</option>
                    </select>
                    @if($errors->has('status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.status_helper') }}</span>
                </div>
            </div>

            {{-- <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="from">{{ trans('cruds.pricelist.fields.from') }}</label>
                    <input type="time" name="from" id="from" class="form-control {{ $errors->has('from') ? 'is-invalid' : '' }}" value="{{ $pricelist->from ?? '00:00' }}" required>
                    @if($errors->has('from'))
                        <div class="invalid-feedback">
                            {{ $errors->first('from') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.from_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required"  for="to">{{ trans('cruds.pricelist.fields.to') }}</label>
                    <input type="time" name="to" id="to" class="form-control {{ $errors->has('to') ? 'is-invalid' : '' }}" value="{{ $pricelist->to ?? '23:59' }}" required>
                    @if($errors->has('to'))
                        <div class="invalid-feedback">
                            {{ $errors->first('to') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.to_helper') }}</span>
                </div>
            </div> --}}

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="upgrade_from">{{ trans('cruds.pricelist.fields.upgrade_from') }}</label>
                    <input type="text" name="upgrade_from" id="upgrade_from" class="form-control {{ $errors->has('upgrade_from') ? 'is-invalid' : '' }}" placeholder="7" value="{{ $pricelist->upgrade_from }}" required>
                    @if($errors->has('upgrade_from'))
                        <div class="invalid-feedback">
                            {{ $errors->first('upgrade_from') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.upgrade_from_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="upgrade_to">{{ trans('cruds.pricelist.fields.upgrade_to') }}</label>
                    <input type="text" name="upgrade_to" id="upgrade_to" class="form-control {{ $errors->has('upgrade_to') ? 'is-invalid' : '' }}" placeholder="15" value="{{ $pricelist->upgrade_to }}" required>
                    @if($errors->has('upgrade_to'))
                        <div class="invalid-feedback">
                            {{ $errors->first('upgrade_to') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.upgrade_to_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="expiring_date">{{ trans('cruds.pricelist.fields.expiring_date') }}</label>
                    <input type="text" name="expiring_date" id="expiring_date" class="form-control {{ $errors->has('expiring_date') ? 'is-invalid' : '' }}" placeholder="5" value="{{ $pricelist->expiring_session }}" required>
                    @if($errors->has('expiring_date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('expiring_date') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.expiring_date_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="expiring_session">{{ trans('cruds.pricelist.fields.expiring_session') }}</label>
                    <input type="text" name="expiring_session" id="expiring_session" class="form-control {{ $errors->has('expiring_session') ? 'is-invalid' : '' }}" placeholder="3" value="{{ $pricelist->expiring_session }}" required>
                    @if($errors->has('expiring_session'))
                        <div class="invalid-feedback">
                            {{ $errors->first('expiring_session') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.expiring_session_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label  for="followup_date">{{ trans('cruds.pricelist.fields.followup_date') }}</label>
                    <input type="text" name="followup_date" id="followup_date" class="form-control" value="{{ $pricelist->followup_date ?? 0 }}">
                    @if($errors->has('followup_date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('followup_date') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.followup_date_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label  for="upgrade_date">{{ trans('cruds.pricelist.fields.upgrade_date') }}</label>
                    <input type="text" name="upgrade_date" id="upgrade_date" class="form-control" value="{{ $pricelist->upgrade_date ?? 0 }}">
                    @if($errors->has('upgrade_date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('upgrade_date') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.upgrade_date_helper') }}</span>
                </div>
            </div>

            {{-- /////////////////////////////// --}}

            <div class="form-group row">
                <div class="col-md-3 mt-4">
                    <h5>Main Service</h5>
                </div>
                
                <div class="col-md-9">
                    <label class="c-switch c-switch-3d c-switch-success my-4">
                        <input type="checkbox" name="main_service" id="main_service" value="{{ $pricelist->main_service == true ? true : false }}" class="c-switch-input" {{ $pricelist->main_service == true ? 'checked' : '' }}>
                        <span class="c-switch-slider shadow-none"></span>
                    </label>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-3 mt-4">
                    <h5>{{ trans('global.all_days') }}</h5>
                </div>
                <div class="col-md-9">
                    <label class="c-switch c-switch-3d c-switch-success my-4">
                        <input type="checkbox" name="all_days" id="all_days" value="true" class="c-switch-input" {{ $pricelist->pricelist_days_count <= 0 ? 'checked' : ''}}>
                        <span class="c-switch-slider shadow-none"></span>
                    </label>
                </div>
            </div>

            <div class="form-group row allDays" style="{{ $pricelist->pricelist_days_count <= 0 ? 'display: none': ''  }}">
                <div class="col-md-6">
                    <label  for="days">{{ trans('global.days') }}</label>
                    <select name="allDays[]" class="form-control select2" multiple="">
                        @foreach (\App\Models\PricelistDays::DAYS as $key => $day)
                            <option value="{{ $key }}" {{ in_array($key,$pricelist->pricelist_days->pluck('day')->toArray()) ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- /////////////////////////////// --}}

            <div class="form-group row">
                <div class="col-md-3 mt-4">
                    <h5>{{ trans('global.full_day') }}</h5>
                </div>
                <div class="col-md-9">
                    <label class="c-switch c-switch-3d c-switch-success my-4">
                        <input type="checkbox" name="full_day" id="full_day" value="{{ $pricelist->full_day == 'true' ? 'true' : 'false' }}" class="c-switch-input" {{ $pricelist->full_day == 'true' ? 'checked' : '' }}>
                        <span class="c-switch-slider shadow-none"></span>
                    </label>
                </div>
            </div>
            <div class="form-group row hideMe" >
                <div class="col-md-6">
                    <label class="required" for="from">{{ trans('cruds.pricelist.fields.from') }}</label>
                    <input type="time" name="from" id="from" class="form-control {{ $errors->has('from') ? 'is-invalid' : '' }}" value="{{ $pricelist->from ?? '00:00' }}" required>
                    @if($errors->has('from'))
                        <div class="invalid-feedback">
                            {{ $errors->first('from') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.from_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required"  for="to">{{ trans('cruds.pricelist.fields.to') }}</label>
                    <input type="time" name="to" id="to" class="form-control {{ $errors->has('to') ? 'is-invalid' : '' }}" value="{{ $pricelist->to ?? '23:59' }}" required>
                    @if($errors->has('to'))
                        <div class="invalid-feedback">
                            {{ $errors->first('to') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.pricelist.fields.to_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-3 mt-4">
                    <h5>All Branches</h5>
                </div>
                <div class="col-md-9">
                    <label class="c-switch c-switch-3d c-switch-success my-4">
                        <input type="checkbox" name="all_branches" id="all_branches" value="{{ $pricelist->all_branches == 'true' ? 'true' : 'false' }}" class="c-switch-input" {{ $pricelist->all_branches == 'true' ? 'checked' : '' }}>
                        <span class="c-switch-slider shadow-none"></span>
                    </label>
                </div>
            </div>

            <div class="form-group card-footer">
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
        if ($("#full_day").val() == 'true') 
        {
            $(".hideMe").slideUp();
        }

        $("#full_day").change(function() {
            if(this.checked == true) {
                $(".hideMe").slideUp();
            }
            if (this.checked == false){
                $(".hideMe").slideDown();
            }
        });

        $("#all_days").change(function() {
            if(this.checked == true) {
                $(".allDays").slideUp();
            }else {
                $(".allDays").slideDown();
            }
        });
    </script>
@endsection