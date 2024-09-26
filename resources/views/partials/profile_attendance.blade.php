
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                {{ trans('global.attendance_data') }} | {{ Auth()->user()->employee && Auth()->user()->employee->branch_id ? Auth()->user()->employee->branch->name : '' }}
            </div>
            <div class="card-body">
                @if (Session::has('user_invalid'))
                    <div class="alert alert-danger font-weight-bold text-center">
                       <i class="fa fa-user-times"></i> {{ session('user_invalid') }}
                    </div>
                @endif
                @if (\App\Models\Setting::first()->has_lockers)
                    <div class="row form-group">
                        <div class="col-12">
                            <label for="">{{ trans('global.take_attendance') }}</label>
                            <input type="hidden" name="membership_id" value="">
                            <input type="hidden" name="member_id" value="">
                            <input type="hidden" name="branch_id" value="{{ Auth()->user()->employee && Auth()->user()->employee->branch_id ? Auth()->user()->employee->branch_id : NULL }}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="member_branch_id" id="member_branch_id" class="form-control">
                                        @foreach (\App\Models\Branch::pluck('member_prefix','id') as $id => $entry)
                                            <option value="{{ $id }}" {{ Auth()->user()->employee &&  Auth()->user()->employee->branch_id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="text" class="form-control" name="card_number" value="" id="member_code" autofocus ondragenter="getMember()" onfocus="this.setSelectionRange(this.value.length,this.value.length);">
                            </div>
                        </div>
                    </div>
                    <br>
                    
                @else
                    <form action="{{ route('admin.take.attend') }}" method="post" id="memberAttendanceForm">
                        @csrf
                        @method('POST')
                        <div class="row form-group">
                            <div class="col-12">
                                <label for="">{{ trans('global.take_attendance') }}</label>
                                <input type="hidden" name="membership_id" value="">
                                <input type="hidden" name="member_id" value="">
                                <input type="hidden" name="branch_id" value="{{ Auth()->user()->employee && Auth()->user()->employee->branch_id ? Auth()->user()->employee->branch_id : NULL }}">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <select name="member_branch_id" id="member_branch_id" class="form-control">
                                            @foreach (\App\Models\Branch::pluck('member_prefix','id') as $id => $entry)
                                                <option value="{{ $id }}" {{ Auth()->user()->employee &&  Auth()->user()->employee->branch_id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="text" class="form-control" name="card_number" value="" id="member_code" autofocus  ondragenter="getMember()" onfocus="this.setSelectionRange(this.value.length,this.value.length);" >
                                </div>
                            </div>
                        </div>
                    </form>
                    
                @endif
            </div>
        </div>
    </div>
</div>