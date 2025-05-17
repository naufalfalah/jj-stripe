<div class="row mb-3">
    <div class="col-lg-3 col-md-6 task-assign-col">
        <div class="status-title"><i class="fa-regular fa-user"></i> {{ __('Assigned to') }}</div>
    </div>
    <div class="col-lg-9 col-md-6 task-assign-col">
        <div class="assigned-detail" id="task-assigns">
            {{-- Assigned img --}}
            <div class="dropdown d-inline">
                @if (auth()->user()->role_name == 'Manager')
                    <button class="dropdown-toggle assign-button" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-plus"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach ($admins as $admin)
                            <li>
                                <button class="dropdown-item assign-item" id="assign-item-{{ $admin->id }}"
                                    data-admin-id="{{ $admin->id }}">
                                    <img src="{{ asset(check_file($admin->image, 'user')) }}"
                                        class="rounded-circle assign-option-img" alt="ava" />
                                    {{ $admin->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
