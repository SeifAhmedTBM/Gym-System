<div class="tab-pane fade" id="invitations" role="tabpanel" aria-labelledby="invitations-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('cruds.lead.title_singular') }}</th>
                        <th>{{ trans('cruds.lead.fields.phone') }}</th>
                        <th>{{ trans('cruds.membership.title_singular') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->invitations as $invite)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.leads.show', $invite->lead_id) }}"
                                    target="_blank">{{ $invite->lead->name ?? '-' }}</a>
                            </td>
                            <td>{{ $invite->lead->phone ?? '-' }}</td>
                            <td>{{ $invite->membership->service_pricelist->name ?? '-' }}</td>
                            <td>{{ $invite->created_at->toFormattedDateString() . ' , ' . $invite->created_at->format('g:i A') }}
                            </td>
                            <td>
                                <a class="btn btn-xs btn-success"
                                    href="{{ route('admin.member.transfer', $invite->lead_id) }}">
                                    <i class="fa fa-exchange"></i> {{ trans('global.transfer') }}
                                </a>

                                @can('delete_invitations')
                                    <form
                                        action="{{ route('admin.invitation.destroy', $invite->id) }}"
                                        method="post" onsubmit="return confirm('Are you sure?');"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-xs" type="submit">
                                            <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>