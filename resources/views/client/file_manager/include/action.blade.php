<a href="javascript:void(0)" onclick="ajaxRequest(this)" data-url="{{ route('user.file_manager.delete', $data->id) }}"
    class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete File">
    <i class="bi bi-trash-fill"></i>
</a>
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
