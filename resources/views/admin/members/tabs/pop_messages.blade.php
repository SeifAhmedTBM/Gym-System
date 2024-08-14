<div class="tab-pane fade" id="popMessages" role="tabpanel" aria-labelledby="popMessages-tab">
    {{-- @can('add_notes') --}}
    @if ($member->pop_messages->count() < 1)
        <div class="form-group">
            <a href="{{ route('admin.popMessage.create', $member->id) }}" class="btn btn-info btn-xs">
                <i class="fas fa-plus"></i> &nbsp; {{ trans('global.pop_messages') }}
            </a>
        </div>
    @endif
    {{-- @endcan --}}
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.message') }}</th>
                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->pop_messages as $message)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $message->message ?? '-' }}</td>
                            <td>{{ $message->created_by->name ?? '-' }}</td>
                            <td>{{ $message->created_at }}</td>
                            <td>
                                <a href="{{ route('admin.popMessage.show', $message->id) }}" target="_blank"
                                    class="btn btn-info btn-xs">
                                    <i class="fa fa-eye"></i> {{ trans('global.show') }}
                                </a>
                                @can('delete_notes')
                                    <form action="{{ route('admin.popMessage.destroy', $message->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <i class="fa fa-trash"></i> &nbsp; Delete
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <td colspan="4" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
