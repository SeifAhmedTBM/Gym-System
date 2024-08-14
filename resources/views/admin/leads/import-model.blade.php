<div class="modal fade" id="importLead" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('global.datatables.excel')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route($route) }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="alert alert-danger font-weight-bold">
                        <i class="fa fa-exclamation-circle"></i> {{ trans('global.max_number_uploaded') .' 1000 Record' }} .
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                            <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                <label for="csv_file" class="col-md-4 control-label">@lang('global.datatables.excel')</label>

                                <div class="col-md-6">
                                    <input id="csv_file" type="file" class="form-control-file" name="Lead" required>

                                    @if($errors->has('csv_file'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('csv_file') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ asset('leads_sample2.xlsx') }}" class="btn btn-danger">
                        <i class="fa fa-download"></i> {{ trans('global.download_sample') }}
                    </a>
                        
                    <button type="submit" class="btn btn-primary">
                       <i class="fa fa-check"></i> @lang('global.confirm')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>