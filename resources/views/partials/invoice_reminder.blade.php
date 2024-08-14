<div class="card reminderCard">
    <div class="card-header">
        {{ trans('cruds.reminder.title_singular') }}
    </div>
    <div class="card-body">
        <div class="row">
            {{-- <div class="col-md-6">
                <label for="">{{ trans('cruds.status.title_singular') }}</label>
                <select name="member_status_id" id="member_status_id" onchange="getStatus()" class="form-control">
                    <option>{{ trans('global.pleaseSelect') }}</option>
                    @foreach ($memberStatuses as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div> --}}
            <div class="col-md-6">
                <label for="due_date">{{ trans('global.due_date') }}</label>
                <input type="date" class="form-control" name="due_date" id="due_date">
            </div>
        </div>
    </div>
</div>