@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>Rules</h5>
    </div>
    <form action="{{ isset($rule) ? route('admin.rules.update',$rule->id) : route('admin.rules.store') }}" method="POST">
        @csrf
        @if (isset($rule))
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <label for="description">{{ trans('global.description') }}</label>
                    <textarea name="description" id="description" rows="10" class="form-control">{{ $rule->description ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-3">
                    <button class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.save') }}</button>
                </div>
            </div>
        </div>
    </form>
</div>



@endsection