@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.member-requests.index') }}" class="btn btn-danger mb-2">
    <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
</a>
<div class="card shadow-sm">
    <div class="card-header">
        <i class="fas fa-hand-paper"></i> {{ trans('global.edit') }} {{ trans('global.member_request') }} | <span class="font-weight-bold">{{ $member_request->member->name ?? '-'}}</span>
    </div>
    <form action="{{ route('admin.member-requests.update',$member_request->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold" width="200">{{ trans('global.subject') }}</td>
                        <td>
                            <input type="text" class="form-control" name="subject" value="{{ $member_request->subject }}">    
                        </td>
                    </tr>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold">{{ trans('global.comment') }}</td>
                        <td>
                            <input type="text" class="form-control" name="comment" value="{{ $member_request->comment }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-dark bg-light font-weight-bold">{{ trans('global.created_at') }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="date" value="{{ $member_request->created_at->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="time" class="form-control" name="time" value="{{ $member_request->created_at->format('H:i') }}">        
                                </div>
                            </div>
                            
                            
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.save') }}</button>
        </div>
    </form>
</div>

@endsection