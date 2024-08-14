<div class="mb-2">
    @can('export_trainers')
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.trainers-report.export', request()->all())]) !!}
            <button type="submit" class="btn btn-success">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </button>
        {!! Form::close() !!}
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fas fa-dumbbell"></i> {{ trans('global.trainers_report') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        <th class="text-dark">{{ trans('global.total_invoices') }}</th>
                        <th class="text-dark">{{ trans('global.this_month_collected') }}</th>
                        <th class="text-dark">{{ trans('global.previous_month_collected') }}</th>
                        <th class="text-dark">{{ trans('global.this_month_commission') }}</th>
                        <th class="text-dark">{{ trans('global.previous_month_commissions') }}</th>
                        <th class="text-dark">{{ trans('global.total_commissions') }}</th>
                        <th class="text-dark">{{ trans('global.view') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trainers as $trainer_id => $trainer_name)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
                            <td class="font-weight-bold text-dark">{{ $trainer_name }}</td>
                            <td class="font-weight-bold text-dark">{{ number_format($commission->where('trainer_id', $trainer_id)->first()['totalInvoices']) . ' EGP' }}</td>
                            @php
                                $pre_com = $pre_commission->where('trainer_id', $trainer_id)->first();
                            @endphp
                            <td class="font-weight-bold">
                                {{ round($commission->where('trainer_id', $trainer_id)->first()['total']) }} EGP
                            </td>
                            <td class="font-weight-bold">
                                @if(isset($pre_com) && $pre_com != NULL)
                                    {{ round($pre_com['pre_total']) }} EGP 
                                @else 
                                    0 EGP 
                                @endif
                            </td>
                            <td class="font-weight-bold">
                                {{ $commission->where('trainer_id', $trainer_id)->first()['commission'] != 0 ? round(intval($commission->where('trainer_id', $trainer_id)->first()['commission'])) . ' EGP' : '0 EGP ' }}
                            </td>
                            <td class="font-weight-bold">
                                @if(isset($pre_com) && $pre_com != NULL) 
                                    {{ round($pre_com['previous_months_commissions']) . ' EGP' }} 
                                @else 
                                    0 EGP 
                                @endif</td>
                            <td class="font-weight-bold">
                                {{ round(intval($commission->where('trainer_id', $trainer_id)->first()['commission'])) + (isset($pre_com['previous_months_commissions']) ? round($pre_com['previous_months_commissions']) : 0) }} EGP
                            </td>
                            <td>
                                <a href="{{ route('admin.reports.trainers-report.show', $trainer_id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> {{ trans('global.show') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{-- {{ $trainers->links() }} --}}
    </div>
</div>