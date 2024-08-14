@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.edit') }} {{ trans('cruds.member.title_singular') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.cardNumber.update', [$member->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row form-group">
                    <div class="col-md-6">
                        <label for="card_number">{{ trans('cruds.member.fields.card_number') }}</label>
                        <input class="form-control  {{ $errors->has('card_number') ? 'is-invalid' : '' }}" type="text"
                            name="card_number" id="card_number" value="{{ old('card_number', $member->card_number) }}">
                        @if ($errors->has('card_number'))
                            <div class="invalid-feedback">
                                {{ $errors->first('card_number') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
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
@endsection
