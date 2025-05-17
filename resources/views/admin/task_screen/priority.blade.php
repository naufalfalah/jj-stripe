<div class="row mb-3">
    <div class="col-lg-3 col-md-6 task-priority-col">
        <div class="status-title"><i class="fa-regular fa-flag"></i> {{ __('Status') }}</div>
    </div>
    <div class="col-lg-9 col-md-6 task-priority-col">
        <div class="status-detail">
            <select id="task-priority" name="priority" class="task-priority" @disabled(auth()->user()->role_name != 'Manager')>
                @foreach ($priorities as $prioritySelect)
                    <option value="{{ $prioritySelect['value'] }}">{{ ucwords($prioritySelect['text']) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
