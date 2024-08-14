@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.edit') }} {{ trans('cruds.member.title_singular') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.members.update', [$member->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="photo">{{ trans('cruds.member.fields.photo') }}</label>
                        <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                            id="photo-dropzone">
                        </div>
                        @if ($errors->has('photo'))
                            <div class="invalid-feedback">
                                {{ $errors->first('photo') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.photo_helper') }}</span>
                    </div>
                </div>

                @if (config('domains')[config('app.url')]['minor'] == true)
                    <div class="row form-group">
                        <div class="col-md-2">
                            {{ trans('global.minor') }}
                        </div>
                        <div class="col-md-1 text-right">
                            <label class="c-switch c-switch-3d c-switch-success">
                                <input type="checkbox" name="minor" id="minor" value="yes" class="c-switch-input" {{ !is_null($member->parent_phone) ? 'checked' : '' }}>
                                <span class="c-switch-slider shadow-none"></span>
                            </label>
                        </div>
                    </div>

                    <div class="row form-group" id="parent" style="{{ $member->parent_phone == NULL ? 'display:none' : '' }}">
                        <div class="col-md-3">
                            <label for="">{{ trans('global.parent_phone') }}</label>
                            <input type="text" class="form-control" name="parent_phone" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="{{ $member->parent_phone }}">
                        </div>
                        <div class="col-md-6">
                            <label for="">{{ trans('global.parent_details') }}</label>
                            <input type="text" class="form-control" name="parent_details" value="{{ $member->parent_details }}">
                        </div>
                    </div>
                @endif

                <div class="row form-group">
                    <div class="col-md-3">
                        <label class="required" for="name">{{ trans('cruds.member.fields.name') }}</label>
                        <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                            id="name" value="{{ old('name', $member->name) }}" required>
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.name_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="phone">{{ trans('cruds.member.fields.phone') }}</label>
                        <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text"
                            name="phone" id="phone" value="{{ old('phone', $member->phone) }}" required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" {{ !is_null($member->parent_phone) ? 'disabled' : '' }}>
                        @if ($errors->has('phone'))
                            <div class="invalid-feedback">
                                {{ $errors->first('phone') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.phone_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="{{ config('domains')[config('app.url')]['national_id'] == true ? 'required' :''}}" for="national">{{ trans('cruds.member.fields.national') }}</label>
                        <input class="form-control {{ $errors->has('national') ? 'is-invalid' : '' }}" type="text"
                            name="national" id="national" value="{{ old('national', $member->national) }}" {{ config('domains')[config('app.url')]['national_id'] == true ? 'required' : '' }} oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" {{ config('domains')[config('app.url')]['national_id'] == true ? 'min="14" max="14" required' :''}} {{ !is_null($member->parent_phone) ? 'disabled' : '' }}>
                            
                            @if ($errors->has('national'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('national') }}
                                </div>
                            @endif
                        <span class="help-block">{{ trans('cruds.member.fields.national_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="{{ config('domains')[config('app.url')]['email'] == true ? 'required' :''}}" for="national">{{ trans('cruds.member.fields.email') }}</label>
                        <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text"
                            name="email" id="email" value="{{ $member->user->email ?? '' }}" {{ config('domains')[config('app.url')]['email'] == true ? 'required' :''}}>

                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        <span class="help-block">{{ trans('cruds.member.fields.email_helper') }}</span>
                    </div>
                </div>

                <div class="row form-group">

                    <div class="col-md-3">
                        <label for="referral_member">{{ trans('cruds.lead.fields.referral_member') }}</label>
                        <input type="text" class="form-control" placeholder="{{ trans('cruds.lead.fields.referral_member') }}" value="{{ old('referral_member', $member->referral_member) }}" name="referral_member" id="referral_member" onblur="referralMember()">
                        <small class="text-danger" id="referral_member_msg"></small>
                    </div>

                 
                    <div class="col-md-3">
                        <label class="required"
                            for="member_code">{{ trans('cruds.member.fields.member_code') }}</label>
                        <input  class="form-control {{ $errors->has('member_code') ? 'is-invalid' : '' }}" type="text"
                            name="member_code" id="member_code" value="{{ old('member_code', $member->member_code) }}"
                            required @cannot('edit_member_code') readonly @endcannot>
                            @if ($errors->has('member_code'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('member_code') }}
                                </div>
                            @endif
                        <span class="help-block">{{ trans('cruds.member.fields.member_code_helper') }}</span>
                    </div>
                    

                    <div class="col-md-3">
                        <label class="required" for="source_id">{{ trans('cruds.member.fields.source') }}</label>
                        <select class="form-control select2 {{ $errors->has('source') ? 'is-invalid' : '' }}"
                            name="source_id" id="source_id" required>
                            @foreach ($sources as $id => $entry)
                                <option value="{{ $id }}"
                                    {{ (old('source_id') ? old('source_id') : $member->source->id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('source'))
                            <div class="invalid-feedback">
                                {{ $errors->first('source') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.source_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label for="whatsapp_number">{{ trans('global.whatsapp') }}</label>
                        <input class="form-control {{ $errors->has('whatsapp_number') ? 'is-invalid' : '' }}" type="text"
                            name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') ?? $member->whatsapp_number }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        @if ($errors->has('whatsapp_number'))
                            <div class="invalid-feedback">
                                {{ $errors->first('whatsapp_number') }}
                            </div>
                        @endif
                    </div>
                </div>


                <div class="row form-group">
                    <div class="col-md-3">
                        <label for="card_number">{{ trans('cruds.member.fields.card_number') }}</label>
                        <input class="form-control  {{ $errors->has('card_number') ? 'is-invalid' : '' }}" type="text"
                            name="card_number" id="card_number" value="{{ old('card_number', $member->card_number) }}">
                        @if ($errors->has('card_number'))
                            <div class="invalid-feedback">
                                {{ $errors->first('card_number') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="dob">{{ trans('cruds.member.fields.dob') }}</label>
                        <input class="form-control {{ $errors->has('dob') ? 'is-invalid' : '' }}" type="date"
                            name="dob" id="dob" value="{{ old('dob', $member->dob) }}" required max="{{ date('Y-m-d') }}">
                        @if ($errors->has('dob'))
                            <div class="invalid-feedback">
                                {{ $errors->first('dob') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.dob_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required">{{ trans('cruds.member.fields.gender') }}</label>
                        <select class="form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}" name="gender"
                            id="gender" required>
                            <option value disabled {{ old('gender', null) === null ? 'selected' : '' }}>
                                {{ trans('global.pleaseSelect') }}</option>
                            @foreach (App\Models\Lead::GENDER_SELECT as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('gender', $member->gender) === (string) $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('gender'))
                            <div class="invalid-feedback">
                                {{ $errors->first('gender') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.gender_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label for="address">{{ trans('cruds.lead.fields.address') }}</label>
                        <select name="address_id" id="address_id"
                            class="form-control select2 {{ $errors->has('address_id') ? 'is-invalid' : '' }}">
                            @foreach ($addresses as $id => $entry)
                                <option value="{{ $id }}" {{ old('address_id') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('address_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('address') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.lead.fields.address_helper') }}</span>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-6">
                        <label for="address_details">{{ trans('cruds.lead.fields.address_details') }}</label>
                        <input type="text" class="form-control {{ $errors->has('address_details') ? 'is-invalid' : '' }}"  name="address_details" id="address_details" value="{{ old('address_details') ?? $member->address_details }}"/>
                        @if ($errors->has('address_details'))
                            <div class="invalid-feedback">
                                {{ $errors->first('address_details') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.lead.fields.notes_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="sales_by_id">{{ trans('cruds.member.fields.sales_by') }}</label>
                        <select class="form-control {{ $errors->has('sales_by') ? 'is-invalid' : '' }}" name="sales_by_id" id="sales_by_id" required  @cannot('edit_sales_by') readonly @endcannot>
                            @foreach ($sales_bies as $id => $entry)
                                <option value="{{ $id }}"
                                    {{ (old('sales_by_id') ? old('sales_by_id') : $member->sales_by->id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('sales_by'))
                            <div class="invalid-feedback">
                                {{ $errors->first('sales_by') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.sales_by_helper') }}</span>
                    </div>

                    <div class="col-md-2">
                        <label for="" class="d-block">
                            {{ trans('cruds.freezeRequest.fields.is_retroactive') }}
                        </label>
                        <label class="c-switch c-switch-3d c-switch-success">
                            <input type="checkbox" name="retroactive" id="retroactive" value="yes" class="c-switch-input">
                            <span class="c-switch-slider shadow-none"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
                        <select name="branch_id" id="branch_id" class="form-control">
                            @foreach ($branches as $id => $name)
                                <option value="{{ $id }}" {{ $member->branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="created_at">{{ trans('cruds.member.fields.created_at') }}</label>
                        <input type="date" class="form-control {{ $errors->has('created_at') ? 'is-invalid' : '' }}" name="created_at"
                            id="created_at" value="{{ old('created_at') ?? $member->created_at->format('Y-m-d') }}">
                        @if ($errors->has('created_at'))
                            <div class="invalid-feedback">
                                {{ $errors->first('created_at') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.notes_helper') }}</span>
                    </div>

                    <div class="col-md-4">
                        <label for="notes">{{ trans('cruds.member.fields.notes') }}</label>
                        <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes"
                            id="notes">{{ old('notes', $member->notes) }}</textarea>
                        @if ($errors->has('notes'))
                            <div class="invalid-feedback">
                                {{ $errors->first('notes') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.notes_helper') }}</span>
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

        Dropzone.options.photoDropzone = {
            url: '{{ route('admin.members.storeMedia') }}',
            maxFilesize: 5, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').find('input[name="photo"]').remove()
                $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="photo"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($member) && $member->photo)
                    var file = {!! json_encode($member->photo) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>

    @if (config('domains')[config('app.url')]['national_id'] == true)
        <script>
            $('#national').on('keyup',function(){
                    if ($('#national').val().length == 14) {
                        $('#national').removeClass('is-invalid').addClass('is-valid');
                    }else{
                        $('#national').removeClass('is-valid').addClass('is-invalid');
                    }
                })

                $('#phone').on('keyup',function(){
                    if ($('#phone').val().length == 11) {
                        $('#phone').removeClass('is-invalid').addClass('is-valid');
                    }else{
                        $('#phone').removeClass('is-valid').addClass('is-invalid');
                    }
                })
        </script>
    @endif

    <script>
            function referralMember()
            {
                var referral_member = $('#referral_member').val();
                var url = "{{ route('admin.referralMember') }}";
                $.ajax({
                    method : 'POST',
                    url : url,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data : {
                        referral_member:referral_member,
                        _token: _token
                    },
                    success:function(data){
                        $('#referral_member_msg').text(data.member.name);
                    },error: function (error) 
                    {
                        if ($('#referral_member').val() !== empty()) 
                        {
                            $('#referral_member_msg').text("{{ trans('global.member_is_not_found') }}");
                        }else{
                            $('#referral_member_msg').text(' ');
                        }
                    },
                })
            }
            referralMember();
    </script>

    <script>
        $("#minor").change(function() {
                if(this.checked == true) {
                    $(".hideMe").slideDown();
                    $('#parent').slideDown();
                    $('#phone').attr('disabled',true);
                    $('#national').attr('disabled',true);
                }else {
                    $(".hideMe").slideUp();
                    $('#parent').slideUp();
                    $('#phone').attr('disabled',false);
                    $('#national').attr('disabled',false);
                }
            });
    </script>
    
@endsection
