<div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('global.message') }}</th>
                        <th>{{ trans('global.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->messages as $msg)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $msg->message }}</td>
                            <td>{{ $msg->created_at }}</td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">No data Available</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
