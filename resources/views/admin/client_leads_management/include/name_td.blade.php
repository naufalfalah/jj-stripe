@if ($data->status == 'uncontacted')
<a href="{{ route('admin.lead-management.client_details',$data->hashid) }}"><span class="badge rounded-pill bg-success">uncontacted</span> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name }}</a>
@elseif ($data->status == 'junk')
<a href="{{ route('admin.lead-management.client_details',$data->hashid) }}"><span class="badge rounded-pill bg-danger">Junk</span> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name }}</a>
@elseif ($data->status == 'spam')
<a href="{{ route('admin.lead-management.client_details',$data->hashid) }}"><span class="badge rounded-pill bg-warning text-dark"> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> Spam</span> {{ $data->name }}</a>
@else
<a href="{{ route('admin.lead-management.client_details',$data->hashid) }}"> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name }}</a>
@endif
