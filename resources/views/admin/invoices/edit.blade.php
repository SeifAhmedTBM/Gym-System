@extends('layouts.admin')
@section('content')

<form method="POST" action="{{ route("admin.invoices.update", [$invoice->id]) }}" enctype="multipart/form-data">
<div class="card">
    <div class="card-header">
        <i class="far fa-credit-card"></i> {{ trans('global.edit') }} {{ trans('cruds.invoice.title_singular') }}
    </div>

    <div class="card-body">
        @method('PUT')
        @csrf
        <div class="form-group row">
            @if ($invoice->payments()->count() <= 1)
                <div class="col-md-6">
                    <label for="edit_membership">{{ trans('global.edit_membership') }}</label>
                    <select name="edit_membership" onchange="editMembership(this)" id="edit_membership" class="form-control">
                        <option value="no">{{ trans('global.no') }}</option>
                        <option value="yes">{{ trans('global.yes') }}</option>
                    </select>
                </div>
            @endif
            <div class="col-md-6">
                <label for="created_at">Invoice Date </label>
                <input type="date" class="form-control" name="created_at" value="{{ $invoice->created_at->format('Y-m-d') }}">
            </div>
        </div>
    </div>
</div>
<div class="appendMeHere"></div>

<div class="form-group">
    <button class="btn btn-danger" type="submit">
        <i class="fa fa-check-circle"></i> {{ trans('global.save') }}
    </button>
</div>
</form>

<input type="hidden" id="flag" value="0">
@endsection

@section('scripts')
    <script>
        
        function editMembership(select) {
            if($(select).val() == 'yes') {
                $(".appendMeHere").html(`@include('admin.invoices.edit_membership')`);
                $('.select2').select2();
                getExpiry();
                getAccounts();
                $("#discount_amount").trigger('keyup');
                $("#Cash").trigger('keyup');
                $("#account_id").trigger("change");
                // alert(1);
            }else {
                $(".appendMeHere").html('')
            }
        }

        function getPricelists() 
        {
            var service_type_id = $('#service_type_id').val();
            var url = "{{ route('admin.getPricelistsByServiceType',':id') }}",

            url = url.replace(':id',service_type_id);
            $.ajax({
                method : 'GET',
                url : url,
                success:function(response){
                    console.log(response);
                    $('#service_pricelist_id').empty();
                    response.pricelists.forEach(resp => {
                        $("#service_pricelist_id").append(`<option value='${resp.id}'>${resp.name}</option>`)
                    })
                }
            });
        }

      
        function getExpiry() 
        {
            var max_discount = "{{ $setting->max_discount }}";
            var flag = $("#flag").val();
            var end_dates =  "{{ date('Y-m-d',strtotime($invoice->membership->end_date)) }}";
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
                    if(flag == 0){
                        $("#end_date").val(end_dates);
                        $("#flag").val(1);
                    }else{
                        $("#end_date").val(response.expiry);
                    }
                    $('#membership_fee').val(response.pricelist.amount);
                    $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val());


                    $('#discount').on('keyup',function()
                    {
                        if (parseFloat($('#discount').val()) <= max_discount) {
                            $('#discount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount_amount').val( $('#membership_fee').val() * $('#discount').val() / 100 );

                            $('#net_amount').val( $('#membership_fee').val() - $('#discount_amount').val() );
                            // $('#amount_pending').val( $('#membership_fee').val() - $('#discount_amount').val() );
                            $('#amount_pending').val( parseFloat($('#net_amount').val()) - parseFloat($('#received_amount').val()));
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

                            // $('#amount_pending').val( $('#membership_fee').val() - $('#discount_amount').val() );
                            $('#amount_pending').val( parseFloat($('#net_amount').val()) - parseFloat($('#received_amount').val()));

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

                    $('#amount_pending').val( parseFloat($('#net_amount').val()) - parseFloat($('#received_amount').val()) );
                    // $('#received_amount').val(0);
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