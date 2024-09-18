@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.service.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.services.update", [$service->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row form-group">
                <div class="col-md-6">
                    <label class="required" for="expiry">{{ trans('cruds.service.fields.expiry') }}</label>
                    <div class="input-group">
                        <input class="form-control {{ $errors->has('expiry') ? 'is-invalid' : '' }}" type="number" name="expiry" id="expiry" value="{{ old('expiry', $service->expiry) }}" step="0.01" required>
                        <div class="input-group-append">
                            <select name="type" class="form-control">
                                @foreach (App\Models\Service::EXPIRY_TYPES as $db_type => $type_name)
                                    <option value="{{ $db_type }}" {{ $service->type == $db_type ? 'selected' : '' }}>{{ $type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($errors->has('expiry'))
                        <div class="invalid-feedback">
                            {{ $errors->first('expiry') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.expiry_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.service.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.name_helper') }}</span>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-6">
                    <label class="required" for="service_type_id">{{ trans('cruds.service.fields.service_type') }}</label>
                    <select class="form-control select2 {{ $errors->has('service_type') ? 'is-invalid' : '' }}" name="service_type_id" id="service_type_id" required>
                        @foreach($service_types as $id => $entry)
                            <option value="{{ $id }}" {{ (old('service_type_id') ? old('service_type_id') : $service->service_type->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('service_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('service_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.service_type_helper') }}</span>
                </div>
                <input type="hidden" name="status" value="active">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="order">{{ trans('cruds.service.fields.order') }}</label>
                        <input class="form-control {{ $errors->has('order') ? 'is-invalid' : '' }}" type="number" name="order" id="order" value="{{ old('order', $service->order) }}" step="1" required>
                        @if($errors->has('order'))
                            <div class="invalid-feedback">
                                {{ $errors->first('order') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.service.fields.order_helper') }}</span>
                    </div>
                </div>
                
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="trainer">{{ trans('cruds.service.fields.trainer') }}</label>
                    <select name="trainer" id="trainer" class="form-control select2 {{ $errors->has('trainer') ? 'is-invalid' : '' }}">
                        <option value="0" {{ $service->trainer == false ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $service->trainer == true ? 'selected' : '' }}>Yes</option>
                    </select>
                    @if($errors->has('trainer'))
                        <div class="invalid-feedback">
                            {{ $errors->first('trainer') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.trainer_helper') }}</span>
                </div>
                
                <div class="col-md-6">
                    <label class="required" for="sales_commission">{{ trans('global.commission') }}</label>
                    <select name="sales_commission" id="sales_commission" class="form-control select2 {{ $errors->has('sales_commission') ? 'is-invalid' : '' }}">
                       
                        @foreach (\App\Models\Service::SALES_COMMISSIONS as $id => $entry)
                            <option value="{{ $id }}" {{ $service->sales_commission == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('sales_commission'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sales_commission') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div id="logo-cover-fields" style="display: none;">
              
                    <div class="form-group">
                        <label class="required" for="logo">{{ trans('cruds.gallery.fields.images') }} Logo</label>
                        <div class="needsclick dropzone {{ $errors->has('logo') ? 'is-invalid' : '' }}" id="logo-dropzone">
                        </div>
                        @if($errors->has('logo'))
                            <div class="invalid-feedback">
                                {{ $errors->first('logo') }}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label class="required" for="cover">{{ trans('cruds.gallery.fields.images') }} Cover</label>
                        <div class="needsclick dropzone {{ $errors->has('cover') ? 'is-invalid' : '' }}" id="cover-dropzone">
                        </div>
                        @if($errors->has('cover'))
                            <div class="invalid-feedback">
                                {{ $errors->first('cover') }}
                            </div>
                        @endif

                    </div>
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
    Dropzone.options.logoDropzone = {
        url: '{{ route('admin.services.storeMedia') }}',
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
            $('form').find('input[name="logo"]').remove()
            $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
        },
        removedfile: function(file) {
            file.previewElement.remove()
            if (file.status !== 'error') {
                $('form').find('input[name="logo"]').remove()
                this.options.maxFiles = this.options.maxFiles + 1
            }
        },
        init: function() {
            @if (isset($service) && $service->logo)
            var file = {!! json_encode($service->logo) !!}
            this.options.addedfile.call(this, file)
            this.options.thumbnail.call(this, file, file.preview)
            file.previewElement.classList.add('dz-complete')
            $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')
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
    Dropzone.options.coverDropzone = {
        url: '{{ route('admin.services.storeMedia') }}',
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
            $('form').find('input[name="cover"]').remove()
            $('form').append('<input type="hidden" name="cover" value="' + response.name + '">')
        },
        removedfile: function(file) {
            file.previewElement.remove()
            if (file.status !== 'error') {
                $('form').find('input[name="cover"]').remove()
                this.options.maxFiles = this.options.maxFiles + 1
            }
        },
        init: function() {
            @if (isset($service) && $service->cover)
            var file = {!! json_encode($service->cover) !!}
            this.options.addedfile.call(this, file)
            this.options.thumbnail.call(this, file, file.preview)
            file.previewElement.classList.add('dz-complete')
            $('form').append('<input type="hidden" name="cover" value="' + file.file_name + '">')
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
    $(document).ready(function() {
        $('#service_type_id').change(function() {
            // console.log($(this).val());
            var selectedType = $(this).val();
            if (selectedType == {{ $image_service_id }}) {
                $('#logo-cover-fields').show();
            } else {
                $('#logo-cover-fields').hide();
            }
        });
        
        // Trigger the change event on page load to set the initial state
        $('#service_type_id').trigger('change');
    });
</script>
@endsection