@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} Schedule Main Group
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.schedule-main-groups.update", [$schedule_main_group->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="name">Name</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $schedule_main_group->name) }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control select2">
                        @foreach (App\Models\ScheduleMainGroup::STATUS as $key => $value)
                            <option value="{{ $key }}" {{ $schedule_main_group->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
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