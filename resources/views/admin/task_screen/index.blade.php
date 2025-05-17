@extends('layouts.admin')

@push('styles')
    <link href="{{ asset('front') }}/assets/css/dashboard/dashboard.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-6">
                <h3 class="font-weight-light">Dashboard</h3>
            </div>
            @if (auth()->user()->role == 'MANAGER')
                <div class="col-6 d-flex flex-row-reverse">
                    <button class="btn btn-primary create-task ms-2">Add Task</button>
                    <button class="btn btn-secondary create-project ms-2">Add Project</button>
                </div>
            @endif
        </div>
        <div class="row flex-row flex-sm-nowrap py-3">
            @foreach ($groups as $group)
                {{-- Task Group --}}
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-uppercase text-truncate py-2">{{ ucwords($group->name) }}</h6>
                            <div class="task-container items border border-light" id="group-{{ $group->id }}">
                                @foreach ($group->form_requests as $task)
                                    @if (auth()->user()->role == 'MANAGER' || $task->is_task_assign_to_admin())
                                        {{-- Task Card --}}
                                        <div class="card draggable shadow cursor-pointer m-2" id="{{ $task->id }}"
                                            draggable="true" data-task-id="{{ $task->id }}" data-toggle="modal"
                                            data-target="#taskModal">
                                            <div class="card-body p-2">
                                                <div class="card-title">
                                                    <div class="row">
                                                        {{-- Title --}}
                                                        <div class="col-md-12 col-xl-9">
                                                            <img src="https://img.freepik.com/free-vector/3d-cartoon-style-clipboard-with-document-icon-realistic-paper-holder-with-contract-agreement-flat-vector-illustration-management-information-assignment-concept_778687-986.jpg?t=st=1709054544~exp=1709058144~hmac=6139d87cc4583b7286c0035d368ff2448478ba9a568a3eb080de8215065970f0&w=740"
                                                                width="30px" height="30px"
                                                                class="rounded-circle float-right">
                                                            <span
                                                                class="lead font-weight-light text-primary">{{ $task->title }}</span>
                                                        </div>
                                                        {{-- Assign --}}
                                                        <div class="col-md-12 col-xl-3"
                                                            id="task-{{ $task->id }}-assign">
                                                            @foreach ($task->assigns as $index => $assign)
                                                                @if ($index < 3)
                                                                    @if ($assign->admin)
                                                                        <img src="{{ $assign->admin->image ? asset($assign->admin->image) : $default_avatar }}"
                                                                            class="assign-img rounded-circle z-index-{{ 3 - $index }}"
                                                                            alt="ava" />
                                                                    @else
                                                                        <img src="{{ $default_avatar }}"
                                                                            class="assign-img rounded-circle z-index-{{ 3 - $index }}"
                                                                            alt="ava" />
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Description --}}
                                                <p class="card-description" id="task-{{ $task->id }}-description">
                                                    {{ $task->description }}
                                                </p>
                                                {{-- Subtasks --}}
                                                @if (count($task->subtasks))
                                                    <div id="task-{{ $task->id }}-subtask">
                                                        <p class="mb-1">Subtask:
                                                            <span
                                                                id="task-{{ $task->id }}-subtask_count">{{ $task->doneSubtasksCount() }}/{{ $task->subtasksCount() }}</span>
                                                        </p class="mb-1">
                                                        <progress value="{{ $task->doneSubtasksPercentage() }}"
                                                            max="100"
                                                            id="task-{{ $task->id }}-subtask_percentage_value"></progress>
                                                        <span
                                                            id="task-{{ $task->id }}-subtask_percentage">{{ floor($task->doneSubtasksPercentage()) }}
                                                            %</span>
                                                    </div>
                                                @endif
                                                {{-- Priority --}}
                                                @php
                                                    foreach ($priorities as $priority) {
                                                        if ($task->priority == $priority['value']) {
                                                            $taskPriority = $priority;
                                                        }
                                                    }
                                                @endphp
                                                <span id="task-{{ $task->id }}-priority"
                                                    class="badge bg-{{ $taskPriority['bgClass'] }} {{ $taskPriority['textClass'] ?? '' }}">
                                                    {{ $taskPriority['text'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @include('admin.task_screen.popup')

        @include('admin.task_screen.create_task_popup')
        @include('admin.task_screen.create_project_popup')
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        var showTaskUrl = "{{ route('admin.show_task') }}";
        var updateTaskUrl = "{{ route('admin.update_task') }}";
        var storeSubtaskUrl = "{{ route('admin.store_subtask') }}";
        var showSubtaskUrl = "{{ route('admin.show_subtask') }}";
        var updateSubtaskUrl = "{{ route('admin.update_subtask') }}";
        var destroySubtaskUrl = "{{ route('admin.destroy_subtask') }}";
        var pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
        var pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
        var messageSend = "{{ route('admin.task_send_message_from.admin') }}";
        var admins = {!! $admins->toJson() !!};
        var imagePath = "{{ asset('') }}"
        var defaultAvatar = "{{ $default_avatar }}"
        var isManager = "{{ auth()->user()->role_name == 'Manager' }}"
    </script>
    <script src="{{ asset('front') }}/assets/js/dashboard/dashboard.js"></script>
@endpush
