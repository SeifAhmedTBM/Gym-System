<!-- SEND WHATSAPP MESSAGE -->
<div class="modal fade" id="sendWhatsappMessage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.send_whatsapp_message') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'action' => 'Admin\Marketing\WhatsappController@store', 'files' => true]) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('numbers_csv', trans('global.numbers_csv')) !!}
                    {!! Form::file('numbers_csv', ['class' => 'd-block']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('message', trans('global.message')) !!}
                    {!! Form::textarea('message', null, ['rows' => '1', 'class' => 'form-control form-control-lg', 'placeholder' => trans('global.message')]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('image', trans('global.featured_image')) !!} <br>
                    {!! Form::file('image', null) !!}
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
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.delete') }}</h5>
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