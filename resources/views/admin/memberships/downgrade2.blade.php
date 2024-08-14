@extends('layouts.admin')
@section('content')
    <h5 class="bg-white rounded text-dark font-weight-bold p-3 mb-3">
        {{ trans('cruds.membership.title_singular') }} - {{ $membership->service_pricelist->service->name }}
        ( <span class="text-primary">{{ $membership->member->name }}</span> )
    </h5>
    <form method="POST" action="{{ route('admin.membership.storeDowngrade',$membership->id) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>{{ trans('cruds.membership.fields.downgrade') }} {{ trans('cruds.membership.title_singular') }}</h5>
            </div>

            <div class="card-body">

                <div class="row form-group">
                    <div class="col-md-6">
                        <label class="required" for="member_id">{{ trans('cruds.membership.fields.member') }}</label>
                        <select class="form-control {{ $errors->has('member') ? 'is-invalid' : '' }}" name="member_id" id="member_id" required readonly>
                            <option value="{{ $membership->member_id }}" {{ old('member_id') == $membership->member_id ? 'selected' : '' }}  >{{ $membership->member->memberPrefix().$membership->member->member_code .' - '. $membership->member->name }}</option>
                        </select>
                        @if($errors->has('member'))
                            <div class="invalid-feedback">
                                {{ $errors->first('member') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.member_helper') }}</span>
                    </div>
                    
                    @if (config('domains')[config('app.url')]['sales_by'] == true ? 'required' : '' )
                        <div class="col-md-6">
                            <label class="required"
                                for="sales_by_id">{{ trans('cruds.member.fields.sales_by') }}</label>
                                <select class="form-control select2 {{ $errors->has('sales_by') ? 'is-invalid' : '' }}"
                                    name="sales_by_id" id="sales_by_id" required>
                                    <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach ($sales_bies as $id => $entry)
                                        <option value="{{ $id }}" {{ $membership->sales_by_id == $id ? 'selected' : '' }}>
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
                    @else
                        <input type="hidden" name="sales_by_id" value="{{ $membership->sales_by_id }}">
                    @endif

                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="notes">{{ trans('cruds.member.fields.notes') }}</label>
                        <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes"
                            id="notes">{{ old('notes') }}</textarea>
                        @if ($errors->has('notes'))
                            <div class="invalid-feedback">
                                {{ $errors->first('notes') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.notes_helper') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                ENTER DETAILS OF THE SUBSCRIPTION
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-3">
                        <label class="required" for="service_pricelist_id">Price list</label>
                        <select onchange="getExpiry()" class="form-control select2 {{ $errors->has('service_pricelist_id') ? 'is-invalid' : '' }}"
                            name="service_pricelist_id" id="service_pricelist_id" required>
                            <option  selected disabled hidden>Select Service</option>
                            @foreach ($pricelists as $pricelist)
                                <option value="{{ $pricelist->id }}" {{ $pricelist->id == $membership->service_pricelist_id ? 'selected' : '' }}>
                                    {{ $pricelist->name }}  </option>
                            @endforeach
                        </select>
                        @if ($errors->has('service_pricelist_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('service_pricelist_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.service_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="start_date">{{ trans('cruds.membership.fields.start_date') }}</label>
                        <input class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="date"
                            name="start_date" id="start_date" onblur="getExpiry()" min="{{ $membership->start_date }}" value="{{ $membership->start_date }}" required readonly>
                        @if ($errors->has('start_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('start_date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.start_date_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="end_date">{{ trans('cruds.membership.fields.end_date') }}</label>
                        <input class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="date"
                            name="end_date" id="end_date" value="{{ old('end_date',$membership->end_date) }}" required readonly>
                        @if ($errors->has('end_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('end_date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="trainer_id">{{ trans('cruds.member.fields.trainer') }}</label>
                        <select class="form-control  {{ $errors->has('trainer_id') ? 'is-invalid' : '' }}" name="trainer_id" id="trainer_id" required>
                            @foreach ($trainers as $id => $entry)
                                <option value="{{ $id }}" {{ old('trainer_id') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('trainer_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('trainer_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.trainer_helper') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                ENTER DETAILS OF THE INVOICE
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-4">
                        <label class="required" for="invoice_id">{{ trans('cruds.payment.fields.invoice') }}</label>
                        <input type="text" class="form-control" name="invoice_id" value="{{ $invoice->id }}" readonly>
                        @if ($errors->has('invoice_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('invoice_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.invoice_helper') }}</span>
                    </div>

                    <div class="col-md-4">
                        <label class="required" for="net_amount">{{ trans('cruds.invoice.fields.net_amount') }}</label>
                        <input type="text" class="form-control" name="net_amount" id="net_amount" value="{{ 0 }}" readonly>
                        @if ($errors->has('net_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('net_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.net_amount_helper') }}</span>
                    </div>

                    <div class="col-md-4">
                        <label class="required" for="received_amount">{{ trans('cruds.invoice.fields.received_amount') }}</label>
                        <input type="text" class="form-control" name="received_amount" id="received_amount" value="{{ $invoice->payments_sum_amount }}" readonly>
                        @if ($errors->has('received_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('received_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.received_amount_helper') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </div>
    </form>



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

    <script>
        function getExpiry() {
            var service_pricelist_id = $("#service_pricelist_id").val();
            var url = "{{ route('admin.getServiceByPricelist',[':id', ':date']) }}",
            url = url.replace(':id', service_pricelist_id);
            url = url.replace(':date', $('#start_date').val());
            $.ajax({
                method:'GET',
                url:url,
                success:function(response){
                    if (response.pricelist.service.trainer == 0) {
                        $('#trainer_id').attr('disabled','disabled');
                        $('#trainer_id').val('');
                    }else{
                        $('#trainer_id').removeAttr('disabled');
                        $('#trainer_id').select2();
                    }
                    $("#end_date").val(response.expiry)
                    // $('#membership_fee').val(response.pricelist.amount);
                    $('#net_amount').val( response.pricelist.amount );

                    $('#discount').on('change',function()
                    {
                        if ($('#discount').val() <= 100) {
                            $('#discount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount_amount').val( $('#membership_fee').val() * $('#discount').val() / 100 );

                            $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );
                        }else{
                            $('#discount').removeClass('is-valid').addClass('is-invalid');
                        }
                    })

                    $('#discount_amount').on('change',function()
                    {
                        if ($('#discount_amount').val() <= $('#membership_fee').val()) {
                            $('#discount_amount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount').val(  $('#discount_amount').val() / $('#membership_fee').val() * 100 );

                            $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );
                        }else{
                            $('#discount_amount').removeClass('is-valid').addClass('is-invalid');
                        }
                    })

                    
                    $('#amount_pending').val( $('#net_amount').val() - $('#received_amount').val() );
                }
            })
        }

        function getAccounts() {
            var accounts = $('#account_id').val();

            @foreach ($accounts as $id => $name)
                if(accounts.includes("{{ str_replace(' ', '', $name) }}"))
                {
                    $('#{{ str_replace(' ', '', $name) }}').removeAttr('readonly');
                }else{
                    $('#{{ str_replace(' ', '', $name) }}').attr('readonly',true);
                    $('#{{ str_replace(' ', '', $name) }}').val(0);
                }

                $('#{{ str_replace(' ', '', $name) }}').on('keyup',function(){
                    var sum = 0;
                    $('.accounts').each(function(){
                        sum += parseFloat($(this).val());
                    });
                    
                    $('#received_amount').val(parseFloat(sum));

                    if(parseFloat($('#received_amount').val()) > parseFloat($("#net_amount").val()) ){
                        $('.accounts').removeClass('is-valid').addClass('is-invalid');
                        $('.accounts').val(0)
                        $('.reminderCard').slideUp();
                        
                    }else if(parseFloat($('#received_amount').val()) < parseFloat($("#net_amount").val())) {
                        $('.accounts').removeClass('is-invalid').addClass('is-valid');
                        $('#amount_pending').val( $('#net_amount').val() - $('#received_amount').val() );
                        $('.reminderCard').slideDown();

                    }else{
                        $('.accounts').removeClass('is-invalid').addClass('is-valid');
                        $('#amount_pending').val( $('#net_amount').val() - $('#received_amount').val() );
                        $('.reminderCard').slideUp();                        
                    }
                })
            @endforeach
        }         
    </script>
@endsection
