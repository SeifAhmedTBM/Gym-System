@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>Send Notifications</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{route('admin.sendNotification')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="section_id">Branches</label>
                <select class="form-control select2 {{ $errors->has('section') ? 'is-invalid' : '' }}" name="branch" required>
                        <option value="">All Branches</option> 
                        @foreach($branches as $branch)
                        <option value="{{$branch->id}}">{{$branch->name}}</option> 
                        @endforeach
                </select>
                @if($errors->has('section'))
                    <div class="invalid-feedback">
                        {{ $errors->first('section') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.news.fields.section_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="description">Notification Content</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" required>{{ old('description') }}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.news.fields.description_helper') }}</span>
            </div>
         
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.send') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')

@endsection