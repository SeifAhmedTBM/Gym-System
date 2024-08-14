   {{-- invoice details --}}
   <div class="card">
    <div class="card-header">
        ENTER DETAILS OF THE INVOICE
    </div>
    <div class="card-body">
        @cannot('free_discount') 
            @if ($setting->max_discount != 100)
                    <div class="alert alert-warning text-center">
                        <strong><i class="fa fa-exclamation-circle"></i> {{ trans('global.max_discount') }} : {{ $setting->max_discount }} %</strong>
                    </div>
            @endif
        @endcannot
        <div class="row form-group">
            <div class="col-md-3">
                <label class="required" for="invoice_id">{{ trans('cruds.payment.fields.invoice') }}</label>
                <input type="text" class="form-control" name="invoice_id" value="{{ $last_invoice + 001  }}" readonly>
                @if ($errors->has('invoice_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('invoice_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.invoice_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="membership_fee">{{ trans('cruds.membership.fields.membership_fee') }}</label>
                <input class="form-control {{ $errors->has('membership_fee') ? 'is-invalid' : '' }}" type="text"
                    name="membership_fee" id="membership_fee" value="{{ old('membership_fee') }}" required readonly>
                @if ($errors->has('membership_fee'))
                    <div class="invalid-feedback">
                        {{ $errors->first('membership_fee') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.membership_fee_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="discount">{{ trans('cruds.invoice.fields.discount') }}</label>
                <div class="input-group">
                    <input class="form-control {{ $errors->has('discount') ? 'is-invalid' : '' }}" type="number"
                        name="discount" id="discount" value="{{ old('discount') ?? 0 }}" required step="0.001">
                    <div class="input-group-append">
                        <span class="input-group-text" >%</span>
                    </div>
                </div>
                @if ($errors->has('discount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('discount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.discount_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="discount_amount">{{ trans('global.discount_amount') }}</label>
                <div class="input-group">
                    <input class="form-control {{ $errors->has('discount_amount') ? 'is-invalid' : '' }}" type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount') ?? 0 }}" required step="0.001">
                    <div class="input-group-append">
                        <span class="input-group-text">EGP</span>
                    </div>
                </div>
                @if ($errors->has('discount_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('discount_amount') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label for="discount_notes">{{ trans('cruds.invoice.fields.discount_notes') }}</label>
                <input type="text" name="discount_notes" id="discount_notes" class="form-control" />
            </div>
            <div class="col-md-6">
                <label for="created_at">Invoice Date :</label>
                <input type="date" class="form-control" name="created_at" value="{{ date('Y-m-d') }}" @cannot('invoice_change_date') readonly  @endcannot>
            </div>
        </div>
    </div>
</div>
