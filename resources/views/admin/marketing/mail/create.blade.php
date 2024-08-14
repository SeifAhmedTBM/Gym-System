@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.marketing.mails.index') }}" class="btn btn-danger mb-2">
    <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back_to_list') }}
</a>
<div class="card shadow-sm">
    <div class="card-header bg-default">
        <h5><i class="fa fa-paper-plane"></i> {{ trans('global.send_mail') }}</h5>
    </div>
    {!! Form::open(['method' => 'POST', 'action' => 'Admin\Marketing\MailCampsController@store', 'files' => true]) !!}
    <div class="card-body">
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('emails', trans('global.emails')) !!}
                    {!! Form::text('emails', null, ['id' => 'emails']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('emails_csv', trans('global.emails_csv')) !!}
                    {!! Form::file('emails_csv', ['class' => 'd-block mt-1']) !!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('subject', trans('global.subject'), ['class' => 'required']) !!}
            {!! Form::text('subject', null, ['class' => 'form-control', 'placeholder' => trans('global.subject')]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('message', trans('global.message'), ['class' => 'required']) !!}
            {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 1]) !!}
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">
            <i class="fa fa-paper-plane"></i> {{ trans('global.submit') }}
        </button>
    </div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
    @parent
    <script>
        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('#emails');
        // initialize Tagify on the above input node reference
        new Tagify(input)
        // CKEditor For Message
        ClassicEditor.create( document.querySelector( '#message' ) );
    </script>
@endsection