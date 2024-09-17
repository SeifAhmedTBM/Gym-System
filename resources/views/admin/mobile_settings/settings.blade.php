@extends('layouts.admin')
@section('content')
    @isset($settings)
        {!! Form::open(['files' => true, 'method' => 'PUT', 'action' => ['Admin\MobileSettingController@update', $settings->id]]) !!}
    @else
        {!! Form::open(['files' => true, 'method' => 'POST', 'action' => 'Admin\MobileSettingController@store']) !!}
    @endisset
    <div class="card shadow-sm">
        <div class="card-header">
            <h5><i class="fa fa-image"></i> Mobile Settings</h5>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('privacy_setting', trans('global.privacy_setting')) !!}
                        {!! Form::textarea('privacy_setting', $settings->privacy_setting ?? null, ['class' => 'form-control', 'placeholder' => trans('global.privacy_setting')]) !!}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('about_us', trans('global.about_us')) !!}
                        {!! Form::textarea('about_us', $settings->about_us ?? null, ['class' => 'form-control', 'placeholder' => trans('global.about_us')]) !!}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('rules', trans('global.rules')) !!}
                        {!! Form::textarea('rules', $settings->rules ?? null, ['class' => 'form-control', 'placeholder' => trans('global.rules')]) !!}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('terms_conditions', trans('global.terms_conditions')) !!}
                        {!! Form::textarea('terms_conditions', $settings->terms_conditions ?? null, ['class' => 'form-control', 'placeholder' => trans('global.terms_conditions')]) !!}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('pt_service_type', trans('global.pt_service_type')) !!}
                        {!! Form::select('pt_service_type',
                            ['service1' => \App\Models\ServiceType::pluck('name','id')],
                            $settings->pt_service_type ?? null,
                        ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn mb-3 btn-success" type="submit">
        <i class="fa fa-check-circle"></i> {{ trans('global.save') }}
    </button>
    {!! Form::close() !!}
@endsection

@section('scripts')
    <script>
        ClassicEditor.create(document.querySelector('#privacy_setting'));
        ClassicEditor.create(document.querySelector('#about_us'));
        ClassicEditor.create(document.querySelector('#rules'));
        ClassicEditor.create(document.querySelector('#terms_conditions'));
    </script>
@endsection
