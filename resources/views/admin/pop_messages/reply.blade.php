@extends('layouts.admin')
@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-hand-paper"></i> {{ trans('global.pop_messages') }} | <span class="font-weight-bold">{{ $pop_message->member->name ?? '-'}}</span>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold">{{ trans('cruds.member.title_singular') }}</td>
                        <td>{{ $pop_message->member->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold" width="200">{{ trans('global.subject') }}</td>
                        <td>{{ $pop_message->message }}</td>
                    </tr>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold">{{ trans('cruds.bonu.fields.created_by') }}</td>
                        <td>{{ $pop_message->created_by->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold">{{ trans('global.created_at') }}</td>
                        <td>{{ $pop_message->created_at->toFormattedDateString() . ' , ' . date('g:i A', strtotime($pop_message->created_at)) }}</td>
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
            @forelse ($pop_message->pop_messages_replies as $reply)
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
                                <i class="fa fa-user-circle"></i> {{ $reply->created_by->name }}
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
        {!! Form::open(['method' => 'POST', 'url' => route('admin.popMessageReply.store',$pop_message->id)]) !!}
        <div class="card-body">
            <div class="form-group">
                <label for="reply" class="required">{{ trans('global.reply') }}</label>
                <input type="text" name="reply" id="reply" class="form-control">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-check-circle"></i> {{ trans('global.submit') }}
            </button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
