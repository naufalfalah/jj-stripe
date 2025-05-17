{{-- Tab --}}
<div class="popup-tabs mt-3">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">{{ __('Subtask') }}
            </button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        {{-- Tab - Subtask --}}
        <div class="tab-pane subtask-list fade show active" id="profile-tab-pane" role="tabpanel"
            aria-labelledby="profile-tab" tabindex="0">
            <div class="tabs-title mt-3">
                <div class="text-title">{{ __('Subtask') }}</div>
                <progress id="task-subtasks_percentage" value="" max="100"></progress>
                <span id="task-subtasks_count"></span>
            </div>
            <table class="table task-table mt-1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Assigne</th>
                        <th>Priority</th>
                        <th>Due date</th>
                        @if (auth()->user()->role_name == 'Manager')
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="task-subtasks">
                    {{-- Subtask Rows --}}
                </tbody>
                <tfoot>
                    @if (auth()->user()->role_name == 'Manager')
                        <tr>
                            <td colspan="5">
                                <button class="btn btn-secondary add-subtask">Add New Subtask</button>
                            </td>
                        </tr>
                    @endif
                </tfoot>
            </table>
            {{-- <div class="tabs-footer mt-3">
                <div class="badge text-bg-light ">Show 5 Closed</div>
            </div> --}}
        </div>
    </div>
</div>
