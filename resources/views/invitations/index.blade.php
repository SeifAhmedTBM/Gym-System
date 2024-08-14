@extends('layouts.admin')
@section('content')
    <div class="row my-2">
        <div class="col-md-10">
            @include('admin_includes.filters', [
            'columns' => [
                'name'          => ['label' => 'Name', 'type' => 'text', 'related_to' => 'member'],
                'phone'         => ['label' => 'Phone', 'type' => 'number','related_to' => 'member'],
                'member_code'   => ['label' => 'Member Code', 'type' => 'text','related_to' => 'member'],
                'created_at'    => ['label' => trans('global.created_at'), 'type' => 'date', 'from_and_to' => true]
            ],
                'route' => 'admin.invitations.index'
            ])
            @can('export_invitations')
                <a href="{{ route('admin.invitations.export',request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
        </div>
        <div class="col-md-2">
            <h2 class="text-center">{{ $invitations->count() }}</h2>
            <h2 class="text-center">{{ trans('global.invitations') }}</h2>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-plus-circle"></i> {{ trans('global.invitations_report') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-sm-responsive">
                        <table class="table table-striped table-hover table-bordered zero-configuration">
                            <thead>
                                <th>#</th>
                                <th>
                                    {{ trans('cruds.lead.title_singular') }}
                                </th>
                                <th>
                                    {{ trans('cruds.member.title_singular') }}
                                </th>
                                <th>
                                    {{ trans('cruds.service.title_singular') }}
                                </th>
                                <th>
                                    {{ trans('global.created_at') }}
                                </th>
                            </thead>
                            <tbody>
                                @forelse ($invitations as $invitation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a  target="_blank" href="{{ route('admin.leads.show',$invitation->lead_id) }}">
                                                {{ $invitation->lead->name ?? '-' }}
                                            </a>
                                            <span class="d-block font-weight-bold">
                                                {{ $invitation->lead->phone ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block font-weight-bold">
                                                {{ \App\Models\Setting::first()->member_prefix.$invitation->member->member_code ?? '-' }}
                                            </span>
                                            <a href="{{ route('admin.members.show',$invitation->member_id) }}" target="_blank">
                                                {{ $invitation->member->name ?? '-' }}
                                            </a>
                                            <span class="d-block font-weight-bold">
                                                {{ $invitation->member->phone ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $invitation->membership->service_pricelist->name ?? '-' }}</td>
                                        <td>{{ $invitation->created_at ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            {{-- {{ $invitations->appends(request()->all())->links() }} --}}
        </div>
    </div>


@endsection
@section('scripts')

@endsection