@push('styles')
    <link rel="stylesheet" href="{{ asset('front') }}/assets/plugins/daterangepicker/css/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('front') }}/assets/css/dashboard/popup.css" />
@endpush

<div class="modal fade bd-example-modal-lg" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Dashboard') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding-top:0px">
                <div class="row">
                    <div class="col-lg-8 col-md-12 task-detail h-100" style="padding-top:8px">
                        <div class="icon-buttons">
                            <div class="options">
                                <i class="options-icon fa-regular fa-circle-dot"></i>
                                <select name="task" id="" class="selectpicker">
                                    <option value="task" data-icon="glyphicon-music">{{ __('Task') }}</option>
                                </select>
                                <div class="options-text">
                                    <p id="task-slug" class="mb-0"></p>
                                </div>
                            </div>
                        </div>
                        <div class="popup-title mt-3">
                            <h2 id="task-title"></h2>
                        </div>
                        <div class="popup-status mt-4">
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    {{-- Status --}}
                                    @include('admin.task_screen.status')
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    {{-- Assign --}}
                                    @include('admin.task_screen.assign')
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    {{-- Dates --}}
                                    @include('admin.task_screen.dates')
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    {{-- Priority --}}
                                    @include('admin.task_screen.priority')
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    {{-- Client --}}
                                    @include('admin.task_screen.client')
                                    {{-- Project --}}
                                    @include('admin.task_screen.project')
                                </div>
                            </div>
                        </div>
                        <div class="popup-data m-1 mt-2 p-2">
                            <div id="task-datas" class="data-list">
                                {{-- Datas List --}}
                            </div>
                            <button id="show-less-task-datas" class="button-show d-none">
                                <i class="fa-solid fa-chevron-up"></i> {{ __('Hide') }}
                            </button>
                            <button id="show-more-task-datas" class="button-show">
                                <i class="fa-solid fa-chevron-right"></i> {{ __('Show more') }}
                            </button>
                        </div>
                        <div class="popup-desc mt-4">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Add description" id="task-description" @disabled(auth()->user()->role_name != 'Manager')></textarea>
                                <label for="task-description">
                                    <i class="fa-regular fa-file"></i>&nbsp{{ __('description') }}
                                </label>
                            </div>
                        </div>

                        @include('admin.task_screen.tabs')

                        @include('admin.task_screen.table')
                    </div>

                    @include('admin.task_screen.aside')
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/daterangepicker/js/daterangepicker.min.js"></script>
@endpush
