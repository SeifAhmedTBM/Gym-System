@extends('layouts.admin')
@section('content')
    <form action="{{ route('admin.storeChangeInvoice') }}" method="post">
        @csrf
        <div class="container my-2">
            <div class="card">
                <div class="card-header">
                    Invoice UPDATES
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <textarea name="invoice_ids" class="form-control" rows="10" placeholder="ex: 1,2,3,4 .. etc"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-01') }}" id="" class="form-control">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
            </div>
        </div>
    </form>
@endsection
