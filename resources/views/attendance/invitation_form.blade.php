<!-- Invitation -->
{{-- style="z-index: 99999" --}}
<div class="modal fade" id="invitation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.invite') }}</h5>
            </div>
            <form action="{{ route('admin.invitation') }}" method="post">
                @csrf
                <input type="hidden" name="membership_id" value="{{ $main_membership->id ?? '' }}">
                <div class="modal-body">
                    <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="lead-tab" data-toggle="pill" href="#lead" role="tab" aria-controls="lead" aria-selected="true">
                                <i class="fa fa-user"></i> {{ trans('cruds.lead.title_singular') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="create-new-tab" data-toggle="pill" href="#create-new" role="tab" aria-controls="create-new" aria-selected="false">
                                <i class="fa fa-plus-circle"></i> {{ trans('global.create') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active py-3" id="lead" role="tabpanel" aria-labelledby="lead-tab">
                            @livewire('search-lead-component')
                        </div>
                        <div class="tab-pane fade" id="create-new" role="tabpanel" aria-labelledby="create-new-tab">
                            <div class="form-group">
                                <label for="name" class="required">{{ trans('cruds.lead.fields.name') }}</label>
                                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                                    id="name" value="{{ old('name', '') }}">
                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                <span class="help-block">{{ trans('cruds.lead.fields.name_helper') }}</span>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="required">{{ trans('cruds.lead.fields.phone') }}</label>
                                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text"
                                    name="phone" id="phone" value="{{ old('phone', '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" min="11" max="11">
                                @if ($errors->has('phone'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('phone') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.lead.fields.phone_helper') }}</span>
                            </div>

                            <div class="form-group">
                                <label class="required">{{ trans('cruds.lead.fields.gender') }}</label>
                                <select class="form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}" name="gender"
                                    id="gender">
                                    <option value disabled {{ old('gender', null) === null ? 'selected' : '' }}>
                                        {{ trans('global.pleaseSelect') }}</option>
                                    @foreach (App\Models\Lead::GENDER_SELECT as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('gender', '') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('gender'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('gender') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.lead.fields.gender_helper') }}</span>
                            </div>

                            <div class="form-group">
                                <label for="branch_id" class="required">{{ trans('cruds.branch.title_singular') }}</label>
                                <select name="branch_id" id="branch_id" class="form-control">
                                    <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="required">{{ trans('cruds.lead.fields.followup') }}</label>
                                <input class="form-control date {{ $errors->has('followup') ? 'is-invalid' : '' }}" type="text"
                                    name="followup" id="followup"
                                    value="{{ old('followup') ?? date('Y-m-d') }}" required>
                                @if ($errors->has('followup'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('followup') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.lead.fields.followup_helper') }}</span>
                            </div>

                            {{-- <div class="form-group">
                                <label for="sales_by_id" class="required">{{ trans('cruds.lead.fields.sales_by') }}</label>
                                <select name="sales_by_id" id="sales_by_id" class="form-control">
                                    <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach (\App\Models\User::whereRelation('roles','title','Sales')->pluck('name','id') as $id => $sale)
                                        <option value="{{ $id }}" >{{ $sale }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ trans('global.cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>