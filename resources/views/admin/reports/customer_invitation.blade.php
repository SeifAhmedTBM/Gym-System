@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-8">
            <form action="{{ route('admin.reports.customer-invitation') }}" method="get">
                <label for="sport_id">Sport</label>
                <div class="input-group">
                    <select name="sport_id" id="sport_id" class="form-control">
                        <option value="{{ NULL }}">Select Sport</option>
                        @foreach (\App\Models\Sport::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ request('sport_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="trainer_id" id="trainer_id" class="form-control">
                        <option value="{{ NULL }}">Select Trainer</option>
                        @foreach (\App\Models\User::whereRelation('roles','title','Trainer')->pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ request('trainer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-md-2">
            <h4 class="text-center">{{ $leads->count() }}</h4>
            <h4 class="text-center">Leads</h4>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-check-circle"></i> Customer Invitation 
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('cruds.member.title_singular') }}</th>
                                    <th>Type</th>
                                    <th>{{ trans('global.sport') }}</th>
                                    <th>{{ trans('global.trainer') }}</th>
                                    <th>{{ trans('global.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ $lead->type == 'lead' ? route('admin.leads.show',$lead->id)  : route('admin.members.show',$lead->id)  }}" target="_blank">
                                                {{ $lead->name ?? '-' }} <br>
                                                {{ $lead->phone }}
                                            </a>
                                        </td>
                                        <td>{{ $lead->type ?? '-' }}</td>
                                        <td>{{ $lead->sport->name ?? '-' }}</td>
                                        <td>{{ $lead->trainer->name ?? '-' }}</td>
                                        <td>{{ $lead->created_at ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection