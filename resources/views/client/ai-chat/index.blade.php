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
                    @if (session('aiResponse'))
                        <div class="alert alert-success">
                            <strong>AI Response:</strong>
                            <div class="mt-3" id="formatted-response">{{ session('aiResponse') }}</div>
                        </div>
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

                    <form action="{{ route('user.ai-chat.sendMessage') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea name="message" id="message" rows="5" class="form-control" placeholder="Type your message here..." required>{{ old('message') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <h6 class="mb-3">Conversation History</h6>
                    <div class="chat-box border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        @forelse ($conversationHistory ?? [] as $conversation)
                            <div class="mb-3">
                                <div><strong>You:</strong> {{ $conversation['user_message'] }}</div>
                                <div><strong>AI:</strong> {{ $conversation['ai_response'] }}</div>
                            </div>
                        @empty
                            <p class="text-muted">No conversation history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
