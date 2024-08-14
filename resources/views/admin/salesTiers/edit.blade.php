@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.salesTier.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.sales-tiers.update", [$salesTier->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="name" class="required">{{ trans('global.name') }}</label>
                <input type="text" required name="name" id="name" class="form-control" value="{{ $salesTier->name }}">
            </div>
            <div class="form-group">
                <label for="month">{{ trans('global.month') }}</label>
                <input type="month" name="month" id="month" value="{{ $salesTier->month ?? date('Y-m') }}" class="form-control">
            </div>

            <div class="form-group">
                <label class="required">{{ trans('cruds.salesTier.fields.type') }}</label>
                <select onchange="getUsersWithRole(this)" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type" required>
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\SalesTier::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ $key == $salesTier->type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.salesTier.fields.type_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="users" class="required">{{ trans('global.users') }}</label>
                <select name="users[]" multiple required id="users" class="form-control select2">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id,$salesTier->sales_tiers_users->pluck('user_id')->toArray()) ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-sm select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-sm deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
            </div>

            @foreach ($salesTier->sales_tiers_ranges as $range)
                <input type="hidden" name="range[]" value="{{ $range->id }}">
                <div class="form-row ">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="required" for="range_from">{{ trans('cruds.salesTier.fields.range_from') }}</label>
                            <input class="form-control" type="number" name="range_from[]" id="range_from" step="0.01" required value="{{ $range->range_from }}">
                            @if($errors->has('range_from'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('range_from') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.salesTier.fields.range_from_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="required" for="range_to">{{ trans('cruds.salesTier.fields.range_to') }}</label>
                            <input class="form-control" type="number" name="range_to[]" id="range_to" step="0.01" required value="{{ $range->range_to }}">
                            @if($errors->has('range_to'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('range_to') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.salesTier.fields.range_to_helper') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="required" for="commission">{{ trans('cruds.salesTier.fields.commission') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</span>
                                </div>
                                <input class="form-control {{ $errors->has('commission') ? 'is-invalid' : '' }}" type="number" name="commission[]" id="commission" step="0.01" required value="{{ $range->commission }}">

                                @if ($loop->iteration == 1)
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="button" onclick="appendRangesForm()">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @if($errors->has('commission'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('commission') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.salesTier.fields.commission_helper') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="appendHere"></div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.salesTier.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\SalesTier::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ $key == $salesTier->status ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.salesTier.fields.status_helper') }}</span>
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
        function appendRangesForm() {
            $(".appendHere").append(`
            <div class="form-row rangesForm">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required" for="range_from">{{ trans('cruds.salesTier.fields.range_from') }}</label>
                        <input class="form-control" type="number" name="range_from[]" id="range_from" step="0.01" required>
                        @if($errors->has('range_from'))
                            <div class="invalid-feedback">
                                {{ $errors->first('range_from') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.salesTier.fields.range_from_helper') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required" for="range_to">{{ trans('cruds.salesTier.fields.range_to') }}</label>
                        <input class="form-control" type="number" name="range_to[]" id="range_to" step="0.01" required>
                        @if($errors->has('range_to'))
                            <div class="invalid-feedback">
                                {{ $errors->first('range_to') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.salesTier.fields.range_to_helper') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required" for="commission">{{ trans('cruds.salesTier.fields.commission') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">%</span>
                            </div>
                            <input class="form-control" type="number" name="commission[]" id="commission" step="0.01" required>
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="button" onclick="removeParentRangesForm(this)">
                                    <i class="fa fa-times-circle"></i>
                                </button>
                            </div>
                        </div>
                        <span class="help-block">{{ trans('cruds.salesTier.fields.commission_helper') }}</span>
                        @if($errors->has('commission'))
                            <div class="invalid-feedback">
                                {{ $errors->first('commission') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            `);
        }
        function removeParentRangesForm(button) {
            $(button).closest('.rangesForm').remove();
        }

        function getUsersWithRole(select) {
            let user_type = $(select).val();
            let url = "{{ route('admin.users_get_with_type', ':user_type') }}";
            url = url.replace(':user_type', user_type);
            $("#users").empty();
            $.ajax({
                method: "GET",
                url : url,
                success: function(response) {
                    for(let i = 0 ; i < response.users.length ; i++) {
                        $("#users").append(`
                            <option value="${response.users[i].id}">${response.users[i].name}</option>
                        `);
                    }
                }
            })
        }
    </script>
@endsection