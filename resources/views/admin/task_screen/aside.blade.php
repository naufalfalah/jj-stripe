{{-- Aside --}}
<div class="col-lg-4 col-md-12 border-left">
    <div class="row h-100">
        <div class="col-12 h-100 activity">
            <div class="activity-header h-20">
                <div class="activity-title">Activity</div>
                <div class="activity-icons">
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="activity-body h-20">
                <div id="task-activities-more" class="activity-list d-none">
                    {{-- More Activities List --}}
                </div>
                <button id="show-less-task-activities" class="btn-no-border d-none">
                    <i class="fa-solid fa-chevron-up"></i> {{ __('Hide') }}
                </button>
                <button id="show-more-task-activities" class="btn-no-border">
                    <i class="fa-solid fa-chevron-right"></i> {{ __('Show more') }}
                </button>
                <div id="task-activities" class="activity-list">
                    {{-- Activities List --}}
                </div>
            </div>

            {{-- Activity Chat --}}
            @if (auth('admin')->user()->role_name == 'Manager' ||
                    str_contains(auth('admin')->user()->role_name, 'manager') ||
                    str_contains(auth('admin')->user()->role_name, 'Manager'))
                <div class="activity-chat h-100">
                    <div class="chat-list container h-100 ">
                        <div class="chat-items container">
                            <div class="chat-item" id="chat-item" style="overflow: scroll">

                                @foreach ($messages as $key => $date)
                                    <div class="conversation-start">
                                        <span class="text-bold">{{ $key }}</span>
                                    </div>
                                    @foreach ($messages[$key] as $message)
                                        <div class="chat">
                                            <div
                                                class="bubble {{ $message['id'] == auth()->user()->id && $message['type_name'] == auth()->user()->table_name ? 'me' : 'you' }}">
                                                {{ $message['message'] }}
                                            </div>
                                            <span
                                                class="chat-msg-date {{ $message['id'] == auth()->user()->id && $message['type_name'] == auth()->user()->table_name ? 'me' : '' }}">{{ $message['time'] }}</span>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <form id="message-form" class="chat-box my-2">
                            @csrf
                            <button type="button"><i class="fa-solid fa-microphone"></i></button>
                            <input type="hidden" name="to_user_id" id="to_user_id">
                            <input type="hidden" name="to_user_type" id="to_user_type"
                                value="{{ \App\Models\User::class }}">
                            <textarea class="chat-text" id="chat-text" placeholder="Type a message.." rows="1"></textarea>
                            <button class="emoji-button" type="button"><i
                                    class="fa-regular fa-face-smile"></i></button>
                            <label><i class="fa-solid fa-paperclip"></i>
                                <input type="file" name="" id=""></label>

                            <button type="submit" class="send-button" id="send-button" type="button">
                                <i class="fa-regular fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>

            @endif
        </div>

        {{-- Sidebar --}}

    </div>
</div>
