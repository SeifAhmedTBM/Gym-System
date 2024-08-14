<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['id' => 'deleteForm', 'method' => 'DELETE']) !!}
            <div class="modal-body border-0">
                <h5 class="text-center text-danger font-weight-bold">
                    {{ trans('global.areYouSure') }}
                </h5>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-check-circle"></i> {{ trans('global.delete') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>