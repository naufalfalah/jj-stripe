<div class="row mb-3">
    <div class="col-lg-3 col-md-6 task-due_date-col">
        <div class="status-title"><i class="fa-regular fa-calendar"></i> {{ __('Due Date') }}</div>
    </div>
    <div class="col-lg-9 col-md-6 task-due_date-col">
        <input type="text" name="daterange" id="task-due_date" class="form-control mb-2 task-due_date"
            @disabled(auth()->user()->role_name != 'Manager') />
    </div>
</div>
