@extends('layouts.admin')
@section('content')
@if (is_null($settings))
    <div class="row">
        <div class="col-md-12 text-right">
            <a href="{{ route('admin.settings.create') }}" class="btn btn-primary mb-2">
                <i class="fa fa-plus-circle"></i> {{ trans('global.add_settings') }}
            </a>
        </div>
    </div>
@endif
<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fa fa-cogs"></i> {{ trans('global.settings') }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-outline shadow-sm table-bordered table-hover table-striped text-center">
            <thead class="thead-light">
                <tr>
                    <th>{{ trans('global.menu_logo') }}</th>
                    <th>{{ trans('global.member_prefix') }}</th>
                    <th>{{ trans('global.invoice_prefix') }}</th>
                    <th>{{ trans('global.freeze_duration') }}</th>
                    <th>{{ trans('global.has_lockers') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if($settings != NULL)
                <tr>
                    <td>
                        @if(!is_null($settings->menu_logo))
                            <img src="{{ asset('images/'. $settings->menu_logo) }}" alt="Menu Logo" width="50">
                        @endif
                    </td>
                    <td>
                        @if(!is_null($settings->login_logo))
                            <img src="{{ asset('images/'. $settings->login_logo) }}" alt="Menu Logo" width="50">
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $settings->invoice_prefix }}</span>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $settings->freeze_duration }}</span>
                    </td>
                    <td>
                        @if ($settings->has_lockers)
                            <span class="badge badge-success">
                                <i class="fa fa-check-circle"></i>
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fa fa-times-circle"></i>
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.settings.edit', $settings->id) }}" class="btn btn-success btn-sm">
                            <i class="fa fa-edit"></i> {{ trans('global.edit') }}
                        </a>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
@parent

@endsection