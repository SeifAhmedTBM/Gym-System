@extends('layouts.admin')
@section('content')
    <form action="{{ route('admin.membership-schedule.store',$schedule->id) }}" method="post">
        @csrf
        <div class="form-group">
            <div class="card">
                <div class="card-header">
                    Add Membership To {{ $schedule->session->name ?? '-' }} - {{ $schedule->trainer->name ?? '-' }}
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="membership_id">{{ trans('cruds.membership.title_singular') }}</label>
                            <select name="membership_id" id="membership_id" class="form-control select2">
                                @foreach ($memberships as $membership)
                                    <option value="{{ $membership->id }}">{{ \App\Models\Setting::first()->member_prefix.$membership->member->member_code ?? '-' }} - {{ $membership->member->name ?? '-' }} - {{ $membership->service_pricelist->name ?? '-' }} - {{ $membership->status ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-check"></i> {{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection