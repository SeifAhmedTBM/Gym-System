@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.membership.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.memberships.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            {{-- @if ($membership->status == 'current' || $membership->status == 'expiring') --}}
                <form action="{{ route('admin.take_manual_attend',$membership->id) }}" method="post">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="date">{{ trans('global.date') }}</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-4">
                                <label for="time">{{ trans('global.time') }}</label>
                                <input type="time" class="form-control" name="time" value="{{ date('H:i') }}">
                            </div>

                            <div class="col-md-2">
                                <label>{{ trans('global.take_attendance') }}</label>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-fingerprint"></i> {{ trans('global.take_attendance') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            {{-- @endif --}}

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.id') }}
                        </th>
                        <td>
                            {{ $membership->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.start_date') }}
                        </th>
                        <td>
                            {{ $membership->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.end_date') }}
                        </th>
                        <td>
                            {{ $membership->end_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.member') }}
                        </th>
                        <td>
                            {{ $membership->member->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.trainer') }}
                        </th>
                        <td>
                            {!! $membership->trainer->name ?? '<span class="badge badge-danger">No Trainer</span>' !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.service') }}
                        </th>
                        <td>
                            {{ $membership->service_pricelist->name ?? '' }} - {{ $membership->service_pricelist->service->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.membership.fields.sales_by') }}
                        </th>
                        <td>
                            {{ $membership->sales_by->name ?? '' }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <th>
                            {{ trans('global.attendances') }}
                        </th>
                        <td>
                            {{ $membership->attendances_count ?? '' }}
                        </td>
                    </tr> --}}

                    @foreach ($membership->service_pricelist->serviceOptionsPricelist as $service_option)
                        <tr class="font-weight-bold">
                            <th>
                                {{ $service_option->service_option->name ?? '' }}
                            </th>
                            <td>
                                <span class="inbody-count">
                                    {{ $membership->membership_service_options()->where('service_option_pricelist_id',$service_option->service_option_id)->count() }}
                                </span>
                                / <span class="service-count">{{ $service_option->count }}</span>

                                <button class="btn btn-success btn-sm" type="button" {{ $membership->membership_service_options()->where('service_option_pricelist_id',$service_option->service_option_id)->count() == $service_option->count ? 'disabled' : '' }} data-membership-id="{{ $membership->id }}" data-service={{ $service_option->service_option_id }} id="counterBtn" onclick="submitService(this)"><i class="fa fa-plus"></i></button>
                            </td>    
                        </tr>    
                    @endforeach
                </tbody>
            </table>

            <hr>
            <h4><i class="fas fa-fingerprint"></i> {{ trans('global.attendance_data') }}</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('cruds.member.title_singular') }}</th>
                            <th>{{ trans('cruds.membershipAttendance.fields.sign_in') }}</th>
                            <th>{{ trans('global.date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membership->attendances as $attend)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('admin.members.show',$attend->membership->member_id) }}">
                                        {{ $attend->membership->member->name ?? '-' }}
                                    </a>
                                    <span class="d-block font-weight-bold">
                                        {{ \App\Models\Setting::first()->member_prefix.$attend->membership->member->member_code ?? '-' }}
                                    </span>
                                    <span class="d-block font-weight-bold">
                                        {{ $attend->membership->member->phone ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $attend->sign_in }}</td>
                                <td>{{ $attend->created_at }}</td>
                                <td>
                                    @can('membership_attendance_delete')
                                        <form
                                            action="{{ route('admin.membership-attendances.destroy', $attend->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure?');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i> &nbsp; Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.memberships.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
@section('scripts')
    <script>
        function submitService(button)
        {
            let membership_id = $(button).data('membership-id');
            let service_option_pricelist_id = $(button).data('service');
            var url = "{{ route('admin.addMembershipServiceOption', [':membership_id', ':service_option_pricelist_id']) }}";
            url = url.replace(':membership_id', membership_id);
            url = url.replace(':service_option_pricelist_id', service_option_pricelist_id);
            $.ajax({
                method:'GET',
                url: url,
                beforeSend: function() {
                    $("#counterBtn").attr('disabled', 'disabled');
                },
                success: function(response) {
                    $(".inbody-count").html(response.new_counter);
                    if(parseInt($(".service-count").html()) == parseInt($(".inbody-count").html())) {
                        $("#counterBtn").attr('disabled', 'disabled');
                    }
                },
                complete: function() {
                    if(parseInt($(".service-count").html()) == parseInt($(".inbody-count").html())) {
                        $("#counterBtn").attr('disabled', 'disabled');
                    }else {
                        $("#counterBtn").removeAttr('disabled');
                    }
                }
            });
        }
    </script>
@endsection