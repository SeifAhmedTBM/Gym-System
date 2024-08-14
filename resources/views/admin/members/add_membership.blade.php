@extends('layouts.admin')
@section('content')
    <form method="POST" action="{{ route('admin.member.addNewMembership',$member->id) }}" enctype="multipart/form-data">
        @csrf
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
                $('#parent_phone').val(null);
                $('#parent_details').val(null);
                $('#phone').attr('disabled',false);
                $('#national').attr('disabled',false);
            }
        });
  </script>
@endsection
