<div class="row mb-3">
    <div class="col-xl-3 col-md-6 task-status-col">
        <div class="status-title"><i class="fas fa-bullseye"></i> {{ __('Status') }}</div>
    </div>
    <div class="col-xl-9 col-md-6 task-status-col">
        <div class="status-detail">
            <select id="task-group" name="group" class="task-group" @disabled(auth()->user()->role_name != 'Manager')>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ ucwords($group->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
