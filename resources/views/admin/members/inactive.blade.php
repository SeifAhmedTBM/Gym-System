@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-4">
            <form action="{{ route('admin.members.inactive') }}" method="get">
                <label for="date">{{ trans('cruds.branch.title_singular') }}</label>
                <div class="input-group">
                    <select name="branch_id" id="branch_id"
                            class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>All Branches</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="sport_id" id="sport_id" class="form-control">
                        <option value="{{ NULL }}">Select Sport</option>
                        @foreach (\App\Models\Sport::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ request('sport_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-2 offset-md-4">
            {{-- @can('export_inactive_members')
                <label for="">{{ trans('global.export_excel') }}</label>
                <a href="{{ route('admin.inactiveMembers.export',request()->all()) }}" class="btn btn-info"><i class="fa fa-download"></i> {{ trans('global.export_excel') }}</a>
            @endcan --}}
        </div>

        <div class="col-md-2">
            <h4 class="text-center">{{ $membersCount }}</h4>
            <h4 class="text-center">{{ trans('global.inactive_members') }}</h4>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-times"></i> {{ trans('global.inactive_members') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div>
                            <input type="text" id="customSearch" placeholder="Search members..."
                                   class="form-control mb-3">
                        </div>
                        <table class="table table-bordered table-striped table-hover zero-configuration" id="inactiveMemberTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('cruds.member.title_singular') }}</th>
                                <th>{{ trans('cruds.branch.title_singular') }}</th>
                                <th>Sport</th>
                                <th>{{ trans('cruds.membership.title') }}</th>
                            </tr>
                            </thead>
                            <tbody id="inactiveTableBody">
                            @include('admin.members.partials.inactive', ['members' => $members])
                            </tbody>
                        </table>
                        <div id="paginationLinks">
                            {{ $members->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#inactiveMemberTable').DataTable({
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

            let url = searchTerm
                ? `{{ route('admin.members.inactive.search') }}?search=${encodeURIComponent(searchTerm)}`
                : window.location.href;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    let tbody = document.getElementById('inactiveTableBody');
                    tbody.innerHTML = '';

                    let currentPage = data.members.current_page;
                    let perPage = data.members.per_page;

                    data.members.data.forEach((member, index) => {
                        let iteration = index + 1 + (currentPage - 1) * perPage;

                        tbody.innerHTML += `
                <tr>
                    <td>${iteration}</td>
                    <td>
                        <a href="{{ url('admin/members') }}/${member.id}" target="_blank" class="text-decoration-none">
                            ${member.name ?? '-'} <br>
                            ${member.phone ?? '-'}
                        </a>
                    </td>
                    <td>${member.branch?.name ?? '-'}</td>
                    <td>${member.sport?.name ?? '-'}</td>
                    <td>
                        <a href="{{ url('admin/memberships') }}?member_id=${member.id}" target="_blank">
                            ${member.memberships_count ?? 0}
                        </a>
                    </td>
                </tr>`;
                    });

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