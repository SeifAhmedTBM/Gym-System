@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ URL::current() }}" method="get">
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="date">{{ trans('global.filter') }}</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="from"
                                   value="{{ request('from') ?? date('Y-m-01') }}">
                            <input type="date" class="form-control" name="to"
                                   value="{{ request('to') ?? date('Y-m-t') }}">
                            <select name="branch_id" id="branch_id" class="form-control">
                                <option value="{{ null }}" selected>All Branches</option>
                                @foreach (App\Models\Branch::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                            <select name="reminder_action" id="reminder_action" class="form-control">
                                <option value="{{ null }}" selected>Action</option>
                                @foreach (App\Models\Reminder::ACTION as $reminder_id => $reminder_action)
                                    <option value="{{ $reminder_id }}"
                                            {{ request('reminder_action') == $reminder_id ? 'selected' : '' }}>{{ $reminder_action }}
                                    </option>
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
                                <a href="{{ route('admin.reports.actions-report.export',request()->all()) }}"
                                   class="btn btn-info">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="form-group">
        <div class="table-responsive">
            <div>
                <input type="text" id="customSearch" placeholder="Search leads..." class="form-control mb-3">
            </div>
            <table class="table table-bordered table-striped table-hover zero-configuration" id="actionsTable">
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
                @include('admin.reports.partials.actions_table', ['reminder_actions' => $reminder_actions])
                </tbody>
            </table>
            <div id="paginationLinks">
                {{ $reminder_actions->links() }}
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#actionsTable').DataTable({
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

            // Construct the URL based on whether there is a search term or not
            let url = searchTerm
                ? `{{ route('admin.reports.action.search') }}?search=${searchTerm}`
                : `${window.location.href}`;

            // Fetch the data from the server
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    const reminderTypes = @json(\App\Models\Reminder::TYPE);
                    const reminderActions = @json(\App\Models\Reminder::ACTION);
                    let tbody = document.getElementById('actionsTableBody');
                    tbody.innerHTML = '';

                    let currentPage = data.reminder_actions.current_page;
                    let perPage = data.reminder_actions.per_page;

                    data.reminder_actions.data.forEach((reminder, index) => {
                        let iteration = index + 1 + (currentPage - 1) * perPage;

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
                            <i class="fa fa-trash"></i>
{{ trans('global.delete') }}
                        </button>
                    </form>
@endcan
                    </td>

                                            </tr>`;
                    });

                    // Handle the visibility of pagination links depending on whether there's a search term
                    let paginationLinks = document.getElementById('paginationLinks');
                    if (searchTerm) {
                        paginationLinks.style.display = 'none';
                    } else {
                        paginationLinks.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error fetching search results:', error));
        });



        function takeMemberAction(id) {
            var id = id;
            var url = "{{ route('admin.reminders.takeMemberAction', ':id') }}";
            url = url.replace(':id', id);
            $(".modalForm2").attr('action', url);
        }

        function formSubmit() {
            $('modalForm2').submit();
        }
    </script>
@endsection
