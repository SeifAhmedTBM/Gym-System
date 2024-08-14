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
    function getPricelists() 
    {
        var service_type_id = $('#service_type_id').val();
        var url = "{{ route('admin.getPricelistsByServiceType',':id') }}",

        url = url.replace(':id',service_type_id);
        $.ajax({
            method : 'GET',
            url : url,
            success:function(response){
                $('#service_pricelist_id').empty();
                $('#service_pricelist_id').append(`<option>{{ trans('global.pleaseSelect') }}</option>`);
                response.pricelists.forEach(resp => {
                    $("#service_pricelist_id").append(`<option value='${resp.id}'>${resp.name}</option>`)
                })
            }
        });
    }

    function getExpiry() {
        var max_discount = "{{ $setting->max_discount }}";
        var service_pricelist_id = $("#service_pricelist_id").val();
        var url = "{{ route('admin.getServiceByPricelist',[':id', ':date']) }}",
        url = url.replace(':id', service_pricelist_id);
        url = url.replace(':date', $('#start_date').val());
        $("#discount").val(0);
        $("#discount_amount").val(0);
        $.ajax({
            method:'GET',
            url:url,
            success:function(response){
                if (response.pricelist.service.trainer == 0) {
                    $('#trainer_id').attr('disabled','disabled');
                    $('#trainer_id').removeAttr('required');
                    $('#trainer_id').val('');
                }else{
                    $('#trainer_id').removeAttr('disabled');
                    $('#trainer_id').attr('required','required');
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

    function getBranch() {
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

                response.accounts.forEach(resp => {
                    $('#accounts_div').empty();
                    $('#accounts_div').append(`
                        <input type="hidden" name="account_ids[]" value="${resp.id}">
                        <div class="col-md-3">
                            <label class="required" for="${resp.name}">${resp.name}</label>
                            <input class="form-control accounts {{ $errors->has('cash_amount') ? 'is-invalid' : '' }}" type="number"
                                name="account_amount[]" id="${resp.id}" required readonly>
                            <span class="help-block">{{ trans('cruds.payment.fields.cash_amount_helper') }}</span>
                        </div>
                    `)
                })
            }
        });
    }

    function getAccounts() {
        var accounts = $('#account_id').val();

        @foreach ($accounts as $id => $name)
            if(accounts.includes("{{ $id }}"))
            {
                $('#{{ $id }}').removeAttr('readonly');
            }else{
                $('#{{ $id }}').attr('readonly',true);
                $('#{{ $id }}').val(0);
            }

            $('#{{ $id }}').on('keyup',function(){
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

    function getStatus()
    {
        var status_id = $('#member_status_id').val();
        var url = "{{ route('admin.getMemberStatus',[':id', ':date']) }}",
        url = url.replace(':id', status_id);
        url = url.replace(':date', "{{date('Y-m-d')}}");

        $.ajax({
            method : 'GET',
            url : url,
            success:function(response)
            {
                $('#due_date').val(response.due_date);
            }
        });
    }  

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

    $('#phone').on('keyup',function(){
        if ($('#phone').val().length == 11) {
            $('#phone').removeClass('is-invalid').addClass('is-valid');
        }else{
            $('#phone').removeClass('is-valid').addClass('is-invalid');
        }
    })
    
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
    </script>
@endif