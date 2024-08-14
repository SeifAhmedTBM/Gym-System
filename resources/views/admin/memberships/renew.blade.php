@extends('layouts.admin')
@section('content')
    <form method="POST" action="{{ route('admin.membership.storeRenew') }}" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>{{ trans('cruds.membership.fields.renew') }} {{ trans('cruds.membership.title_singular') }} | <span class="badge badge-info">{{ $membership->memberPrefix().$membership->member->member_code .' - '. $membership->member->name }}</span></h5>
            </div>

            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <label class="required" for="member_id">{{ trans('cruds.membership.fields.member') }}</label>
                        <select class="form-control {{ $errors->has('member') ? 'is-invalid' : '' }}" name="member_id" id="member_id"  readonly> 
                            <option value="{{ $membership->member_id }}" {{ old('member_id') == $membership->member_id ? 'selected' : '' }}>{{ $membership->memberPrefix().$membership->member->member_code .' - '. $membership->member->name }}</option>
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

                {{-- <div class="form-group">
                    <label for="notes">{{ trans('cruds.member.fields.notes') }}</label>
                    <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes"
                        id="notes">{{ old('notes') }}</textarea>
                    @if ($errors->has('notes'))
                        <div class="invalid-feedback">
                            {{ $errors->first('notes') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.member.fields.notes_helper') }}</span>
                </div> --}}
            </div>
        </div>

        {{-- subscription details --}}
        @include('partials.subscription_details')

        {{-- invoice details --}}
        @include('partials.invoices_details')


        {{-- payments details --}}
        @include('partials.payments_details')

        {{-- reminders --}}
        @include('partials.invoice_reminder')

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
    @include('partials.create_member_transfer_js')
@endsection
{{-- @section('scripts')
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
        var max_discount = "{{ $setting->max_discount }}";

        $('#discount').on('keyup',function()
        {
            if (parseFloat($('#discount').val()) <= max_discount) {
                $('#discount').removeClass('is-invalid').addClass('is-valid');

                $('#discount_amount').val( $('#membership_fee').val() * $('#discount').val() / 100 );

                $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );
                $('#amount_pending').val( $('#membership_fee').val() - $('#discount_amount').val() );
                
            }else{
                $('#discount').removeClass('is-valid').addClass('is-invalid');
                $('#discount').val(0);
                $('#discount_amount').val(0);
            }
        })

        $('#discount_amount').on('keyup',function()
        {
            if (parseFloat($('#discount_amount').val()) <= parseFloat($('#membership_fee').val())) {
                $('#discount_amount').removeClass('is-invalid').addClass('is-valid');

                $('#discount').val( Math.round($('#discount_amount').val() / $('#membership_fee').val() * 100 ));

                $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );

                $('#amount_pending').val( $('#membership_fee').val() - $('#discount_amount').val() );

                if (parseFloat($('#discount').val()) <= max_discount)
                {
                    $('#discount').removeClass('is-invalid').addClass('is-valid');
                }else{
                    $('#discount').removeClass('is-valid').addClass('is-invalid');
                    $('#discount').val(0);
                    $('#discount_amount').val(0);
                }
            }else{
                $('#discount_amount').removeClass('is-valid').addClass('is-invalid');
                $('#discount').val(0);
                $('#discount_amount').val(0);
            }
        })

        $('#amount_pending').val( $('#net_amount').val() - $('#received_amount').val() );
        $('#received_amount').val(0);
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
                    $('#membership_fee').val(response.pricelist.amount);
                    $('#net_amount').val( $('#membership_fee').val() );

                    $('#discount').on('keyup',function()
                    {
                        if (parseFloat($('#discount').val()) <= max_discount) {
                            $('#discount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount_amount').val( $('#membership_fee').val() * $('#discount').val() / 100 );

                            $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );
                            $('#amount_pending').val( $('#membership_fee').val() - $('#discount_amount').val() );
                            
                        }else{
                            $('#discount').removeClass('is-valid').addClass('is-invalid');
                            $('#discount').val(0);
                            $('#discount_amount').val(0);
                        }
                    })

                    $('#discount_amount').on('keyup',function()
                    {
                        if (parseFloat($('#discount_amount').val()) <= parseFloat($('#membership_fee').val())) {
                            $('#discount_amount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount').val( Math.round($('#discount_amount').val() / $('#membership_fee').val() * 100 ));

                            $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );

                            $('#amount_pending').val( $('#membership_fee').val() - $('#discount_amount').val() );

                            if (parseFloat($('#discount').val()) <= max_discount)
                            {
                                $('#discount').removeClass('is-invalid').addClass('is-valid');
                            }else{
                                $('#discount').removeClass('is-valid').addClass('is-invalid');
                                $('#discount').val(0);
                                $('#discount_amount').val(0);
                            }
                        }else{
                            $('#discount_amount').removeClass('is-valid').addClass('is-invalid');
                            $('#discount').val(0);
                            $('#discount_amount').val(0);
                        }
                    })

                    $('#amount_pending').val( $('#net_amount').val() - $('#received_amount').val() );
                    $('#received_amount').val(0);
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
@endsection --}}
