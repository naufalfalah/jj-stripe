<a href="{{ route('user.email-template.temp_details',$data->hashid) }}" title="{{$data->title}}">
    {{ Str::limit($data->title, 25) }}
</a>
