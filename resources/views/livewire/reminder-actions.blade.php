<div>
    <div class="form-group">
        <label for="action">Action</label>
        <select name="action" id="action" class="form-control" wire:model.live="action">
            <option value="{{ null }}">Select Action</option>
            @foreach (App\Models\Reminder::ACTION as $key => $value)
                @if (auth()->user()->roles[0]->title == 'Trainer')
                    @if ($key == 'done' || $key == 'appointment' || $key == 'not_interested')
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endif
                @else
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif
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
