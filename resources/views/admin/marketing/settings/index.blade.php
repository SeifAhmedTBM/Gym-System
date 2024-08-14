@extends('layouts.admin')
@section('content')
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <h5><i class="fa fa-cogs"></i> {{ trans('global.marketing_settings') }}</h5>
    </div>
</div>

<div class="form-group row">
    {{-- <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-header font-weight-bold">
                Zoom Meeting
            </div>
            <div class="card-body">
                <i class="fas fa-video text-info fa-5x"></i>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-dark" type="button" data-toggle="modal" data-target="#zoomModal">
                    <i class="fas fa-video"></i>  {{ trans('global.settings') }}
                </button>
            </div>
        </div>
    </div> --}}
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-header font-weight-bold">
                {{ trans('global.whatsapp') }}
            </div>
            <div class="card-body">
                <i class="fab fa-whatsapp-square text-success fa-5x"></i>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-dark" type="button" data-toggle="modal" data-target="#whatsappModal">
                    <i class="fab fa-whatsapp"></i> {{ trans('global.settings') }}
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-header font-weight-bold">
                {{ trans('global.sms') }}
            </div>
            <div class="card-body">
                <i class="fa fa-envelope-square text-info fa-5x"></i>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-dark" type="button" data-toggle="modal" data-target="#smsModal">
                    <i class="fa fa-envelope"></i> {{ trans('global.settings') }}
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-header font-weight-bold">
                {{ trans('global.email_campaigns') }}
            </div>
            <div class="card-body">
                <i class="fa fa-paper-plane text-danger fa-5x"></i>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-dark" type="button" data-toggle="modal" data-target="#emailCampaignsModal">
                    <i class="fa fa-envelope"></i> {{ trans('global.settings') }}
                </button>
            </div>
        </div>
    </div>
</div>


@include('admin.marketing.settings.modals')
@endsection