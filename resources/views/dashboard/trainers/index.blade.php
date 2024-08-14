@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-user"></i> Monthly PT MemberShips</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered zero-configuration">
                            <thead>
                                <th>#</th>
                                <th>
                                    {{ trans('cruds.membership.fields.member') }}
                                </th>
                                {{-- <th>{{ trans('cruds.branch.title_singular') }}</th> --}}
                                <th>{{ trans('cruds.membership.title') }}</th>
                                <th>{{ trans('cruds.service.fields.service_type') }}</th>
                                <th>{{ trans('global.date') }}</th>
                                <th>{{ trans('global.trainer') }}</th>
                                <th>Remaining Sessions</th>
                                <th>Amount</th>
                                <th>{{ trans('global.created_at') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($memberships as $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.show', $value->member->id) }}">
                                                {{ $value->member->name }} <br>
                                                {{ $value->member->phone }} <br>
                                                Memberships : {{ $value->member->memberships_count }}
                                            </a>
                                        </td>
                                        {{-- <td>{{ $value->branch->name ?? '-' }}</td> --}}
                                        <td>
                                            {{ $value->service_pricelist->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $value->service_pricelist->service->service_type->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ 'Start Date : ' . $value->start_date ?? '-' }} <br>
                                            {{ 'End Date : ' . $value->end_date ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $value->trainer->name ?? '-' }}
                                        </td>
                                        <td>

                                            {{ $value->trainer_attendances_count ?? 0 }} \
                                            {{ $value->service_pricelist->session_count ?? 0 }}
                                        </td>
                                        <td>{{ number_format($value->invoice->net_amount ?? 0) }}</td>
                                        <td>
                                            {{ $value->created_at->toFormattedDateString() ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
