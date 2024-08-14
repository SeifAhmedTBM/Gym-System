   {{-- payments details --}}
   <div class="card">
    <div class="card-header">
        ENTER DETAILS OF THE PAYMENT
    </div>
    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-4">
                <label class="required" for="net_amount">{{ trans('cruds.invoice.fields.net_amount') }}</label>
                <input type="text" class="form-control" name="net_amount" id="net_amount" value="{{ old('net_amount') ?? 0 }}" readonly required>
                @if ($errors->has('net_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('net_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.net_amount_helper') }}</span>
            </div>

            <div class="col-md-4">
                <label class="required" for="received_amount">{{ trans('cruds.invoice.fields.received_amount') }}</label>
                <input type="text" class="form-control" name="received_amount" id="received_amount" value="{{ old('received_amount') ?? 0 }}" readonly required>
                @if ($errors->has('received_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('received_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.received_amount_helper') }}</span>
            </div>

            <div class="col-md-4">
                <label class="required" for="amount_pending">{{ trans('cruds.payment.fields.amount_pending') }}</label>
                <input class="form-control {{ $errors->has('amount_pending') ? 'is-invalid' : '' }}" type="number"
                    name="amount_pending" id="amount_pending" value="{{ old('amount_pending') ?? 0 }}" required readonly>
                @if ($errors->has('amount_pending'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount_pending') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.amount_pending_helper') }}</span>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label class="required">{{ trans('cruds.branch.title_singular') }}</label>
                <select class="form-control {{ $errors->has('branch_id') ? 'is-invalid' : '' }}" name="branch_id" id="branch_id" 
                    onchange="getBranch()"  {{ is_null($selected_branch) ? '' : 'readonly' }} required >
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}" {{ $selected_branch != NULL && $selected_branch->id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if($errors->has('branch_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('branch_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.account_helper') }}</span>
            </div>

            <div class="col-md-6">
                <label class="required">{{ trans('cruds.member.fields.payment_method') }}</label>
                <select class="form-control select2 {{ $errors->has('account_id') ? 'is-invalid' : '' }}" id="account_id" multiple="multiple" onchange="getAccounts()" required>
                    @if ($selected_branch != NULL)
                        @foreach ($selected_branch->accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    @endif
                </select>
                @if($errors->has('account_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('account_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.account_helper') }}</span>
            </div>
        </div>

        <div class="row form-group" id="accounts_div">
            @if ($selected_branch != NULL)
                @foreach ($selected_branch->accounts as $id => $account)
                    <input type="hidden" name="account_ids[]" value="{{ $account->id }}">
                    <div class="col-md-3">
                        <label class="required" for="{{ $account->name }}">{{ $account->name }}</label>
                        <input class="form-control accounts {{ $errors->has('cash_amount') ? 'is-invalid' : '' }}" type="number"
                            name="account_amount[]" id="{{ $account->id }}" required readonly>
                        <span class="help-block">{{ trans('cruds.payment.fields.cash_amount_helper') }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
