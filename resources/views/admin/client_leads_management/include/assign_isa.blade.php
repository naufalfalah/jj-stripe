@if (isset($data->assign->lead_id) && $data->assign->lead_id === $data->id)
    <span class="badge rounded-pill bg-success">Assigned</span>
@else
    <span class="badge rounded-pill bg-danger">Un Assigned</span>
@endif
