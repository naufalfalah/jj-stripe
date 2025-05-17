@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Add Notification</h5>
                        </div>
                        <hr/>
                        <div class="col-md-12">
                            <label for="title">Title<span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" placeholder="Enter Title" class="form-control" disabled>
                        </div>
                        <div class="col-md-12">
                            <label for="body">Body<span class="text-danger">*</span></label>
                            <input type="text" name="body" id="body" placeholder="Enter Body" class="form-control" disabled>
                        </div>
                        <div class="col-md-12">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select" disabled>
                                @foreach ($types as $key => $type)
                                    <option value="{{ $key }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="card-title d-flex align-items-center">
                                <h5 class="">Schedule</h5>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 d-flex flex-row-reverse bd-highlight">
                            <a href="{{ route('admin.notification.schedule-create', ['id' => $notification->id]) }}" class="btn btn-primary ml-auto" id="assign_leads_btn" style="white-space: nowrap;">
                                Create
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        #</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Receiver</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Published At</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notificationSchedules as $index => $schedule)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $index + 1 }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $schedule->client_id > 0 ? $schedule->client?->client_name : "All" }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $schedule->published_at }}</p>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $schedule->is_published ? 'primary' : 'secondary' }}">
                                                {{ $schedule->is_published ? 'Published' : 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
