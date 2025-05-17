@extends('layouts.admin')

@push('styles')
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="row">
        <div class="modal fade" id="addScriptModal" tabindex="-1" role="dialog" aria-labelledby="addScriptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <form action="{{ route('admin.scripts.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addScriptModalLabel">Add Script</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="titleScript">Title</label>
                                <input min="5" required type="text" name="title" placeholder="Title Script" id="title"
                                    class="form-control" value="{{ old('title') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="descScript">Description</label>
                                <textarea name="desc" id="desc" placeholder="Description" class="form-control rich"
                                    cols="30" rows="10">{{ old('desc') }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="scriptText">Script Text</label>
                                <textarea required name="script_text" placeholder="Script text" id="script_text"
                                    class="form-control rich" cols="30" rows="10">{{ old('script_text') }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-title p-3 text-bold">
                    Scripts
                </div>
                <div class="card-body">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addScriptModal">Add
                        Script</button>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="script_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/ckeditor/ckeditor.js"></script>
    <script>
        $(".rich").each(function () {
            let id = $(this).attr('id');
            ClassicEditor
            .create( document.querySelector('#'+id), {
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        '|',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'insertTable',
                        '|',
                        'undo',
                        'redo'
                    ]
                },
                table: {
                    contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
                },
                language: 'en'
            })
            .catch( error => {
                console.error( error );
            });
        });
    </script>
    <script>
        var table = $("#script_table").DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            ajax: {
                url: "{{ route('admin.scripts.index') }}",
            },
            columns: [
                { name: 'DT_RowIndex', data: 'DT_RowIndex' },
                {name: 'title', data: 'title'},
                {name: 'action', data: 'action', orderable: false, searchable: false},
            ]
        });

        function deleteScript(id) {
            let url = "{{ route('admin.scripts.delete', ':id')}}";
            url = url.replace(':id', id);
            Swal.fire({
                title: "Do you want to delete this script?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: "Delete",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: url,
                        success: function () {
                            Swal.fire("Deleted successfully!", "", "success");
                            table.draw();
                        },
                        error: function () {
                            Swal.fire("There is something wrong, Your data can not be delete. Please try again", "", "error");
                        }
                    })
                } else if (result.dismiss) {
                    return null
                }
            });
        }
    </script>
@endpush