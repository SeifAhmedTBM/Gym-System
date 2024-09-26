@foreach ($reminder_sources as $reminder)
    <tr>
        <td>{{ $loop->iteration + ($reminder_sources->currentPage() - 1) * $reminder_sources->perPage() }}</td>
        <td>
            @if ($reminder->lead->type == 'member')
                <a href="{{ route('admin.members.show', $reminder->lead_id) }}" target="_blank"
                   class="text-decoration-none">
                    {{ \App\Models\Setting::first()->member_prefix . $reminder->lead->member_code ?? '-' }}
                    <span class="d-block">
                                        {{ $reminder->lead->name }}
                                    </span>
                    <span class="d-block">
                                        {{ $reminder->lead->phone }}
                                    </span>
                </a>
            @else
                <a href="{{ route('admin.leads.show', $reminder->lead_id) }}" target="_blank"
                   class="text-decoration-none">
                                    <span class="d-block">
                                        {{ $reminder->lead->name }}
                                    </span>
                    <span class="d-block">
                                        {{ $reminder->lead->phone }}
                                    </span>
                </a>
            @endif
            {{ $reminder->lead->type ?? '-' }}
        </td>
        <td>{{ $reminder->lead->branch->name ?? '-' }}</td>
        <td>
            {{ \App\Models\Reminder::TYPE[$reminder->type] ?? '' }}
        </td>
        <td>
            {{ \App\Models\Reminder::ACTION[$reminder->action] ?? '' }}
        </td>
        <td>
                            <span class="d-block">
                                {{ $reminder->membership->service_pricelist->name ?? '-' }}
                            </span>
            @if ($reminder->type == 'due_payment')
                <span class="d-block">
                                    {{ trans('global.total') }} :
                                    {{ $reminder->membership->invoice->net_amount ?? 0 }}
                                </span>
                <span class="d-block">
                                    Paid :
                                    {{ $reminder->membership->invoice->payments_sum_amount ?? 0 }}
                                </span>
                <span class="d-block">
                                    {{ trans('global.rest') }} :
                                    {{ $reminder->membership->invoice->rest ?? 0 }}
                                </span>
            @endif
        </td>
        <td>{{ $reminder->due_date ?? '' }}</td>
        <td>{{ $reminder->user->name ?? '-' }}</td>
        <td>{{ $reminder->notes }}</td>
        <td>{{ $reminder->created_at }}</td>
        <td>
            @can('reminder_delete')
                <form action="{{ route('admin.reminderHistory.destroy', $reminder->id) }}" method="post"
                      onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit">
                        <i class="fa fa-trash"></i>
                        {{ trans('global.delete') }}
                    </button>
                </form>
            @endcan
        </td>
    </tr>
@endforeach