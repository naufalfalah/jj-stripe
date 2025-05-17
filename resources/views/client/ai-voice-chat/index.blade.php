@extends('layouts.front')

@push('styles')
<style>
    .microphone-container {
        background-color: white;
        border-radius: 50%;
        padding: 20px;
    }

    .microphone-container i {
        font-size: 36px;
        color: #007bff;
    }

    .modal-backdrop {
        background-color: rgb(255, 255, 255), 0.7);
    }

    .modal.show .modal-backdrop {
        background-color: rgb(255, 255, 255);
    }
</style>
@endpush

@section('content')
<div class="col-12 col-xl-12 mt-5">
    <div class="card shadow radius-10 w-100">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="chat-history mb-4" style="max-height: 400px; overflow-y: auto;">
                        @foreach ($chats as $chat)
                            <!-- User Message -->
                            <div class="d-flex align-items-start mb-3 flex-row-reverse">
                                <div>
                                    <div class="message bg-secondary text-white p-3 rounded" style="max-width: 75%;">
                                        <p class="mb-0">{{ $chat['property'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bot Message -->
                            <div class="d-flex align-items-start mb-3">
                                <div>
                                    <div class="message bg-light p-3 rounded" style="max-width: 75%;">
                                        <p class="mb-0">{{ $chat['converted_content'] }}</p>
                                    </div>
                                    @if(!empty($chat['audio_path']))
                                        <audio controls class="mt-2 mb-2">
                                            <source src="{{ asset('storage/ai-content/' . $chat['audio_path']) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="aiVoiceChatForm">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" id="message" rows="5" class="form-control" placeholder="Type your message here..." required>{{ old('message') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" id="startRecordBtn" class="btn btn-primary me-2">
                                <i class="bi bi-mic-fill"></i>
                            </button>
                            
                            <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-up-circle-fill"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Recording -->
<div class="modal fade" id="recordingModal" tabindex="-1" aria-labelledby="recordingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="microphone-container">
                    <i class="bi bi-mic-fill"></i>
                </div>
                <p>Voice chat in Progress...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        $(document).ready(function() {
            const chatHistory = document.querySelector('.chat-history');
            if (chatHistory) {
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }

            $('#message').on('keydown', function(event) {
                if (event.key === "Enter" && !event.shiftKey) {
                    event.preventDefault();
                    $(this).closest('form').submit();
                }
            });

            $('#message').on('input', function() {
                const messageValue = $(this).val().trim();
                if (messageValue) {
                    $('#startRecordBtn').hide();
                } else {
                    $('#startRecordBtn').show();
                }
            });

            let recognition;
            let isRecording = false;
            let silenceTimeout;

            if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
                recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                recognition.lang = 'en-SG'; 
                recognition.interimResults = true;
                recognition.maxAlternatives = 1;

                recognition.onresult = function(event) {
                    let transcript = '';
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        transcript += event.results[i][0].transcript;
                    }
                    $('#message').val(transcript);
                    resetSilenceTimeout();
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error:', event.error);
                    alert('Error with speech recognition: ' + event.error);
                };
            } else {
                alert('Speech Recognition API is not supported in your browser.');
            }

            $('#startRecordBtn').on('click', function() {
                if (isRecording) {
                    stopRecording();
                } else {
                    startRecording();
                }
            });

            function resetSilenceTimeout() {
                clearTimeout(silenceTimeout);
                silenceTimeout = setTimeout(stopRecording, 3000);
            }

            function stopRecording() {
                recognition.stop();
                // $('#startRecordBtn').text('Start Recording');
                $('#startRecordBtn').addClass('btn-primary').removeClass('btn-secondary');
                $('#recordingModal').modal('hide');
                isRecording = false;
            }

            function startRecording() {
                recognition.start();
                // $('#startRecordBtn').text('Stop Recording');
                $('#startRecordBtn').addClass('btn-secondary').removeClass('btn-primary');
                $('#recordingModal').modal('show');
                isRecording = true;
                resetSilenceTimeout();
            }

            $('#aiVoiceChatForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                const userMessage = $('#message').val().trim();

                if (!userMessage) {
                    return;
                }

                const userMessageHtml = `
                    <div class="d-flex align-items-start mb-3 flex-row-reverse">
                        <div>
                            <div class="message bg-secondary text-white p-3 rounded" style="max-width: 75%;">
                                <p class="mb-0">${userMessage}</p>
                            </div>
                        </div>
                    </div>
                `;
                $('.chat-history').append(userMessageHtml);

                const botLoadingHtml = `
                    <div class="d-flex align-items-start mb-3 bot-loading">
                        <div>
                            <div class="message bg-light p-3 rounded d-flex align-items-center" style="max-width: 75%;">
                                <div class="spinner-border text-primary me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0">Processing...</p>
                            </div>
                        </div>
                    </div>
                `;
                $('.chat-history').append(botLoadingHtml);

                chatHistory.scrollTop = chatHistory.scrollHeight;
                $('#message').val('');

                $.ajax({
                    url: "{{ route('ai-voice-chat.sendMessage') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('.bot-loading').remove();

                        if (response.success) {
                            const botMessageHtml = `
                                <div class="d-flex align-items-start mb-3">
                                    <div>
                                        <div class="message bg-light p-3 rounded" style="max-width: 75%;">
                                            <p class="mb-0">${response.data.aiResponse}</p>
                                        </div>
                                        ${response.data.audioUrl ? `<audio controls class="mt-2 mb-2">
                                            <source src="${response.data.audioUrl}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>` : ''}
                                    </div>
                                </div>
                            `;
                            $('.chat-history').append(botMessageHtml);
                            chatHistory.scrollTop = chatHistory.scrollHeight;

                            if (response.data.audioUrl) {
                                const audio = new Audio(response.data.audioUrl);
                                audio.play();
                            }
                        } else {
                            alert(response.message || "Failed to send message.");
                        }
                    },
                    error: function(xhr) {
                        alert("Error: " + (xhr.responseJSON?.message || "An unknown error occurred."));
                    }
                });
            });

        });
        
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
