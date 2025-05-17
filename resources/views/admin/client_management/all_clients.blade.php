@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="">{{ $title }}</h5>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            S NO:</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Client Image</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Client Name</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Mobile</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Agency</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Package</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Email</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Address</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($all_clients as $key => $val)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $key + 1 }}</p>
                                            </td>
                                            <td>
                                                <img src='{{ check_file($val->image) }}' class='product-img-2'
                                                    alt='product'>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->client_name ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->phone_number ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->agency ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->package ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->email ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->address ?? '-' }}</p>
                                            </td>
                                            <td>
                                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                    <a href="{{ route('admin.client-management.view', $val->hashid) }}"
                                                        class="text-success" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title="View Files"><i
                                                            class="bi bi-eye"></i></a>
                                                </div>
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
    </div>
@endsection