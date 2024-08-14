@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <div class="card">
            <div class="card-header">
                Create Zoom Meeting
            </div>
            <div class="card-body">
                <form action="{{ route('admin.zoom.store') }}" method="post">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="agenda">Agenda</label>
                            <input type="text" name="agenda" placeholder="Agenda .. " class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="topic">Topic</label>
                            <input type="text" name="topic" placeholder="Topic .. " class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="branch_id">
                                Branch 
                                <small class="text-danger">( if empty will select all branches )</small>
                            </label>
                            <select name="branch_id[]" id="branch_id" class="form-control select2" multiple>
                                {{-- <option value="{{ NULL }}" selected>All Branches</option> --}}
                                @foreach ($branches as $branch_id => $branch_name)
                                    <option value="{{ $branch_id }}">{{ $branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="role_id">
                                Roles
                                <small class="text-danger">( if empty will select all roles )</small>
                            </label>
                            <select name="role_id[]" id="role_id" class="form-control select2" multiple>
                                {{-- <option value="{{ NULL }}" selected>All Roles</option> --}}
                                @foreach ($roles as $role_id => $role_title)
                                    <option value="{{ $role_id }}">{{ $role_title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="date">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="start_time">Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="{{ date('H:00',strtotime('+1 Hour')) }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="type">Password</label>
                            <input type="text" name="password" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="duration">Duration</label>
                            <input type="number" name="duration" class="form-control" value="60">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            {{-- <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="pre_schedule" name="pre_schedule" value="1">
                                <label class="custom-control-label" for="pre_schedule">Pre-shcedule</label>
                            </div> --}}
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="mute" name="mute" value="1">
                                <label class="custom-control-label" for="mute">Mute ( mute participants when they join the meeting )</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="host_video" name="host_video" value="1">
                                <label class="custom-control-label" for="host_video">Host Video ( start host video when they join the meeting )</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="video" name="video" value="1">
                                <label class="custom-control-label" for="video">Video ( start video when they join the meeting )</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="approval" name="approval" value="0">
                                <label class="custom-control-label" for="approval">Approval ( Automatically Approve )</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection