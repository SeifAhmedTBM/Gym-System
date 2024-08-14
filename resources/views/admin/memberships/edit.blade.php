@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.membership.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.memberships.update", [$membership->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.membership.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date', $membership->start_date) }}" required>
                @if($errors->has('start_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.start_date_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.membership.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date', $membership->end_date) }}" required>
                @if($errors->has('end_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
            </div>


            <div class="form-group">
                <label class="" for="notes">Subscription Notes</label>
                <input class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" type="text" name="notes" id="notes" value="{{ old('notes', $membership->notes) }}">
                @if($errors->has('notes'))
                    <div class="invalid-feedback">
                        {{ $errors->first('notes') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="" for="date">Created at</label>
                <input class="form-control {{ $errors->has('created_at') ? 'is-invalid' : '' }}" type="date" name="created_at" id="created_at" value="{{ date('Y-m-d',strtotime($membership->created_at)) }}">
                @if($errors->has('created_at'))
                    <div class="invalid-feedback">
                        {{ $errors->first('created_at') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
            </div>

            
            <div class="form-group">
                <label class="required" for="trainer_id">{{ trans('cruds.membership.fields.trainer') }}</label>
                <select class="form-control  {{ $errors->has('trainer') ? 'is-invalid' : '' }}" name="trainer_id" id="trainer_id" required {{ $membership->service_pricelist->service->trainer == 0 ? 'disabled' : '' }}>
                    @foreach($trainers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('trainer_id') ? old('trainer_id') : $membership->trainer->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('trainer'))
                    <div class="invalid-feedback">
                        {{ $errors->first('trainer') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.trainer_helper') }}</span>
            </div>
            

            <input type="hidden" id="service_pricelist_id" name="service_pricelist_id" value="{{$membership->service_pricelist->id}}" />

            {{-- <div class="form-group">
                <label class="required" for="service_pricelist_id">{{ trans('cruds.membership.fields.service') }}</label>
                <select class="form-control select2 {{ $errors->has('service') ? 'is-invalid' : '' }}" name="service_pricelist_id" id="service_pricelist_id" required>
                    @foreach($services as $id => $entry)
                        <option value="{{ $id }}" {{ (old('service_id') ? old('service_id') : $membership->service->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('service'))
                    <div class="invalid-feedback">
                        {{ $errors->first('service') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.service_helper') }}</span>
            </div> --}}
            
            {{-- <div class="form-group">
                <label class="required" for="sales_by_id">{{ trans('cruds.membership.fields.sales_by') }}</label>
                <select class="form-control select2 {{ $errors->has('sales_by') ? 'is-invalid' : '' }}" name="sales_by_id" id="sales_by_id" required>
                    @foreach($sales_bies as $id => $entry)
                        <option value="{{ $id }}" {{ (old('sales_by_id') ? old('sales_by_id') : $membership->sales_by->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('sales_by'))
                    <div class="invalid-feedback">
                        {{ $errors->first('sales_by') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.sales_by_helper') }}</span>
            </div> --}}
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection