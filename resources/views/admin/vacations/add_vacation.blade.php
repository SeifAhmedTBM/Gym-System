@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.vacation.title_singular') }}</h5>
    </div>

    <form method="POST" action="{{ route("admin.vacations.store") }}" enctype="multipart/form-data">
        <div class="card-body">
            @csrf
            @livewire('create-vacation',['employee_id' => $employee->id])
        </div>
        <div class="card-footer">
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')
    <script>
        $('#employee_id').attr('readonly',true)
    </script>
@endsection