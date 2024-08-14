<div id="transfer_to_branch" class="modal" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="post">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Transfer To Branch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="from_branch">From Branch</label>
                        <select class="form-control" name="from_branch" readonly>
                            <option value="{{ NULL }}">From Branch</option>
                            @foreach (App\Models\Branch::pluck('name','id') as $from_branch_id => $from_branch_name)
                                <option value="{{ $from_branch_id }}">{{ $from_branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="branch_id">To Branch</label>
                        <select class="form-control" name="branch_id">
                            <option value="{{ NULL }}">Branch</option>
                            @foreach (App\Models\Branch::pluck('name','id') as $branch_id => $branch_name)
                                <option value="{{ $branch_id }}">{{ $branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> Confirm</button>
                </div>
            </div>
        </form>
    </div>
</div>