@php
    $trainers = Auth::user()->employee->branch
        ? App\Models\User::whereRelation('roles', 'title', 'Trainer')
            ->whereHas('employee', fn($i) => $i->whereHas('branch', fn($x) => $x->where('id', Auth::user()->employee->branch->id)))
            ->orderBy('name')
            ->pluck('name', 'id')
        : App\Models\User::whereRelation('roles', 'title', 'Trainer')
            ->orderBy('name')
            ->pluck('name', 'id');
@endphp
<div>
    @if (Auth()->user()->roles[0]->title == 'Trainer')
        <input type="hidden" name="trainer_id" value="{{ auth()->id() }}">
    @else
        <div class="form-group">
            <label for="trainer_id">Trainer</label>
            <select name="trainer_id"  class="form-control">
                <option value="{{ null }}">Select Trainer</option>

            @foreach ($trainers as $trainer_id => $trainer_name)
                    <option value="{{ $trainer_id }}">{{ $trainer_name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="form-group">
        <label for="action">Action</label>
        <select name="action" id="action" class="form-control" wire:model.live="action">
            <option value="{{ null }}">Select Action</option>
            @foreach (App\Models\Reminder::ACTION as $key => $value)
{{--                @if (auth()->user()->roles[0]->title == 'Trainer')--}}
{{--                    @if ($key == 'done' || $key == 'appointment' || $key == 'not_interested')--}}
{{--                        <option value="{{ $key }}">{{ $value }}</option>--}}
{{--                    @endif--}}
{{--                @else--}}
{{--                @endif--}}
                @if ($key != 'done' && $key != 'not_interested')
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif
{{--                    <option value="{{ $key }}">{{ $value }}</option>--}}
            @endforeach

        </select>
    </div>
    @if ($due_date_active == true)
        <div class="form-group">
            <label for="due_date">{{ trans('cruds.reminder.fields.next_due_date') }}</label>
            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $due_date }}">
        </div>
    @endif
</div>
