<div class="tab-pane fade" id="freeSessions" role="tabpanel" aria-labelledby="freeSessions-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('cruds.membership.title_singular') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->freeSessions as $session)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $session->membership->service_pricelist->name ?? '-' }}</td>
                            <td>{{ $session->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
                <tfoot>
                    <td>{{ trans('global.total') }}</td>
                    <td>{{ $member->free_sessions_count }}</td>
                </tfoot>
            </table>
        </div>
    </div>
</div>
