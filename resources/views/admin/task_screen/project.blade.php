<div class="row mb-3 project-section">
    <div class="col-xl-3 col-md-6 task-project-col">
        <div class="project-title"><i class="fas fa-rocket"></i> {{ __('Project') }}</div>
    </div>
    <div class="col-xl-9 col-md-6 task-project-col">
        <div class="project-detail">
            <select id="task-project" name="project" class="task-project" @disabled(auth()->user()->role_name != 'Manager')>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ ucwords($project->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
