@if ($data->status == 'uncontacted')
<a href="{{ route('user.leads-management.client_details',$data->hashid) }}"><span class="badge rounded-pill bg-success">uncontacted</span> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name ?? ''}}</a>
@elseif ($data->status == 'junk')
<a href="{{ route('user.leads-management.client_details',$data->hashid) }}"><span class="badge rounded-pill bg-danger">Junk</span> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name ?? ''}}</a>
@elseif ($data->status == 'spam')
<a href="{{ route('user.leads-management.client_details',$data->hashid) }}"><span class="badge rounded-pill bg-warning text-dark">Spam</span> <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name ?? ''}}</a>
@else
<a href="{{ route('user.leads-management.client_details',$data->hashid) }}">
    <span class="text-muted">{!! getLeadSourceIcon($data->source_type_id) !!}</span> {{ $data->name }}</a>
@endif
