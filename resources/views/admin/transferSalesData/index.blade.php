@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <button class="btn btn-info" type="button" data-target="#upload" data-toggle="modal">
            <i class="fa fa-upload"></i> {{ trans('global.upload') }}
        </button>
    </div>

    <form action="{{ route('admin.transfer_sales_data.store') }}" method="post">
        @csrf
        <div class="form-group">
            <div class="card">
                <div class="card-header">
                    {{ trans('global.transfer_sales_data') }}
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="alert alert-warning">
                            <h5 class="text-center">
                                <i class="fa fa-exclamation-circle"></i>
                                <strong>if <span class="text-danger">(Is Retroactive)</span> is checked will transfer data
                                    from ( Members - Memberships - Invoices - Payments ). </strong>
                            </h5>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-2">
                            {{ trans('cruds.freezeRequest.fields.is_retroactive') }}
                        </div>
                        <div class="col-md-1 text-right">
                            <label class="c-switch c-switch-3d c-switch-success">
                                <input type="checkbox" name="retroactive" id="retroactive" value="yes"
                                    class="c-switch-input">
                                <span class="c-switch-slider shadow-none"></span>
                            </label>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <label for="from">{{ trans('global.from') }}</label>
                            <select name="from" id="from" class="form-control select2">
                                <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                @foreach ($sales as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="to">{{ trans('global.to') }}</label>

                            <select name="to[]" id="to" multiple class="form-control select2">
                                {{-- <option value="" disabled selected hidden>Please Select</option> --}}
                                @foreach ($sales as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i>
                        {{ trans('global.confirm') }}</button>
                </div>
            </div>
        </div>
    </form>


    <!-- Modal -->
    <div class="modal fade" id="upload" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.importSalesData') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.upload') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="upload">{{ trans('global.file') }}</label>
                        <input type="file" class="form-control" name="upload" id="upload">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
                            {{ trans('global.close') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i>
                            {{ trans('global.upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
