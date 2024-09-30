@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reports.daily-task-report') }}" method="get">
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="date">{{ trans('global.filter') }}</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="from"
                                value="{{ request('from') ?? date('Y-m-d') }}">
                            <input type="date" class="form-control" name="to"
                                value="{{ request('to') ?? date('Y-m-d') }}">
                            <select name="branch_id" id="branch_id" class="form-control">
                                <option value="{{ null }}" selected>All Branches</option>
                                @foreach (App\Models\Branch::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                            <select name="source_id" id="source_id" class="form-control">
                                <option value="{{ null }}" selected>Source</option>
                                @foreach (App\Models\Source::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ request('source_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <select name="sales_by_id" id="sales_by_id" class="form-control">
                                <option value="{{ null }}" selected>Sales By</option>
                                @foreach (App\Models\User::whereRelation('roles','title','Sales')->pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ request('sales_by_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <select name="type" id="type" class="form-control">
                                <option value="{{ null }}" selected>Type</option>
                                @foreach (App\Models\Lead::TYPE_SELECT as $key => $value)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-prepend">
                                <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="form-group">
        <div>
            <input type="text" id="customSearch" placeholder="Search Leads..."
                   class="form-control mb-3">
        </div>
        <table class="table table-bordered table-striped table-hover zero-configuration" id="SourcesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        {{ trans('cruds.lead.title_singular') }}
                    </th>
                    <th>
                        {{ trans('cruds.branch.title_singular') }}
                    </th>
                    <th>
                        {{ trans('global.type') }}
                    </th>
                    <th>
                        {{ trans('cruds.action.title_singular') }}
                    </th>
                    <th>
                        {{ trans('global.details') }}
                    </th>
                    <th>
                        {{ trans('global.due_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.lead.fields.sales_by') }}
                    </th>
                    <th>{{ trans('global.notes') }}</th>
                    <th>{{ trans('global.action_date') }}</th>
                    <th>{{ trans('global.action') }}</th>
                </tr>
            </thead>
            <tbody id="actionsTableBody">
            @include('admin.reports.partials.sources', ['reminder_sources' => $reminder_sources])
            </tbody>
        </table>
        <div id="paginationLinks">
            {{ $reminder_sources->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#SourcesTable').DataTable({
                destroy: true,
                searching: false,
                lengthChange: false,
                paging: false,
                info: true,
                dom: 't<"bottom"p>',
                select: false,
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });
        });
        document.getElementById('customSearch').addEventListener('input', function () {
            let searchTerm = this.value.trim();

            let currentUrl = new URL(window.location.href);

            let params = new URLSearchParams(currentUrl.search);

            if (searchTerm) {
                params.set('search', searchTerm);
                params.delete('page');
            } else {
                params.delete('search');
            }
            let url = `{{ route('admin.reports.daily-task-report') }}?${params.toString()}`;
            console.log(url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    const reminderTypes = @json(\App\Models\Reminder::TYPE); // Use PHP variables in JS
                    const reminderActions = @json(\App\Models\Reminder::ACTION);

                    let tbody = document.getElementById('actionsTableBody'); // Get table body element
                    tbody.innerHTML = ''; // Clear existing rows

                    let currentPage = data.reminder_sources.current_page; // Extract pagination info
                    let perPage = data.reminder_sources.per_page;

                    // Loop through reminders and append rows
                    data.reminder_sources.data.forEach((reminder, index) => {
                        let iteration = index + 1 + (currentPage - 1) * perPage; // Calculate row number

                        tbody.innerHTML += `
                <tr>
                    <td>${iteration}</td>
                    <td>
                        ${reminder.lead.type === 'member' ? `
                            <a href="{{ url('admin/members') }}/${reminder.lead_id}" target="_blank" class="text-decoration-none">
                                {{ \App\Models\Setting::first()->member_prefix }}${reminder.lead.member_code ?? '-'}
                                <span class="d-block">${reminder.lead.name}</span>
                                <span class="d-block">${reminder.lead.phone}</span>
                            </a>
                        ` : `
                            <a href="{{ url('admin/leads') }}/${reminder.lead_id}" target="_blank" class="text-decoration-none">
                                <span class="d-block">${reminder.lead.name}</span>
                                <span class="d-block">${reminder.lead.phone}</span>
                            </a>
                        `}
                        ${reminder.lead.type ?? '-'}
                    </td>
                    <td>${reminder.lead.branch?.name ?? '-'}</td>
                    <td>${reminder.type ? reminderTypes[reminder.type] || '' : ''}</td>
                    <td>${reminder.action ? reminderActions[reminder.action] || '' : ''}</td>
                    <td>
                        <span class="d-block">${reminder.membership?.service_pricelist?.name ?? '-'}</span>
                        ${reminder.type === 'due_payment' ? `
                            <span class="d-block">{{ trans('global.total') }}: ${reminder.membership?.invoice?.net_amount ?? 0}</span>
                            <span class="d-block">{{ trans('invoices::invoice.paid') }}: ${reminder.membership?.invoice?.payments_sum_amount ?? 0}</span>
                            <span class="d-block">{{ trans('global.rest') }}: ${reminder.membership?.invoice?.rest ?? 0}</span>
                        ` : ''}
                    </td>
                    <td>${reminder.due_date ?? ''}</td>
                    <td>${reminder.user?.name ?? '-'}</td>
                    <td>${reminder.notes ?? ''}</td>
                    <td>${reminder.created_at}</td>
                    <td>
                        @can('reminder_delete')
                        <form action="{{ route('admin.reminderHistory.destroy', '') }}/${reminder.id}" method="post"
                              onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                            @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">
                            <i class="fa fa-trash"></i>{{ trans('global.delete') }}
                        </button>
                    </form>
@endcan
                        </td>
                    </tr>`;
                    });

                    // Hide pagination if search term is present
                    let paginationLinks = document.getElementById('paginationLinks');
                    if (searchTerm) {
                        paginationLinks.style.display = 'none';
                    } else {
                        paginationLinks.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error fetching search results:', error));
        });

    </script>
@endsection
