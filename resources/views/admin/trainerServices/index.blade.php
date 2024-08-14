@extends('layouts.admin')
@section('content')
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fa fa-list"></i> {{ trans('global.trainer_services') }}
    </div>
    <div class="card-body">
        @can('trainer_services_create')
            <a href="{{ route('admin.trainer-services.create') }}" class="btn btn-primary float-right mb-2">
                <i class="fa fa-plus-circle"></i> {{ trans('global.add') }}
            </a>
        @endcan
        <div class="table-responsive">
            <table class="table text-center table-outline table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark font-weight-bold">#</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.trainer') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('cruds.service.title_singular') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.type') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.commission') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trainerServices as $trainerService)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td>{{ $trainerService->trainer->name }}</td>
                            <td>{{ $trainerService->service->name }}</td>
                            <td>{{ App\Models\TrainerService::COMMISSION_TYPE[$trainerService->commission_type] }}</td>
                            <td>{{ $trainerService->commission . ' ' . ($trainerService->commission_type == 'percentage' ? '%' : 'EGP') }}</td>
                            <td>
                                @can('trainer_services_delete')
                                <button type="button" data-toggle="modal" data-target="#deleteModal" onclick="deleteModel(this)" data-trainer-service="{{ $trainerService->id }}" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <span class="float-right">
            {{ $trainerServices->links() }}
        </span>
    </div>
</div>

@can('trainer_services_delete')
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
@endcan
@endsection

@section('scripts')
    <script>
        @can('trainer_services_delete')
        function deleteModel(button) {
            let trainer_service_id = $(button).data('trainer-service');
            let url = "{{ route('admin.trainer-services.destroy', ':trainer_service') }}";
            url = url.replace(':trainer_service', trainer_service_id);
            $("#deleteForm").attr('action', url);
        }
        @endcan
    </script>
@endsection