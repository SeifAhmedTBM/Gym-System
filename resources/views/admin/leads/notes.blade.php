@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-md-2">
            <a class="btn btn-danger" href="{{ $lead->type == 'member' ? route('admin.members.index') : route('admin.leads.index')}}">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back_to_list') }}
            </a>
        </div>

        <div class="col-md-2">
            <a class="btn btn-danger" href="{{ $lead->type == 'member' ? route('admin.members.show',$lead->id) : route('admin.leads.show',$lead->id)}}">
                <i class="fa fa-arrow-circle-left"></i> {{ trans('global.profile_information') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.note.store',$lead->id) }}" method="post">
        @csrf
        <div class="form-group">
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.lead.fields.notes') }}
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" id="notes" rows="7" placeholder="Enter notes .. "></textarea>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> {{ trans('global.confirm') }}</button>
                </div>
            </div>
        </div>
    </form>

    @foreach ($lead->Notes as $note)
        <div class="form-group">
            <div class="card">
                <div class="card-body">
                    <h4>{{ $note->notes }}</h4>
                    <h6 class="text-danger text-right">{{ $note->created_by->name ?? '-' }}</h6>
                    <h6 class="text-info text-right">{{ $note->created_at }}</h6>
                </div>
            </div>
        </div>
    @endforeach

    @if (!is_null($lead->notes))
        <div class="form-group">
            <div class="card">
                <div class="card-body">
                    <h4>{{ $lead->notes }}</h4>
                    <h6 class="text-danger text-right">{{ $lead->created_by->name ?? '-' }}</h6>
                    <h6 class="text-info text-right">{{ $lead->created_at }}</h6>
                </div>
            </div>
        </div>
    @endif
@endsection
