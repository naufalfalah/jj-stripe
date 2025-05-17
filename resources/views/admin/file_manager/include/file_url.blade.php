<a href="{{ asset($data->file_path) }}" target="_blank">{{ Str::limit($data->file_name, 40, '...') ?? '' }}</a>
