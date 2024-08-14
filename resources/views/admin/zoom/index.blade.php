@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <a href="{{ route('admin.zoom.create') }}" class="btn btn-primary btn-sm">Create New Meeting</a>
    </div>

    <div class="card">
        <div class="card-header">
            Zoom Meetings
        </div>
        <div class="card-body">
            <div class="form-group">
                <table class="table table-striped table-hover table-bordered zero-configuration">
                    <thead>
                        <tr>
                            <th>Created At</th>
                            <th>Topic</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($meetings as $meet)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($meet['created_at'])->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $meet['topic'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($meet['start_time'])->format('Y-m-d') }}</td>
                                <td>{{ \Carbon\Carbon::parse($meet['start_time'])->format('H:i') }}</td>
                                <td>{{ $meet['duration'].' Min' }}</td>
                                <td>
                                    @php
                                        $date = \Carbon\Carbon::parse($meet['start_time'])->format('Y-m-d');
                                        $start_time = \Carbon\Carbon::parse($meet['start_time'])->format('H:i');
                                        $end_time = \Carbon\Carbon::parse($meet['start_time'])->addMinutes($meet['duration'])->format('H:i');  
                                        $now = \Carbon\Carbon::now()->addMinutes(-30)->format('H:i');
                                    @endphp
                                    <div class="btn-group">
                                        @if ($date == date('Y-m-d'))
                                            @if ($start_time >= $now && $end_time >= $now)
                                                <a href="{{ $meet['join_url'] }}" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fa fa-link"></i> Join
                                                </a>
                                            @else   
                                                <span class="badge badge-danger">Ended</span>
                                            @endif
                                        @elseif ($date > date('Y-m-d'))
                                            <span class="badge badge-warning">Upcomming</span>    
                                        @else
                                            <span class="badge badge-danger">Ended</span>
                                        @endif

                                        <a href="{{ route('admin.zoom.show',$meet['id']) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        
                                        <form action="{{ route('admin.zoom.destroy', $meet['id']) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i> &nbsp;{{ trans('global.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection