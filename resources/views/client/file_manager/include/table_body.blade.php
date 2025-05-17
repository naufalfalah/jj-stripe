@foreach ($folder_files as $key => $files)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td><a href="{{ route('user.file_manager.file_detail', $files->hashid) }}">{{ Str::limit($files->file_name, 40, '...') ?? '' }}</a></td>
        <td>{{ $files->created_at->format('M-d-Y - h:i a') ?? '' }}</td>
        <td>
            <a href="javascript:void(0)" onclick="ajaxRequest(this)" data-url="{{ route('user.file_manager.delete', $files->id) }}"
                class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete File">
                <i class="bi bi-trash-fill"></i>
            </a>
            <a href="javascript:void(0)" class="text-success copy-button fs-5" data-clipboard-text="{{ route('file_view', $files->hashid)}}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy File Url">
                <i class="fadeIn animated bx bx-copy"></i>
            </a>
            <a href="{{ route('user.file_manager.file_detail', $files->hashid) }}" class="text-primary fs-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View File Detail">
                <i class="bi bi-eye-fill"></i>
            </a>
        </td>
    </tr>
@endforeach

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
