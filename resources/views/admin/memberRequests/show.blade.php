@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.member-requests.index') }}" class="btn btn-danger mb-2">
    <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
</a>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="fas fa-hand-paper"></i> {{ trans('global.member_request') }} | <span class="font-weight-bold">{{ $member_request->member->name }}</span>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="text-dark bg-light font-weight-bold">{{ trans('cruds.member.title_singular') }}</td>
                    <td>{{ $member_request->member->name }}</td>
                </tr>
                <tr>
                    <td class="text-dark bg-light font-weight-bold" width="200">{{ trans('global.subject') }}</td>
                    <td>{{ $member_request->subject }}</td>
                </tr>
                <tr>
                    <td class="text-dark bg-light font-weight-bold">{{ trans('global.comment') }}</td>
                    <td>{{ $member_request->comment }}</td>
                </tr>
                <tr>
                    <td class="text-dark bg-light font-weight-bold">{{ trans('cruds.status.title_singular') }}</td>
                    <td>
                        <span class="badge px-2 py-2 badge-{{ App\Models\MemberRequest::STATUS[$member_request->status] }}">
                            {{ Str::ucfirst($member_request->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="text-dark bg-light font-weight-bold">{{ trans('cruds.bonu.fields.created_by') }}</td>
                    <td>{{ $member_request->user->name }}</td>
                </tr>
                <tr>
                    <td class="text-dark bg-light font-weight-bold">{{ trans('global.created_at') }}</td>
                    <td>{{ $member_request->created_at->toFormattedDateString() . ' , ' . date('g:i A', strtotime($member_request->created_at)) }}</td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="fa fa-comment"></i> {{ trans('global.replies') }}
    </div>
    <div class="card-body">
        @forelse ($member_request->member_request_replies as $reply)
        <div class="bg-light rounded px-3 py-2 shadow-sm mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-1">
                            <img src="{{ asset('images/user.png') }}" class="img-fluid" alt="User Image">
                        </div>
                        <div class="col-md-11 mt-2 font-weight-bold text-dark">
                            <i class="fa fa-comment"></i> {{ $reply->reply }} <br>
                            <span class="text-danger"><i class="fa fa-calendar"></i> {{ $reply->created_at->toFormattedDateString() }} , {{ $reply->created_at->format('g:i A') }}</span> <br>
                            <i class="fa fa-user-circle"></i> {{ $reply->user->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
            <h3 class="text-center my-3 font-weight-bold text-danger">
                {{ trans('global.no_replies_available') }}
            </h3>
        @endforelse
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="fa fa-edit"></i> {{ trans('global.leave_a_reply') }}
    </div>
    {!! Form::open(['method' => 'POST', 'url' => route('admin.reply.store')]) !!}
    <div class="card-body">
        <div class="form-group">
            <label for="reply" class="required">{{ trans('global.reply') }}</label>
            <input type="text" name="reply" id="reply" class="form-control">
        </div>
        <input type="hidden" name="member_request_id" value="{{ $member_request->id }}">
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success">
            <i class="fa fa-check-circle"></i> {{ trans('global.submit') }}
        </button>
    </div>
    {!! Form::close() !!}
</div>
@endsection