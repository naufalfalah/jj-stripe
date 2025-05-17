
<div class="modal fade bd-example-modal-lg" id="createProjectModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.store_project') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Project') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Name --}}
                    <input type="text" name="name" class="form-control new-project-title mb-2" placeholder="Project name" required/>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-row-reverse">
                        <button type="submit" class="btn btn-primary">Create Project</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>