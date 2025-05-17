<input type="checkbox" class="form-check-input selected_leads" id="assign_lead" name="assign_lead"
    value="{{ $data->id }}">
@if (isset($data->assign->lead_id) && $data->assign->lead_id === $data->id)
    <span class="badge rounded-pill bg-success">Assigned</span>
@else
@endif
