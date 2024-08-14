@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.refund.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.refunds.update", [$refund->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="date">Date </label>
                <input type="date" name="created_at" id="created_at" class="form-control" value="{{date('Y-m-d',strtotime($refund->created_at))}}" >
                <span class="help-block">{{ trans('cruds.refund.fields.refund_reason_helper') }}</span>
            </div>
        
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.refund.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', $refund->amount) }}" step="0.01" required>
                @if($errors->has('amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.refund.fields.amount_helper') }}</span>
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