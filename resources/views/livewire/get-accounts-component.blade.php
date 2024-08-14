<div>
    <div class="form-group">
        <label class="required" for="amount">{{ trans('cruds.assetsMaintenance.fields.amount') }}</label>
        <input wire:model.live="amount" class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" step="0.01" required>
        @if($errors->has('amount'))
            <div class="invalid-feedback">
                {{ $errors->first('amount') }}
            </div>
        @endif
        <span class="help-block">{{ trans('cruds.assetsMaintenance.fields.amount_helper') }}</span>
    </div>
    <div class="form-group">
        <label class="required" for="account_id">{{ trans('cruds.account.title_singular') }}</label>
        <select name="account_id" id="account_id" class="form-control">
            @forelse ($accounts as $account_id => $account_name)
                <option value="{{ $account_id }}">{{ $account_name }}</option>
            @empty
                <option selected disabled hidden>{{ trans('global.please_enter_amount_first') }}</option>
            @endforelse
        </select>
    </div>
</div>
