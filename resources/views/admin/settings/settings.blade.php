@extends('layouts.admin')
@section('content')
@isset($settings)
{!! Form::open(['files' => true,'method' => 'PUT', 'action' => ['Admin\SettingController@update', $settings->id]]) !!}
@else
{!! Form::open(['files' => true, 'method' => 'POST', 'action' => 'Admin\SettingController@store']) !!}
@endisset
<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fa fa-image"></i> {{ trans('global.settings') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="col-md-4">
                <a href="{{ isset($settings->menu_logo) ?  asset('images/'.$settings->menu_logo) : '' }}" target="_blank">
                    <img src="{{ isset($settings->menu_logo) ? asset('images/'.$settings->menu_logo) : '' }}" alt="" width="100">
                </a>
                <div class="form-group">
                    {!! Form::label('menu_logo', trans('global.menu_logo')) !!} <br>
                    {!! Form::file('menu_logo', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{ isset($settings->login_logo) ?  asset('images/'.$settings->login_logo) : '' }}" target="_blank">
                    <img src="{{ isset($settings->login_logo) ? asset('images/'.$settings->login_logo) : '' }}" alt="" width="100">
                </a>
                <div class="form-group">
                    {!! Form::label('login_logo', trans('global.login_logo')) !!} <br>
                    {!! Form::file('login_logo', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{ isset($settings->login_background) ?  asset('images/'.$settings->login_background) : '' }}" target="_blank">
                    <img src="{{ isset($settings->login_background) ? asset('images/'.$settings->login_background) : '' }}" alt="" width="100">
                </a>
                <div class="form-group">
                    {!! Form::label('login_background', trans('global.login_background')) !!} <br>
                    {!! Form::file('login_background', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('name', trans('global.gym_name')) !!}
                    {!! Form::text('name', $settings->name ?? null , ['class' => 'form-control', 'placeholder' => trans('global.name')]) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('email', trans('global.gym_email')) !!}
                    {!! Form::email('email', $settings->email ?? null , ['class' => 'form-control', 'placeholder' => trans('global.gym_email')]) !!}
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('phone_numbers', trans('global.gym_phone_numbers')) !!}
                    {!! Form::text('phone_numbers', $settings->phone_numbers ?? null , ['class' => 'form-control', 'placeholder' => trans('global.gym_phone_numbers')]) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('landline', trans('global.landline')) !!}
                    {!! Form::text('landline', $settings->landline ?? null , ['class' => 'form-control', 'placeholder' => trans('global.landline')]) !!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('address', trans('global.address')) !!}
            {!! Form::text('address', $settings->address ?? null , ['class' => 'form-control', 'placeholder' => trans('global.address')]) !!}
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('invoice_prefix', trans('global.invoice_prefix')) !!}
                    {!! Form::text('invoice_prefix', $settings->invoice_prefix ?? null , ['class' => 'form-control', 'placeholder' => trans('global.invoice_prefix')]) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('member_prefix', trans('global.member_prefix')) !!}
                    {!! Form::text('member_prefix', $settings->member_prefix ?? null , ['class' => 'form-control', 'placeholder' => trans('global.member_prefix')]) !!}
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('payroll_day', trans('global.payroll_day')) !!}
                    {!! Form::number('payroll_day', $settings->payroll_day ?? null , ['class' => 'form-control', 'placeholder' => trans('global.payroll_day_ex')]) !!}
                    <small>
                        {{ trans('global.payroll_hint') }}
                    </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('freeze_duration', trans('global.freeze_duration')) !!}
                    {!! Form::select('freeze_duration', 
                        ['weeks' => trans('global.weeks'), 'days' => trans('global.days')],
                        $settings->freeze_duration ?? 'weeks',
                    ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                {!! Form::label('color', trans('global.color')) !!}
                {!! Form::color('color', $settings->color ?? null , ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-4">
                <label for="max_discount">{{ trans('global.max_discount') }}</label>
                <div class="input-group">
                    <input class="form-control {{ $errors->has('max_discount') ? 'is-invalid' : '' }}" type="number" name="max_discount" id="max_discount" value="{{ $settings->max_discount ?? old('max_discount') }}" required step="0.001">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label for="inactive_members_days">{{ trans('global.inactive_members_days') }}</label>
                <input class="form-control {{ $errors->has('inactive_members_days') ? 'is-invalid' : '' }}" type="number" name="inactive_members_days" id="inactive_members_days" value="{{ $settings->inactive_members_days ?? old('inactive_members_days') }}" required step="0.001">
            </div>
            {{-- inactive_members_days --}}
        </div>

        <div class="form-group row">
            <div class="col-md-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="has_lockers" value="1" {{  isset($settings->has_lockers) && $settings->has_lockers ? 'checked' : '' }} class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">{{ trans('global.has_lockers') }}</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="freeze_request" value="1" {{  isset($settings) && $settings->freeze_request ? 'checked' : '' }} class="custom-control-input" id="customCheck2">
                    <label class="custom-control-label" for="customCheck2">{{ trans('global.freeze_request_automatically') }}</label>
                    <small>( Without Approve & Reject)</small>
                </div>
            </div>
        </div>
        
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shodow-sm">
            <div class="card-header">
                Social
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('facebook', 'Facebook') !!}
                            {!! Form::url('facebook', $settings->facebook ?? null , ['class' => 'form-control', 'placeholder' => 'facebook']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('instagram', 'Instagram') !!}
                            {!! Form::url('instagram', $settings->instagram ?? null , ['class' => 'form-control', 'placeholder' => 'instagram']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('whatsapp', 'Whatsapp') !!}
                            {!! Form::number('whatsapp', $settings->whatsapp ?? null , ['class' => 'form-control', 'placeholder' => 'whatsapp']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('gmail', 'Gmail') !!}
                            {!! Form::url('gmail', $settings->gmail ?? null , ['class' => 'form-control', 'placeholder' => 'gmail']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('long', 'Long') !!}
                            {!! Form::text('long', $settings->long ?? null , ['class' => 'form-control', 'placeholder' => 'long']) !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('lat', 'Lat') !!}
                            {!! Form::text('lat', $settings->lat ?? null , ['class' => 'form-control', 'placeholder' => 'lat']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.invoice_header') }}
                <span class="badge badge-success">
                    {{ trans('global.left_section') }}
                </span>
            </div>
            <div class="card-body">
                <p class="font-weight-bold text-primary">{{ trans('global.invoice_flags') }}</p>
                <div class="form-group">
                    @foreach (config('flags') as $key => $value)
                        <span data-editor="editor1" data-section="left_section" onclick="addFlagToTextArea(this)" style="cursor: pointer" class="mr-2 mb-2 badge badge-dark text-white cursor-pointer" data-value="{{ $key }}">{{ $value }}</span>
                    @endforeach
                </div>
                <label for="left_section">{{ trans('global.left_section') }}</label>
                <textarea id="left_section" name="left_section" id="left_section" rows="7" class="form-control form-control-lg my-align-right" style="text-align:*">{{ isset($settings) && json_decode($settings->invoice, true)['left_section'] ? json_decode($settings->invoice, true)['left_section'] : '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fa fa-arrow-circle-right"></i> {{ trans('global.invoice_header') }}
                <span class="badge badge-danger">
                    {{ trans('global.right_section') }}
                </span>
            </div>
            <div class="card-body">
                <p class="font-weight-bold text-primary">{{ trans('global.invoice_flags') }}</p>
                <div class="form-group">
                    @foreach (config('flags') as $key => $value)
                        <span data-editor="editor2" data-section="right_section" onclick="addFlagToTextArea(this)" style="cursor: pointer" class="mr-2 mb-2 badge badge-dark text-white cursor-pointer" data-value="{{ $key }}">{{ $value }}</span>
                    @endforeach
                </div>
                <label for="right_section">{{ trans('global.right_section') }}</label>
                <textarea id="right_section"  name="right_section" id="right_section" rows="7" class="form-control form-control-lg">{{ isset($settings) && json_decode($settings->invoice, true)['right_section'] ? json_decode($settings->invoice, true)['right_section'] : '' }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="fa fa-credit-card"></i> {{ trans('global.invoice_footer') }}
    </div>
    <div class="card-body">
        <p class="font-weight-bold text-primary">{{ trans('global.invoice_flags') }}</p>
        <div class="form-group">
            @foreach (config('flags') as $key => $value)
                <span data-editor="editor3" data-section="footer" onclick="addFlagToTextArea(this)" class="mr-2 mb-2 badge badge-dark text-white" style="cursor: pointer" data-value="{{ $key }}">{{ $value }}</span>
            @endforeach
        </div>
        <div class="form-group">
            <label for="invoice_footer">{{ trans('global.invoice_footer') }}</label>
            <textarea id="footer"  name="invoice_footer" id="invoice_footer" rows="7" class="form-control form-control-lg">{{ isset($settings) && json_decode($settings->invoice, true)['footer'] ? json_decode($settings->invoice, true)['footer'] : '' }}</textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                Terms & Conditions
            </div>
            <div class="card-body">
                <div class="form-group">
                    <textarea id="terms"  name="terms" rows="7" class="form-control form-control-lg">{{ $settings->terms ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                Privacy & Policy
            </div>
            <div class="card-body">
                <div class="form-group">
                    <textarea id="privacy"  name="privacy"  rows="7" class="form-control form-control-lg">{{ $settings->privacy ?? '' }}</textarea>
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
        let editor1, editor2, editor3;

        ClassicEditor.create( document.querySelector( '#left_section' ),{
            language: {

                ui: '{{ app()->isLocale("ar") ? "ar" : "en" }}',

                content: '{{ app()->isLocale("ar") ? "ar" : "en" }}'
            }
        } ).then(editor => {
            editor1 = editor;
        });

        ClassicEditor.create( document.querySelector( '#right_section' ),{
            language: {

                ui: '{{ app()->isLocale("ar") ? "ar" : "en" }}',

                content: '{{ app()->isLocale("ar") ? "ar" : "en" }}'
            }
        } ).then(editor => {
            editor2 = editor;
        });

        ClassicEditor.create( document.querySelector( '#footer' ),{
            language: {

                ui: '{{ app()->isLocale("ar") ? "ar" : "en" }}',

                content: '{{ app()->isLocale("ar") ? "ar" : "en" }}'
            }
        } ).then(editor => {
            editor3 = editor;
        });

        ClassicEditor.create( document.querySelector( '#terms' ),{
            language: {

                ui: '{{ app()->isLocale("ar") ? "ar" : "en" }}',

                content: '{{ app()->isLocale("ar") ? "ar" : "en" }}'
            }
        } ).then(editor => {
            editor4 = editor;
        });

        ClassicEditor.create( document.querySelector( '#privacy' ),{
            language: {

                ui: '{{ app()->isLocale("ar") ? "ar" : "en" }}',

                content: '{{ app()->isLocale("ar") ? "ar" : "en" }}'
            }
        } ).then(editor => {
            editor5 = editor;
        });

        function addFlagToTextArea(button) {
            let flag = $(button).data('value');
            let textarea = $(button).data('section');
            let editor_number = $(button).data('editor');
            if(editor_number == 'editor1') {
                editor1.model.change(writer => {
                    const insertPosition = editor1.model.document.selection.getLastPosition();
                    writer.insertText('{' + flag + '}', insertPosition );
                });
            }else if(editor_number == 'editor2') {
                editor2.model.change(writer => {
                    const insertPosition = editor2.model.document.selection.getLastPosition();
                    writer.insertText('{' + flag + '}', insertPosition );
                });
            }else if(editor_number == 'editor3') {
                editor3.model.change(writer => {
                    const insertPosition = editor3.model.document.selection.getLastPosition();
                    writer.insertText('{' + flag + '}', insertPosition );
                });
            }
        }

        $('#max_discount').on('keyup',function()
        {
            if (parseInt($('#max_discount').val()) <= 100) {
                $('#max_discount').removeClass('is-invalid').addClass('is-valid');
            }else{
                $('#max_discount').removeClass('is-valid').addClass('is-invalid');
            }
        })

    </script>
@endsection