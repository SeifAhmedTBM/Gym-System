@forelse ($memberships as $membership)
    <tr>
        <td>{{ $loop->iteration + ($memberships->currentPage() - 1) * $memberships->perPage() }}</td>
        <td>
            <a href="{{ route('admin.members.show', $membership->member_id) }}" target="_blank">
                {{ $membership->member->name ?? '-' }}
            </a>
            <span class="d-block font-weight-bold">{{ $membership->member->member_code ?? '-' }}</span>
            <span class="d-block font-weight-bold">{{ $membership->member->phone ?? '-' }}</span>
        </td>
        <td>{{ $membership->member->branch->name ?? '-' }}</td>
        <td>
            <a href="{{ route('admin.memberships.show', $membership->id) }}" target="_blank">
                {{ $membership->service_pricelist->name ?? '-' }}
            </a>
        </td>
        <td>{{ $membership->member->sport->name ?? '-' }}</td>
        <td>{{ $membership->start_date ?? '-' }}</td>
        <td>{{ $membership->end_date ?? '-' }}</td>
        <td>
            <a href="{{ route('admin.invoices.show', $membership->invoice->id) }}">
                <span class="d-block">{{ trans('global.total') }}: {{ number_format($membership->invoice->net_amount) ?? 0 }}</span>
                <span class="d-block">{{ trans('invoices::invoice.paid') }}: {{ number_format($membership->invoice->payments_sum_amount) ?? 0 }}</span>
                <span class="d-block">{{ trans('global.rest') }}: {{ number_format($membership->invoice->rest) ?? 0 }}</span>
            </a>
        </td>
        <td>{!! $membership->last_attendance ?? '<span class="badge badge-danger">No attendance</span>' !!}</td>
        <td>{{ $membership->sales_by->name ?? '-' }}</td>
        <td>
            <button type="button" data-toggle="modal" data-target="#takeMemberAction"
                    onclick="takeMemberAction({{ $membership->member_id }})" class="btn btn-info btn-xs">
                <i class="fa fa-phone"></i>&nbsp; {{ trans('cruds.reminder.fields.action') }}
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center">{{ trans('global.no_data_available') }}</td>
    </tr>
@endforelse
