@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.externalPayment.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.external-payments.store") }}" enctype="multipart/form-data">
            @csrf
            @livewire('search-lead-component')

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="title">{{ trans('cruds.externalPayment.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" >
                    @if($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.externalPayment.fields.title_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label for="external_payment_category_id" class="required">Other Revenue Category</label>
                    <select name="external_payment_category_id" id="external_payment_category_id" class="from-control select2" required>
                        <option value="{{ NULL }}" selected disabled hidden>{{ trans('global.pleaseSelect') }}</option>
                        @foreach ($external_payment_categories as $id => $name)
                            <option value="{{ $id }}" {{ old('external_payment_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
           
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.externalPayment.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.externalPayment.fields.amount_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required">{{ trans('cruds.branch.title_singular') }}</label>
                    <select class="form-control {{ $selected_branch == NULL ? 'select2' : '' }} {{ $errors->has('branch_id') ? 'is-invalid' : '' }}" id="branch_id" 
                        onchange="getBranch()" required {{ $selected_branch != NULL ? 'readonly' : '' }}>
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
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="account_id">{{ trans('cruds.externalPayment.fields.account') }}</label>
                    <select class="form-control select2 {{ $errors->has('account') ? 'is-invalid' : '' }}" name="account_id" id="account_id" required>
                        @if ($selected_branch != NULL)
                            @foreach ($selected_branch->accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($errors->has('account'))
                        <div class="invalid-feedback">
                            {{ $errors->first('account') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.externalPayment.fields.account_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="date">Date</label>
                    <input type="date" class="form-control" value="{{date('Y-m-d')}}" id="date" name="date" />
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="notes">{{ trans('cruds.externalPayment.fields.notes') }}</label>
                    <input type="text" value="{{ old('notes') }}" class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">
                    @if($errors->has('notes'))
                        <div class="invalid-feedback">
                            {{ $errors->first('notes') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.externalPayment.fields.notes_helper') }}</span>
                </div>
            </div>
            
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
    <script>
        function selectLead(divElement, leadName) {
            let lead_id = $(divElement).data('id');
            $("#lead_id").val(lead_id);
            $("#search_lead").val(leadName);
            $(".leadsDiv").each(function() {
                $(this).remove();
            })
        }
    </script>

    <script>
        function getBranch() 
        {
            var branch_id = $('#branch_id').val();
            var url = "{{ route('admin.get-branch-accounts',':id') }}";
            url = url.replace(':id',branch_id);
            $.ajax({
                method : 'GET',
                url : url,
                success:function(response)
                {
                    $('#account_id').empty();
                    response.accounts.forEach(resp => {
                        $("#account_id").append(`<option value='${resp.id}'>${resp.name}</option>`)
                    })
                }
            });
        }
    </script>
@endsection