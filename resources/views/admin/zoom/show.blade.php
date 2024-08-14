@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <div class="card">
            <div class="card-header">
                Zoom Meeting | {{ $meeting['topic'] }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                Topic
                            </th>
                            <td>
                                {{ $meeting['topic'] }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Agenda
                            </th>
                            <td>
                                {{ $meeting['agenda'] }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Date 
                            </th>
                            <td>
                                {{ \Carbon\Carbon::parse($meeting['start_time'])->format('Y-m-d') }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Time 
                            </th>
                            <td>
                                {{ \Carbon\Carbon::parse($meeting['start_time'])->format('H:i:s') }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Status 
                            </th>
                            <td>
                                {{ $meeting['status'] }}
                                @if ($meeting['status'] == 'started')
                                    <form action="{{ route('admin.zoom.end', $meeting['id']) }}" method="POST"
                                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-times"></i> &nbsp; End
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                URL 
                            </th>
                            <td>
                                <a href="{{ $meeting['join_url'] }}" target="_blank" class="btn btn-sm btn-info">
                                   <i class="fa fa-link"></i> Join
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Created At 
                            </th>
                            <td>
                                {{ \Carbon\Carbon::parse($meeting['created_at'])->format('Y-m-d H:i:s') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection