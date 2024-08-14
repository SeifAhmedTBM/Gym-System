@extends('layouts.admin')
@section('content')
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fa fa-list"></i> {{ trans('global.sports') }}
    </div>
    <div class="card-body">
        @can('sports_create')
            <button data-toggle="modal" data-target="#addNewSportModal" class="btn btn-primary float-right mb-2">
                <i class="fa fa-plus-circle"></i> {{ trans('global.add') }}
            </button>
        @endcan
        <div class="table-responsive">
            <table class="table text-center table-outline table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th class="font-weight-bold text-dark">#</th>
                        <th class="font-weight-bold text-dark">{{ trans('global.name') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('global.created_at') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sports as $sport)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td>{{ $sport->name }}</td>
                            <td>{{ $sport->created_at->toFormattedDateString() }}</td>
                            <td>
                                @can('sports_delete')
                                <button type="button" data-toggle="modal" data-target="#deleteModal" onclick="deleteModel(this)" data-sport="{{ $sport->id }}" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deleteModalLabel">{{ trans('global.delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'DELETE', 'id' => 'deleteForm']) !!}
            <div class="modal-body text-danger text-center">
                <h5 class="m-0 p-0 font-weight-bold">{{ trans('global.delete_alert') }}</h5>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success"> <i class="fa fa-trash"></i> {{ trans('global.delete') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Add New Sport Modal -->
<div class="modal fade" id="addNewSportModal" tabindex="-1" aria-labelledby="addNewSportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewSportLabel">{{ trans('global.add') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.sports.store')]) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label for="sport_name" class="required">{{ trans('global.sport') }}</label>
                    <input required type="text" name="sport_name" placeholder="{{ trans('global.type_here') }}" id="sport_name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success"> <i class="fa fa-check-circle"></i> {{ trans('global.add') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function deleteModel(button) {
            let sport_id = $(button).data('sport');
            let url = "{{ route('admin.sports.destroy', ':sport') }}";
            url = url.replace(':sport', sport_id);
            $("#deleteForm").attr('action', url);
        }
    </script>
@endsection