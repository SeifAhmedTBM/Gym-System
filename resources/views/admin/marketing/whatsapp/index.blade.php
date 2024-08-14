@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#sendWhatsappMessage">
            <i class="fa fa-paper-plane"></i> {{ trans('global.send_whatsapp_message') }}
        </button>
    </div>
</div>
<div class="card shadow">
    <div class="card-header">
        <h5><i class="fab fa-whatsapp"></i> {{ trans('global.whatsapp') }}</h5>
    </div>
    <div class="card-body">
        <table class="table shadow-sm text-center table-bordered table-hover table-striped">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>{{ trans('global.messages') }}</th>
                    <th>{{ trans('global.sent_by') }}</th>
                    <th>{{ trans('global.created_at') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($whatsapp_messages as $wpm)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $wpm->message }}</td>
                        <td>{{ $wpm->user->name }}</td>
                        <td>{{ $wpm->created_at->toFormattedDateString() }}</td>
                        <td>
                            <a href="{{ route('admin.marketing.whatsapp.show', $wpm->id) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> {{ trans('global.view') }}
                            </a>
                            <button data-toggle="modal" data-target="#deleteModal" data-id="{{ $wpm->id }}" onclick="deleteRecord(this)" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="font-weight-bold text-uppercase">@lang('global.no_data_available')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@include('admin.marketing.whatsapp.modals')
@endsection

@section('scripts')
    <script>
        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('input[name=numbers]');
        // initialize Tagify on the above input node reference
        new Tagify(input)

        function deleteRecord(button) {
            let wp_id = $(button).data('id');
            let url = "{{ route('admin.marketing.whatsapp.destroy', ':id') }}";
            url = url.replace(':id', wp_id);
            $("#delete_form").attr('action', url);
        }
    </script>
@endsection