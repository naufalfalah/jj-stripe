<div class="btn-group">
    <a href="{{ route('admin.assign_task.edit', $data->hashid) }}">
        <i class="bi bi-pencil-fill"></i> 
    </a> &nbsp; &nbsp;
   <a href="javascript:void(0)" class="text-danger" onclick="ajaxRequest(this)" data-url="{{route('admin.assign_task.delete',$data->hashid)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="bi bi-trash-fill"></i></a>
</div>