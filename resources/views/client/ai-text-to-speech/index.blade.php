@extends('layouts.front')

@section('content')
<div class="col-12 col-xl-12 mt-5">
    <div class="card shadow radius-10 w-100">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-6">
                    <h5 class="mb-0">{{ $title }}</h5>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    @if (session('audioUrl'))
                        <div class="alert alert-success">
                            <strong>Audio generated successfully!</strong> You can listen to it below:
                        </div>
                        <audio controls autoplay class="mt-2 mb-2" id="audioPlayer">
                            <source src="{{ session('audioUrl') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.ai-tts.generateSpeech') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="text" class="form-label">Text</label>
                            <textarea name="text" id="text" rows="5" class="form-control" required>{{ old('text') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="voice_id" class="form-label">Voice ID</label>
                            <select name="voice_id" id="voice_id" class="form-select" required>
                                <option value="9BWtsMINqrJLrRacOk9x" {{ old('voice_id') === '9BWtsMINqrJLrRacOk9x' ? 'selected' : '' }}>Singapore English - Luna</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Convert to Voice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.onload = function() {
        var audio = document.getElementById('audioPlayer');
        if (audio) {
            audio.play().catch(function(error) {
                console.log('Audio playback failed:', error);
            });
        }
    };
</script>
@endpush