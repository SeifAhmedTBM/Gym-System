<div class="row">
    <div class="col-md-6">
        <label class="required" for="member_code">{{ trans('cruds.membership.fields.member') }}</label>
        <input wire:model.live="search" type="text" class="form-control" placeholder="Search members..."/>
    </div>
    <div class="col-md-6">
        <label class="required" for="member">{{ trans('cruds.membership.fields.member') }}</label>
        <select name="member_id" id="member_id" class="form-control select2">
            @foreach ($members as $member)
                <option value="{{ $member['id'] }}" selected>{{ $member['name'] }}</option>
            @endforeach
        </select>
    </div>
</div>

