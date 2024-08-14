@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.role.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.roles.update", [$role->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="title">{{ trans('cruds.role.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $role->title) }}" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.role.fields.title_helper') }}</span>
            </div>
            
            <div class=" row">
                <div class="mx-3">
                    <input type="checkbox" class="" id="select_all" onchange="selectAll()">
                    <label class="" for="select_all">
                        {{ trans('global.select_all') }}
                    </label>
                </div>
            </div>

            <div class="table-responsive text-center">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="font-weight-bold">
                        <tr>
                            <td>#</td>
                            <td>{{ trans('global.name') }}</td>
                            <td>{{ trans('global.role') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perms as $role_name => $permission)
                            @if (count($permission) <= 2)
                                <tr>
                                    <td colspan="3" class="text-center bg-secondary text-white">
                                        <div class="custom-control custom-checkbox d-inline mx-3">
                                            <input {{ $role->permissions()->whereTitle($permission['True'])->first() ? 'checked' : '' }} type="checkbox" name="permissions[]" value="{{ $permission['True'] }}" class="custom-control-input" id="{{ $permission['True'] }}"  > 
                                            <label class="custom-control-label font-weight-bold" for="{{ $permission['True'] }}" >
                                                {{ $role_name }}
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox d-inline mx-3">
                                            <input type="checkbox" name="selects[]" id="selects_{{ $permission['True'] }}"   class="custom-control-input"  data = "{{ $permission['ID'] }}" onchange="selectAllRole(this)">
                                            <label class="custom-control-label font-weight-bold"  for="selects_{{ $permission['True'] }}"  >
                                                Select All 
                                            </label>
                                        </div>   
                                    </td>
                                </tr>
                            @else
                            <tr>
                                <td># {{ $loop->iteration }}</td>
                                <td>{{ $role_name }}</td>
                                <td>
                                    <div class="row form-group text-left">
                                        @foreach ($permission as $per_name => $per)
                                            @if($per_name != 'ID')
                                                <div class="col-3">
                                                    <div class="custom-control custom-checkbox my-1">
                                                        <input {{ $role->permissions()->whereTitle($per)->first() ? 'checked' : '' }} type="checkbox" name="permissions[]" value="{{ $per }}" class="custom-control-input su{{ $permission['ID'] }}" id="{{ $per }}">
                                                        <label class="custom-control-label font-weight-bold" for="{{ $per }}">
                                                            {{ $per_name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
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
        function selectAll()
        {
            var atLeastOneIsChecked = $('#select_all:checkbox:checked').length > 0;
            if (atLeastOneIsChecked) {
                $('input:checkbox').attr('checked',true);
            } else {
                $('input:checkbox').removeAttr('checked');
            } 
        }

        function selectAllRole(obj)
        {
            var atLeastOneIsChecked = $(obj).is(":checked");
            var permission = $(obj).attr('data');
            if (atLeastOneIsChecked) {
                $(".su"+permission).attr('checked',true);
            } else {
                $(".su"+permission).removeAttr('checked');
            } 
        }
    </script>
@endsection