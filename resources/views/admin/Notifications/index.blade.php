@extends('layouts.admin')
@section('content')

<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js"></script>
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

<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.14.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.14.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "{{ env('FCM_API_KEY') }}",
    authDomain: "{{ env('FCM_AUTH_DOMAIN') }}",
    projectId: "{{ env('FCM_PROJECT_ID') }}",
    storageBucket: "{{ env('FCM_STORAGE_BUCKET') }}",
    messagingSenderId: "{{ env('FCM_MESSAGING_SENDER_ID') }}",
    appId: "{{ env('FCM_APP_ID') }}",
    measurementId: "{{ env('FCM_MEASUREMENT_ID') }}",
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>
@endsection

@section('scripts')

@endsection