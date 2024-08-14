@extends('layouts.admin')
@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5>{{ trans('global.email_campaigns') }}</h5>
    </div>
    <div class="card-body">
        <a href="{{ route('admin.marketing.mails.create') }}" class="btn btn-primary float-right mb-2">
            <i class="fa fa-plus-circle fa-lg"></i>
        </a>
        <div class="table-responsive">
            <table class="table shadow-sm text-center table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.subject') }}</th>
                        <th>{{ trans('global.sent_by') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mailCamps as $mailCamp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $mailCamp->subject }}</td>
                            <td>{{ $mailCamp->user->name }}</td>
                            <td>{{ $mailCamp->created_at->toFormattedDateString() }}</td>
                            <td>
                                <a href="{{ route('admin.marketing.mails.show', $mailCamp->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-eye"></i> {{ trans('global.view_campaign') }}
                                </a>
                                <button data-toggle="modal" data-target="#deleteModal" data-id="{{ $mailCamp->id }}" onclick="deleteRecord(this)" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $mailCamps->links() }}
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{ trans('global.delete') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['id' => 'delete_form', 'method' => 'DELETE']) !!}
            <div class="modal-body">
                <h5 class="font-weight-bold text-danger text-center">
                    {{ trans('global.delete_alert') }}
                </h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function deleteRecord(button) {
            let wp_id = $(button).data('id');
            let url = "{{ route('admin.marketing.mails.destroy', ':id') }}";
            url = url.replace(':id', wp_id);
            $("#delete_form").attr('action', url);
        }
    </script>
@endsection