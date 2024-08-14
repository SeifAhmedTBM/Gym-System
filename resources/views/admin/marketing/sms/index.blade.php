@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#sendSMSModal">
            <i class="fa fa-paper-plane"></i> {{ trans('global.send_sms') }}
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fa fa-comments"></i> {{ trans('global.send_sms') }}</h5>
    </div>
    <div class="card-body">
        <table class="table text-center shadow-sm table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('global.message') }}</th>
                    <th>{{ trans('global.sent_by') }}</th>
                    <th>{{ trans('global.created_at') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sms as $msg)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $msg->message }}</td>
                    <td>{{ $msg->user->name }}</td>
                    <td>{{ $msg->created_at->toFormattedDateString() }}</td>
                    <td>

                        <a href="{{ route('admin.marketing.sms.show', $msg->id) }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-eye"></i> {{ trans('global.view') }}
                        </a>
                        <button data-toggle="modal" data-target="#deleteModal" data-id="{{ $msg->id }}" onclick="deleteRecord(this)" class="btn btn-sm btn-danger">
                            <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">{{ trans('global.no_data_available') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $sms->links() }}
    </div>
</div>
@include('admin.marketing.sms.modals')
@endsection

@section('scripts')
    <script>
        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('#numbers');
        // initialize Tagify on the above input node reference
        new Tagify(input)

        function deleteRecord(button) {
            let wp_id = $(button).data('id');
            let url = "{{ route('admin.marketing.sms.destroy', ':id') }}";
            url = url.replace(':id', wp_id);
            $("#delete_form").attr('action', url);
        }
    </script>
@endsection