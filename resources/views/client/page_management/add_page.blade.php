@extends('layouts.front')
@section('page-css')
    <link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <style>
        .modal-footer-centered {
            display: flex;
            justify-content: center;
        }
        .adjust{
            width:450px;
            margin-left:20px;
            margin-right:10px;
        }

        .labler{
            margin-left:20px;
        }
    </style>

    <style>
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .image-preview {
            position: relative;
            width: 23%; /* Adjust for margins to fit 4 per row */
            margin: 1%;
        }

        .image-preview img {
            width: 100%; /* Ensure the image covers the full width of the container */
            max-height: 250px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background: #fff;
        }

        .image-preview .delete-icon {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px; /* Center the × symbol */
        }

    </style>

@endsection
@section('content')

    @include('client.page_management.include.'.$page_name);

@endsection
@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
            <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
            <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
@endsection
