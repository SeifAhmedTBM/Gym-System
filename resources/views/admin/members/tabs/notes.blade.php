<div class="tab-pane fade" id="member_notes" role="tabpanel" aria-labelledby="notes-tab">
    @can('add_notes')
        <div class="form-group">
            <a href="{{ route('admin.note.create', $member->id) }}" class="btn btn-info btn-xs">
                <i class="fas fa-plus"></i> &nbsp; {{ trans('cruds.lead.fields.notes') }}
            </a>
        </div>
    @endcan
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('global.notes') }}</th>
                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->Notes as $note)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $note->notes ?? '-' }}</td>
                            <td>{{ $note->created_by->name ?? '-' }}</td>
                            <td>{{ $note->created_at }}</td>
                            <td>
                                @can('edit_notes')
                                    <a href="javascript:void(0)" data-target="#editNotesModal"
                                        class="btn btn-sm btn-success" data-toggle="modal" data-note="{{ $note->notes }}"
                                        data-route="{{ route('admin.note.update', $note->id) }}"
                                        onclick="updateNote(this)">
                                        <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('delete_notes')
                                    <form action="{{ route('admin.note.destroy', $note->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> &nbsp; Delete
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <td colspan="46" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Edit Notes Modal -->
<div class="modal fade" id="editNotesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.edit') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'PUT', 'id' => 'editNotesForm']) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('edit_note', trans('global.notes'), ['class' => 'required']) !!}
                    {!! Form::textarea('edit_note', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    {{ trans('global.close') }}
                </button>
                <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    function updateNote(button) 
    {
        $("#edit_note").text($(button).data('note'));
        $("#editNotesForm").attr('action', $(button).data('route'));
    }
</script>