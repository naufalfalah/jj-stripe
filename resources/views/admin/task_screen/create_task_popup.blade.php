<div class="modal fade bd-example-modal-lg" id="createTaskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.store_task') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Task') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Title --}}
                    <input type="text" name="title" class="input-inline new-task-title mb-2" placeholder="Task name" required/>
                    {{-- Description --}}
                    <textarea class="form-control"  name="description" placeholder="Add description"></textarea>
                    {{-- Property --}}
                    <div class="pt-4 pb-2 d-flex">
                        {{-- Status --}}
                        <select name="form_group_id" class="new-task-group me-3">
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ ucwords($group->name) }}</option>
                            @endforeach
                        </select>
                        {{-- Date --}}
                        <input type="text" name="due_date" class="form-inline new-task-due_date me-3" />
                        {{-- Priority --}}
                        <select name="priority" class="new-task-priority me-3">
                            @foreach ($priorities as $prioritySelect)
                                <option value="{{ $prioritySelect['value'] }}">{{ ucwords($prioritySelect['text']) }}</option>
                            @endforeach
                        </select>
                        {{-- Type --}}
                        <select name="type" id="newTaskType" class="new-task-type me-3" required>
                            <option value="">Type</option>
                            <option value="INTERNAL">{{ ucwords("internal") }}</option>
                            <option value="CLIENT">{{ ucwords("client") }}</option>
                        </select>
                    </div>
                    {{-- Additional Property --}}
                    <div class="pt-2 pb-2 d-flex">
                        {{-- Project --}}
                        <select name="project_id" class="new-task-project me-3 d-none">
                            <option value="">Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ ucwords($project->name) }}</option>
                            @endforeach
                        </select>
                        {{-- Client --}}
                        <select name="client_id" class="new-task-client me-3 d-none">
                            <option value="">Client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ ucwords($client->client_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-row-reverse">
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>