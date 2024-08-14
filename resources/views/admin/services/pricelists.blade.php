@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <a href="{{ route('admin.pricelists.create',$service->id) }}" class="btn btn-success">Create</a>
    </div>

    <div class="form-group">
        <div class="card-header">
            Pricelists List | {{ $service->name }}
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('cruds.pricelist.fields.order') }}</th>
                            <th>{{ trans('cruds.pricelist.fields.name') }}</th>
                            <th>{{ trans('cruds.pricelist.fields.freeze_count') }}</th>
                            <th>{{ trans('cruds.pricelist.fields.session_count') }}</th>
                            <th>{{ trans('cruds.pricelist.fields.amount') }}</th>
                            <th>{{ trans('global.full_day') }}</th>
                            <th>{{ trans('global.all_days') }}</th>
                            <th>{{ trans('cruds.pricelist.fields.status') }}</th>
                            <th>{{ trans('global.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($service->service_pricelist as $pricelist)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <th>{{ $pricelist->order }}</th>
                                <th>{{ $pricelist->name }}</th>
                                <th>{{ $pricelist->freeze_count }}</th>
                                <th>{{ $pricelist->session_count }}</th>
                                <th>{{ $pricelist->amount }}</th>
                                <th>{!! $pricelist->full_day == 'true' ? "<span class='badge badge-success'>Yes</span>" : "<span class='badge badge-danger'>No</span>" !!}</th>
                                <th>{!! $pricelist->pricelist_days->count() > 0 ? "<span class='badge badge-danger'>No</span>" : "<span class='badge badge-success'>Yes</span>" !!}</th>
                                <th>{{ $pricelist->status }}</th>
                                <th>
                                    <div class="dropdown">
                                        <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                            aria-expanded="false">
                                            {{ trans('global.action') }}
                                        </a>
                                    
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="{{ route('admin.pricelists.show', $pricelist->id) }}">
                                                <i class="fa fa-eye"></i> &nbsp; {{ trans('global.view') }}
                                            </a>

                                            <a class="dropdown-item" href="{{ route('admin.pricelists.edit', $pricelist->id) }}">
                                                <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection