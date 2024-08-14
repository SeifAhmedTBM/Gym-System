<!-- Add New Campaign Modal -->
<div class="modal fade" id="addCampaignModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel">{{ trans('global.campaign') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.marketing.campaigns.store'), 'files' => true]) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('type', trans('cruds.hotdeal.fields.type'), ['class' => 'required']) !!}
                    {!! Form::select('type', App\Models\Campaign::TYPES, null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('image', trans('cruds.news.fields.image'), ['class' => 'd-block']) !!}
                    {!! Form::file('image') !!}
                </div>
                <div class="form-group">
                    {!! Form::label('text', trans('global.text')) !!}
                    {!! Form::textarea('text', null, ['class' => 'form-control', 'placeholder' => trans('global.type_here')]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    {{ trans('global.close') }}
                </button>
                <button type="submit" class="btn btn-success">
                    {{ trans('global.save') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>



<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'DELETE', 'id' => 'deleteForm']) !!}
            <div class="modal-body">
                <h4 class="text-danger font-weight-bold text-center">
                    {{ trans('global.delete_alert') }}
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    {{ trans('global.close') }}
                </button>
                <button type="submit" class="btn btn-success">{{ trans('global.delete') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>