@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.hotdeal.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.hotdeals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.id') }}
                        </th>
                        <td>
                            {{ $hotdeal->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.cover') }}
                        </th>
                        <td>
                            @if($hotdeal->cover)
                                <a href="{{ $hotdeal->cover->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $hotdeal->cover->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.logo') }}
                        </th>
                        <td>
                            @if($hotdeal->logo)
                                <a href="{{ $hotdeal->logo->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $hotdeal->logo->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.title') }}
                        </th>
                        <td>
                            {{ $hotdeal->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.promo_code') }}
                        </th>
                        <td>
                            {{ $hotdeal->promo_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.redeem') }}
                        </th>
                        <td>
                            {{ $hotdeal->redeem }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\Hotdeal::TYPE_SELECT[$hotdeal->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.hotdeal.fields.description') }}
                        </th>
                        <td>
                            {{ $hotdeal->description }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.hotdeals.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection