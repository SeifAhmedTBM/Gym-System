@extends('layouts.admin')
@section('content')
<div class="row mb-2">
    <div class="col-md-6 text-left">
        <a href="{{ \URL::previous() }}" class="btn btn-danger">
            <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
        </a>
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.revenue-details-report.export', request()->all())]) !!}
        <button type="submit" class="btn btn-success">
            <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
        </button>
        {!! Form::close() !!}
    </div>
    <div class="col-md-6 text-right">
        <h4>
            <span class="d-inline-block mr-4 font-weight-bold">
                {{ trans('global.total') . ' : ' . $attendants->count() }}
            </span>
            <span class="d-inline-block ml-4 text-success font-weight-bold">
                ( {{ trans('global.revenue') . ' : ' . $revenue . ' EGP' }} )
            </span>
        </h4>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <i class="fas fa-list"></i> {{ trans('global.revenue_details') }}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-outline table-hover text-center zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark font-weight-bold">#</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.athlete_name') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('cruds.membership.title_singular') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.session_cost') }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.session_name') . ' ( ' . trans('global.time') . ' )' }}</th>
                        <th class="text-dark font-weight-bold">{{ trans('global.attended_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendants as $attendant)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td>{{ $attendant->member->name }}</td>
                            <td class="font-weight-bold">
                                {{ trans('cruds.pricelist.title_singular') . ' : ' . $attendant->membership->service_pricelist->name }} <br>
                                {{ trans('cruds.pricelist.fields.amount') . ' : ' . $attendant->membership->service_pricelist->amount }} <br>
                                {{ trans('cruds.pricelist.fields.session_count') . ' : ' . $attendant->membership->service_pricelist->session_count }}
                            </td>
                            <td>
                                @if (!is_null($attendant->membership->invoice))
                                    {{ round(
                                        $attendant->membership->invoice->net_amount /
                                        $attendant->membership->service_pricelist->session_count
                                    ) . ' EGP' }}
                                @else
                                    <span class="badge badge-danger text-light">
                                        {{ trans('global.no_invoice_available') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge px-3 py-2 shadow-sm" style="background:{{ $attendant->schedule->session->color }}">
                                    <span>
                                        {{ $attendant->schedule->session->name }}
                                        ( {{ date('g:i A', strtotime($attendant->schedule->timeslot->from)) }} ) </td>
                                    </span>
                                </span>
                            <td>
                                {{ $attendant->created_at->format('Y-m-d , g:i:s A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="float-right">
            {{-- {{ $attendants->appends(request()->all())->links() }} --}}
        </div>
    </div>
</div>
@endsection