<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">Chat</div>
            <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto;">
                <!-- Messages will be appended here -->
            </div>
            {{-- <div class="card-footer">
                <div class="input-group">
                    <input type="text" id="message-input" class="form-control" placeholder="Type your message...">
                    <button id="send-btn" class="btn btn-primary">Send</button>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let role = `{{ $role }}`
        let toPhoneNumber = `{{ $toPhoneNumber }}`;
        let page = 1;
        let isFetching = false;
        let canFetch = true;

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            
            // Get the difference in days
            const diffInTime = now - date;
            const diffInDays = Math.floor(diffInTime / (1000 * 60 * 60 * 24));
            
            const optionsTime = { hour: 'numeric', minute: 'numeric', hour12: true };

            // Format time part
            const time = date.toLocaleString('en-US', optionsTime);

            // Today
            if (diffInDays === 0) {
                return `today, ${time}`;
            }

            // Yesterday
            if (diffInDays === 1) {
                return `yesterday, ${time}`;
            }

            // Within the last week
            if (diffInDays < 7) {
                const optionsWeekday = { weekday: 'long' }; // Saturday, etc.
                const weekday = date.toLocaleString('en-US', optionsWeekday);
                return `${weekday.toLowerCase()}, ${time}`;
            }

            // More than a week ago
            const optionsFullDate = { month: 'long', day: 'numeric' }; // October 18
            const fullDate = date.toLocaleString('en-US', optionsFullDate);
            return `${fullDate}, ${time}`;
        }

        // Function to append messages to chat box
        function prependMessage(message, sender, timestamp, status) {
            if (role == 'client') {
                sender = !sender;
            }

            let messageHtml = '';
            let statusText = '';
            let statusClass = '';

            // Determine status text and icon
            if (status === 'read') {
                statusText = '✔✔ Read';
                statusClass = 'text-success';
            } else if (status === 'delivered') {
                statusText = '✔ Delivered';
                statusClass = 'text-secondary';
            } else {
                statusText = '✔ Sent';
                statusClass = 'text-muted';
            }

            // Build message HTML based on sender
            if (sender) {
                messageHtml = `
                    <div class="d-flex justify-content-end mb-3">
                        <div>
                            <div class="p-3 bg-primary text-white rounded">
                                <span class="message-text">${message}</span>
                            </div>
                            <div class="message-info d-flex align-items-center justify-content-end">
                                <small class="text-muted">Sent at ${timestamp}</small>
                                <span class="ms-2 ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </div>`;
            } else {
                messageHtml = `
                    <div class="d-flex justify-content-start mb-3">
                        <div>
                            <div class="p-3 bg-light text-dark rounded">
                                <span class="message-text">${message}</span>
                            </div>
                            <div class="message-info d-flex align-items-center">
                                <small class="text-muted">Sent at ${timestamp}</small>
                                <span class="ms-2 ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </div>`;
            }

            // Append to chat box and scroll down
            $('#chat-box').prepend(messageHtml);
            if (page == 1) { // Auto-scroll at the first load
                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            }
        }

        // Function to fetch messages
        function fetchMessages() {
            $.ajax({
                url: '/api/message',
                type: 'GET',
                data: {
                    to_phone_number: toPhoneNumber,
                    page: page,
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length) {
                            let messages = response.data;
                            messages.forEach(function(msg) {
                                let isSender = typeof msg.sent !== 'undefined';
                                let timestamp = formatDate(msg.created_at);
                                let status = msg.read ? 'read' : (msg.received ? 'delivered' : (msg.sent ? 'sent' : null));
                                
                                prependMessage(msg.message.text, isSender, timestamp, status);
                            });
                        }
                    }
                    page++;
                    if (response.data.length < 5) {
                        canFetch = false;
                    }
                    isFetching = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching messages:', error);
                    isFetching = false;
                }
            });
        }

        // On page load, fetch initial messages
        $(document).ready(function() {
            fetchMessages();
        });

        $('#chat-box').on('scroll', function() {
            if (canFetch) {
                if ($(this).scrollTop() === 0 && !isFetching) {
                    fetchMessages();
                }
            }
        });

        // Function to send a new message
        $('#send-btn').click(function() {
            let message = $('#message-input').val();
            if (message.trim() !== '') {
                prependMessage(message, 'user'); // Append immediately in UI for user
                $('#message-input').val(''); // Clear input

                // AJAX call to send the message (you'll need an API endpoint for this)
                $.ajax({
                    url: '/api/send-message',
                    type: 'POST',
                    data: {
                        session_key: sessionKey,
                        message: message
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Message sent successfully.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error sending message:', error);
                    }
                });
            }
        });
    </script>
@endpush