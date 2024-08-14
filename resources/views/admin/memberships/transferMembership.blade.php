@extends('layouts.admin')
@section('content')
<form method="POST" action="{{ route("admin.membership.storeTransfer", [$membership->id]) }}" enctype="multipart/form-data">
    <div class="card shadow-sm">
        @method('PUT')
        @csrf
        <div class="card-header font-weight-bold">
            <i class="fa fa-refresh"></i> {{ trans('global.transfer_to_member') }}
        </div>
        <div class="card-body">
            @livewire('transfer-member-component')
        </div>
    </div>
    <div class="appendMeHere">
        @if (old('member_type') == 'new_member')
            <div class="card shadow-sm">
                <div class="card-body">@include('admin.memberships.add_new_member')</div>
            </div>
        @endif
    </div>
    <div class="card shadow-sm">
        <div class="card-header font-weight-bold">
            <i class="fa fa-money-bill"></i> {{ trans('cruds.payment.title_singular') }}
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-4 form-group">
                    {!! Form::label('title', trans('global.title'), ['class' => 'required']) !!}
                    {!! Form::text('title', trans('global.transfer_fees'), ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="col-md-4 form-group">
                    {!! Form::label('amount', trans('global.amount'), ['class' => 'required']) !!}
                    {!! Form::text('amount', null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="col-md-4 form-group">
                    {!! Form::label('account', trans('cruds.account.title_singular'), ['class' => 'required']) !!}
                    {!! Form::select('account', $accounts, null, ['class' => 'select2', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button class="btn btn-success" type="submit">
                <i class="fa fa-check-circle"></i> {{ trans('global.transfer') }}
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
    <script>
        function createNewLead() {
            $(".appendMeHere").html(`
                <div class="card shadow-sm">
                    <div class="card-body">
                        @include('admin.memberships.add_new_member')    
                    </div>    
                </div>
            `);
            $(".select2").select2();
            $("#member_type").val('new_member');
            $(".getMyData").html('');
        }

        function currentMember(button) {
            let member_id = $(button).data('mi');
            let member_name = $(button).data('mn');
            $("#member_type").val(member_id);
            $(".getMyData").html('');
            $("#lead").val(member_name);
            $("#lead").attr('readonly', 'readonly');
            $('#myCreateNew').fadeOut('slow');
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
@endsection