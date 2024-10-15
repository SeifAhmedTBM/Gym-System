@extends('layouts.admin')
@section('content')

<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js"></script>
<div class="card">
    <div class="card-header">
        <h5>Send Notifications</h5>
    </div>
    @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card-body">
        <form method="POST" action="{{route('admin.sendNotification')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="section_id">Branches</label>
                <select class="form-control select2 {{ $errors->has('section') ? 'is-invalid' : '' }}" name="branch_id" required>
                        <option value="">Select Branch</option> 
                        <option value="0">All Branches</option> 
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
                <label class="required" for="description">Notification Title</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" id="title" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.news.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="description">Notification Content</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="body" id="body" required>{{ old('description') }}</textarea>
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