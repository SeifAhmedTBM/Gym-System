@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-2">
            <a class="btn btn-danger" href="{{ route('admin.members.index') }}">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back_to_list') }}
            </a>
        </div>

        <div class="col-md-2">
            <a class="btn btn-danger" href="{{ route('admin.members.show',$member->id)}}">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.profile_information') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.popMessage.store',$member->id) }}" method="post">
        @csrf
        <div class="form-group">
            <div class="card">
                <div class="card-header">
                    {{ trans('global.pop_messages') }}
                </div>
                <div class="card-body">
                    <textarea name="message" class="form-control" id="notes" rows="7" placeholder="Enter message .. "></textarea>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> {{ trans('global.confirm') }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
