@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12 mx-auto">
        <div class="card">
            <div class="card-title text-bold p-3">
                Detail Script
            </div>
            <form action="{{route('admin.scripts.update', $script->id)}}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="titleScript">Title</label>
                        <input value="{{$script->title}}" min="5" required type="text" name="title"
                            placeholder="Title Script" id="title" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="descScript">Description</label>
                        <textarea name="desc" id="desc" placeholder="Description" class="form-control rich">{!! $script->desc !!}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="scriptText">Script Text</label>
                        <textarea value="" required name="script_text" placeholder="Script text" id="script_text"
                            class="form-control rich">{!! $script->script_text !!}</textarea>
                    </div>
                    <div class="modal-footer">
                        <a href="{{route('admin.scripts.index')}}" type="button" class="btn btn-secondary"
                            data-dismiss="modal">Back</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
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
                    '|',
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
@endsection