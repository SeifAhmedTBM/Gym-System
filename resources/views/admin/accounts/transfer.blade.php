@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <div class="card ">
                <div class="card-body bg-success text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ number_format($account->balance) }} EGP</h5>
                    <h5><i class="fa fa-money"></i>
                        {{ trans('cruds.account.fields.balance') }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <form action="{{ route('admin.account-transfer.store', $account->id) }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('global.transfer') }} {{ trans('global.from') }} {{ $account->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="to_account">{{ trans('cruds.account.title_singular') }}</label>
                                <select name="to_account" id="to_account" class="form-control select2">
                                    <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach ($accounts as $id => $entry)
                                        <option value="{{ $id }}">{{ $entry }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="amount">{{ trans('global.amount') }}</label>
                                <input type="number" class="form-control" value="0" step="0.001" id="amount"
                                    name="amount">
                            </div>

                            <div class="col-md-6">
                                <label for="amount">Date</label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" id="created_at"
                                    name="created_at">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submit"
                            disabled>{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('#amount').on('keyup', function() {
            if (parseFloat($('#amount').val()) <= {{ $account->balance }}) {
                $('#amount').removeClass('is-invalid').addClass('is-valid');
                $('#submit').removeAttr('disabled');
            } else {
                $('#amount').removeClass('is-valid').addClass('is-invalid');
                $('#amount').val(0);
                $('#submit').attr('disabled');
            }
        })
    </script>
@endsection
