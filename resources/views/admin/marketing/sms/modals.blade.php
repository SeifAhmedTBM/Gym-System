<!-- Send SMS Modal -->
<div class="modal fade" id="sendSMSModal" tabindex="-1" aria-labelledby="sendSMSModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendSMSModalLabel">{{ trans('global.send_sms') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'action' => 'Admin\Marketing\SmsController@store', 'files' => true]) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('numbers_csv', trans('global.numbers_csv')) !!}
                    {!! Form::file('numbers_csv', ['class' => 'd-block']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('message', trans('global.message')) !!}
                    {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 1]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i> {{ trans('global.send') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>



{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['id' => 'delete_form', 'method' => 'DELETE']) !!}
            <div class="modal-body">
                <h5 class="font-weight-bold text-danger text-center">
                    {{ trans('global.delete_alert') }}
                </h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>