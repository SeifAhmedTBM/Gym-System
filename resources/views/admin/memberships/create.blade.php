@extends('layouts.admin')
@section('content')
    <div class="row subscription" style="display: none">
        <div class="col-12">
            <div class="alert">
                <h2 class="text-center"><span id="member_name"></span></h2>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.memberships.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>{{ trans('global.create') }} {{ trans('cruds.membership.title_singular') }}</h5>
            </div>

            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <label class="required" for="member_code">{{ trans('cruds.membership.fields.member') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">{{ Auth()->user()->employee && Auth()->user()->employee->branch_id != NULL ? Auth()->user()->employee->branch->member_prefix : '' }}</span>
                            </div>
                            {{-- <select name="member_code" id="member_code" class="js-data-example-ajax form-control">
                                
                            </select> --}}
                            <input type="number" class="form-control" name="member_code" id="member_code" placeholder="Member Code" onkeyup="getMember()">
                        </div>
                        <small class="text-danger">Type member id to make membership</small>
                      
                
                        <input type="hidden" value="" id="member_id" name="member_id" />
                        <span class="help-block">{{ trans('cruds.membership.fields.member_helper') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card subscription" style="display: none">
            @include('partials.subscription_details')
            @include('partials.invoices_details')
            @include('partials.payments_details')
            @include('partials.invoice_reminder')
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
    @include('partials.create_member_transfer_js')
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
        var url = "{{ route('admin.getMember') }}";
        var member_code = $('#member_code').val();
        $('.js-data-example-ajax').select2({
            ajax: {
                url: 'https://api.github.com/search/repositories',
                dataType: 'json'
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
        });
        function getMember()
        {
            var member_code = $('#member_code').val();
            var branch_id =  "{{  Auth()->user()->employee && Auth()->user()->employee->branch_id != NULL ? Auth()->user()->employee->branch_id : '' }}";
            console.log('branch_id'+branch_id);
            console.log('member_code'+member_code);
            var url = "{{ route('admin.getMember') }}";
            $.ajax({
                method : 'POST',
                url : url,
                _token: $('meta[name="csrf-token"]').attr('content'),
                data : {
                    member_code:member_code,
                    branch_id:branch_id,
                    _token: _token
                },
                success:function(data){
                    console.log(data)
                    $('.subscription').slideDown();
                    $('#member_name').text(data.member.name + ' ' + data.member.phone);
                    $('.alert').removeClass('alert-danger').addClass('alert-success');
                    $("#member_id").val(data.member.id);
                },error: function (error) {
                    $('.subscription').slideDown();
                    $('#member_name').text('Member Not Found')
                    $('.alert').removeClass('alert-success').addClass('alert-danger')
                },
            })
        }
    </script>
    
@endsection
