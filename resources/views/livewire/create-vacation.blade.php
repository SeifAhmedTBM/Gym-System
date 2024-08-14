<div>
    @if (!is_null($employee_id))
        <div class="alert alert-info text-center">
            {{ trans('global.vacations_balance') }} : {{ $vacations_balance ?? 0 }}
        </div>
    @endif
    <div class="form-group row">
        <div class="col-md-6">
            <label class="required" for="employee_id">{{ trans('cruds.vacation.fields.employee') }}</label>
            <select class="form-control {{ $errors->has('employee') ? 'is-invalid' : '' }}" name="employee_id" id="employee_id" required wire:model.live="employee_id">
                @foreach($employees as $id => $entry)
                    <option value="{{ $id }}" {{ old('employee_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                @endforeach
            </select>
            @if($errors->has('employee'))
                <div class="invalid-feedback">
                    {{ $errors->first('employee') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.vacation.fields.employee_helper') }}</span>
{{-- 
            {{ var_export($employee_id) }}
            {{ var_export($vacations_balance) }} --}}
        </div>
        <div class="col-md-6">
            <label class="required" for="name">{{ trans('global.title') }}</label>
            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
            @if($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.vacation.fields.name_helper') }}</span>
        </div>
    </div>
   
    <div class="form-group row">
        <div class="col-md-12">
            <label class="required" for="description">{{ trans('cruds.vacation.fields.description') }}</label>
            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" required>{{ old('description') }}</textarea>
            @if($errors->has('description'))
                <div class="invalid-feedback">
                    {{ $errors->first('description') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.vacation.fields.description_helper') }}</span>
        </div>
    </div>
    
    <div class="form-group row">
        <div class="col-md-4">
            <label class="required" for="from">{{ trans('cruds.vacation.fields.from') }}</label>
            <input class="form-control  {{ $errors->has('from') ? 'is-invalid' : '' }}" type="date" name="from" id="from" value="{{ old('from') ?? date('Y-m-d') }}" required  wire:model.defer="from">
            @if($errors->has('from'))
                <div class="invalid-feedback">
                    {{ $errors->first('from') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.vacation.fields.from_helper') }}</span>
        </div>

        <div class="col-md-4">
            <label class="required" for="to">{{ trans('cruds.vacation.fields.to') }}</label>
            <input class="form-control {{ $errors->has('to') ? 'is-invalid' : '' }}" type="date" name="to" id="to" value="{{ old('to') ?? date('Y-m-d') }}" required wire:model.lazy="to">
            @if($errors->has('to'))
                <div class="invalid-feedback">
                    {{ $errors->first('to') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.vacation.fields.to_helper') }}</span>
        </div>
        <div class="col-md-4">
            <label class="required" for="diff">{{ trans('global.days') }}</label>
            <input class="form-control {{ $errors->has('diff') ? 'is-invalid' : '' }}" type="text" name="diff" id="diff" value="{{ old('diff',0) }}" readonly wire:model.live="diff">
            @if($errors->has('diff'))
                <div class="invalid-feedback">
                    {{ $errors->first('diff') }}
                </div>
            @endif
            <span class="help-block">{{ trans('cruds.vacation.fields.to_helper') }}</span>
        </div>
    </div>
</div>
