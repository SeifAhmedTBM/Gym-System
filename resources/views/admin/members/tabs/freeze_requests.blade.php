<div class="tab-pane fade" id="freezesRequests" role="tabpanel" aria-labelledby="freezesRequests-tab">
    <div class="row">
        <div class="col-md-12">
            @if (isset($last_membership))
                <div id="{{ $last_membership->memberPrefix() . $last_membership->id }}"
                    class="collapse {{ $last_membership->first()->id ? 'show' : '' }}" aria-labelledby="headingTwo"
                    data-parent="#freezesRequests">
                    <div class="card-body">
                        <table class="table table-bordered table-striped zero-configuration">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>
                                        {{ trans('cruds.membership.title_singular') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.freezeRequest.fields.freeze') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.freezeRequest.fields.start_date') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.freezeRequest.fields.end_date') }}
                                    </th>
                                    <th>
                                        {{ trans('global.consumed') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.freezeRequest.fields.status') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.bonu.fields.created_by') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.freezeRequest.fields.created_at') }}
                                    </th>
                                    <th>
                                        {{ trans('global.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($member->freezeRequests as $freezeRequest)
                                    @php

                                        $now = Carbon\Carbon::now();
                                        $start = Carbon\Carbon::parse($freezeRequest->start_date);
                                        $end = Carbon\Carbon::parse($freezeRequest->end_date);
                                        $diff = $now->diffInDays($start);

                                        if ($diff > $freezeRequest->freeze) {
                                            $diff = $end->diffInDays($start);
                                        }

                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.memberships.show', $freezeRequest->membership_id) }}"
                                                target="_blank">
                                                {{ $freezeRequest->membership->service_pricelist->name ?? '-' }}
                                            </a>
                                        </td>
                                        <td>{{ $freezeRequest->freeze }}</td>
                                        <td>
                                            {{ $freezeRequest->start_date }}
                                        </td>
                                        <td>
                                            {{ $freezeRequest->end_date }}
                                        </td>
                                        <td class="text-success">
                                            {{ $freezeRequest->status == 'confirmed' ? $diff . ' ' . trans('global.day_or_days') : '-' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge px-3 py-2 badge-{{ App\Models\FreezeRequest::STATUS_COLOR[$freezeRequest->status] }}">
                                                {{ ucfirst($freezeRequest->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $freezeRequest->created_by->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $freezeRequest->created_at->toFormattedDateString() . ' , ' . $freezeRequest->created_at->format('g:i A') }}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-primary dropdown-toggle" href="#" role="button"
                                                    id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                                    {{ trans('global.action') }}
                                                </a>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.freeze-requests.edit', $freezeRequest->id) }}">
                                                        <i class="fa fa-edit"></i> &nbsp;
                                                        {{ trans('global.edit') }}
                                                    </a>

                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.freeze-requests.show', $freezeRequest->id) }}">
                                                        <i class="fa fa-eye"></i> &nbsp;
                                                        {{ trans('global.view') }}
                                                    </a>

                                                    <a href="javascript:void(0)" onclick="deleteFreezeRequest(this)"
                                                        data-toggle="modal" data-target="#deleteFreezeRequestModal"
                                                        data-route="{{ route('admin.freeze-requests.destroy', $freezeRequest->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-trash"></i> &nbsp;
                                                        {{ trans('global.delete') }}
                                                    </a>

                                                    @can('approve_reject_freeze')
                                                        @if ($freezeRequest->status == 'pending')
                                                            <form
                                                                action="{{ route('admin.freeze-requests.confirm', $freezeRequest->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                                style="display: inline-block;">
                                                                <input type="hidden" name="_method" value="PUT">
                                                                <input type="hidden" name="_token"
                                                                    value="{{ csrf_token() }}">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fa fa-check"></i> &nbsp;
                                                                    {{ trans('global.confirm') }}
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="{{ route('admin.freeze-requests.reject', $freezeRequest->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                                style="display: inline-block;">
                                                                <input type="hidden" name="_method" value="PUT">
                                                                <input type="hidden" name="_token"
                                                                    value="{{ csrf_token() }}">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fa fa-times"></i>
                                                                    &nbsp;{{ trans('global.reject') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <td colspan="10" class="text-center">
                                        {{ trans('global.no_data_available') }}</td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteFreezeRequestModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'DELETE', 'id' => 'deleteFreezeRequestForm']) !!}
            <div class="modal-body">
                <h4 class="text-danger font-weight-bold text-center">
                    {{ trans('global.delete_alert') }}
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    {{ trans('global.close') }}
                </button>
                <button type="submit" class="btn btn-success">{{ trans('global.delete') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    function deleteFreezeRequest(button) 
    {
        let formURL = $(button).data('route');
        $("#deleteFreezeRequestForm").attr('action', formURL);
    }
</script>
