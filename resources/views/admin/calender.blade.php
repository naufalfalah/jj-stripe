@extends('layouts.admin')
@section('page-css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.css" rel="stylesheet" media="print">
@endsection
@section('content')
<div class="row">
    <form method="get" action="" class="row g-3 ajaxFormClient">
        <div class="row">
            <div class="form-group col-sm-12 col-lg-6 my-2">
                <label>All Clients <span class="text-danger">*</span></label>
                <input type="hidden" value="{{ request()->input('client') }}" id="client_id">
                <select name="client" id="client" class="form-control single-select" required>
                    <option value="">Select Client</option>
                    @foreach ($clients as $val)
                    <option value="{{ $val->hashid }}" {{ (request()->input('client') == $val->hashid) ? 'selected' : '' }}>
                        {{ $val->client_name }} ( {{ $val->email }} )
                    </option>
                    @endforeach
                </select>
            </div>

        </div>
    </form>
</div>
<div class="card">
    <div class="card-header">
        <a href="javascript:void(0);" class="btn btn-primary" id="add-event">Add Event</a>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Calender Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.google.save_event') }}" method="post" id="EventForm">
                @csrf
                <input type="hidden" name="user_id" value="{{$user_id}}">    
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="">Title<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Enter Event Title"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" id="date" name="date" class="form-control" value="" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Start Time <span class="text-danger">*</span></label>
                        <input type="text" name="start_time" class="form-control time-selector" value="">
                    </div>

                    <div class="form-group mb-3">
                        <span>End Time <span class="text-danger">*</span></span>
                        <input type="text" name="end_time" class="form-control time-selector" value="">
                    </div>

                    <div class="form-group mb-3">
                        <label for="">Event Description<span class="text-danger">*</span></label>
                        <textarea name="description" cols="30" rows="5" class="form-control"
                            placeholder="Event Description" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Event -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Calendar Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="EditEventForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="eventTitle">Title<span class="text-danger">*</span></label>
                        <input type="text" id="eventTitle" class="form-control" placeholder="Enter Event Title" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" id="editDate" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Start Time <span class="text-danger">*</span></label>
                        <input type="text" id="editStartTime" class="form-control time-selector" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>End Time <span class="text-danger">*</span></label>
                        <input type="text" id="editEndTime" class="form-control time-selector" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="eventDescription">Event Description<span class="text-danger">*</span></label>
                        <textarea id="eventDescription" cols="30" rows="5" class="form-control" placeholder="Event Description" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="updateEvent">Update</button>
                    <button type="button" class="btn btn-danger" id="deleteEvent">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script>
    $('#client').change(function() { 
        $('.ajaxFormClient').submit();
    })
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendar = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'month',
            timeFormat: 'HH:mm',
            slotLabelFormat: 'HH:mm',
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: "{{ route('admin.google.get_google_calender_events') }}",
                    method: 'GET',
                    data: {
                        start: start.format(),
                        end: end.format(),
                        user_id: "{{$user_id}}",
                    },
                    success: function(data) {
                        var events = [];
                        $(data).each(function() {
                            events.push({
                                id: this.id,
                                title: this.title,
                                description: this.description,
                                start: this.start,
                                end: this.end,
                                allDay: this.allDay,
                            });
                        });
                        callback(events);
                    },
                    error: function() {
                        alert('Error fetching calendar events');
                    }
                });
            },
            eventRender: function(eventObj, $el) {
                var dateTimeFormat = eventObj.allDay ? 'MMMM D, YYYY' : 'MMMM D, YYYY HH:mm';
                var popoverContent = 'Start: ' + moment(eventObj.start).format(dateTimeFormat);
                if (eventObj.end) {
                    popoverContent += '<br>End: ' + moment(eventObj.end).format(dateTimeFormat);
                }
                if (eventObj.description) {
                    popoverContent += '<br>Description: ' + eventObj.description;
                }

                $el.popover({
                    title: eventObj.title,
                    content: popoverContent,
                    trigger: 'hover',
                    placement: 'top',
                    container: 'body',
                    html: true // Enable HTML content in popover
                });
            },
            dayClick: function (date, allDay, jsEvent, view) {
                $('#EventForm')[0].reset();
                $('#addEventModal').modal('show');

                // Select date
                var formattedDate = formatDateForInput(date._d);
                $('#date').val(formattedDate);

                // Select time
                var hours = date._d.getHours();
                var minutes = date._d.getMinutes();
                var formattedTime = formatTimeForInput(hours, minutes);
                $('.time-selector').val(formattedTime);
            },
            eventClick: function(event) {
                var eventId = event.id; 

                $('#eventTitle').val(event.title);
                $('#eventDescription').val(event.description || '');
                $('#editDate').val(event.start.format('YYYY-MM-DD'));
                $('#editStartTime').val(event.start.format('HH:mm'));
                $('#editEndTime').val(event.end ? event.end.format('HH:mm') : '');
                
                $('#editEventModal').modal('show');

                $('#updateEvent').off('click').on('click', function() {
                    var updatedTitle = $('#eventTitle').val();
                    var updatedDescription = $('#eventDescription').val();
                    var updatedDate = $('#editDate').val();
                    var updatedStartTime = $('#editStartTime').val();
                    var updatedEndTime = $('#editEndTime').val();

                    event.title = updatedTitle;
                    event.description = updatedDescription;
                    event.start = moment(updatedDate + 'T' + updatedStartTime, "YYYY-MM-DDTHH:mm A");
                    event.end = moment(updatedDate + 'T' + updatedEndTime, "YYYY-MM-DDTHH:mm A");

                    updateEvent(eventId, updatedTitle, updatedDescription, updatedDate, updatedStartTime, updatedEndTime);
                    $('#calendar').fullCalendar('updateEvent', event);
                    $('#editEventModal').modal('hide');
                });

                $('#deleteEvent').off('click').on('click', function() {
                    if (confirm('Are you sure you want to delete this event?')) {
                        deleteEvent(eventId);
                        $('#calendar').fullCalendar('removeEvents', event._id);
                        $('#editEventModal').modal('hide');
                    }
                });
            },
            viewRender: function(view, element) {
                // Capitalize the first letter of month, week, and day
                $(".fc-month-button").text(function() {
                    return $(this).text().charAt(0).toUpperCase() + $(this).text().slice(1);
                });
                $(".fc-agendaWeek-button").text(function() {
                    return $(this).text().charAt(0).toUpperCase() + $(this).text().slice(1);
                });
                $(".fc-agendaDay-button").text(function() {
                    return $(this).text().charAt(0).toUpperCase() + $(this).text().slice(1);
                });
                $(".fc-today-button").text(function() {
                    return $(this).text().charAt(0).toUpperCase() + $(this).text().slice(1);
                });
            }
        });
    });



    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    function updateEvent(eventId, updatedTitle, updatedDescription, updatedDate, updatedStartTime, updatedEndTime) {
        console.log(eventId, updatedTitle, updatedDescription, updatedDate, updatedStartTime, updatedEndTime);
        $.ajax({
            url: "{{ route('admin.google.update_event') }}",
            type: 'PUT',
            data: {
                event_id: eventId,
                title: updatedTitle,
                description: updatedDescription,
                date: updatedDate,
                start_time: updatedStartTime,
                end_time: updatedEndTime,
                user_id: "{{$user_id}}",
            },
            success: function(response) {
                console.log("Event updated");
            },
            error: function(xhr, status, error) {
                toast('Some Thing went Wrong', "Error!", 'error');
            }
        });
    }

    function deleteEvent(eventId) {
        $.ajax({
            url: "{{ route('admin.google.delete_event') }}",
            type: 'DELETE',
            data: {
                event_id: eventId,
                user_id: "{{$user_id}}",
            },
            success: function(response) {
                console.log("Event deleted");
            },
            error: function(xhr, status, error) {
                toast('Some Thing went Wrong', "Error!", 'error');
            }
        });
    }

    $('#disconnectCalendarBtn').on('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "By this action your google calender is disconnected",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, confirm it!"
        }).then(function (t) {
            if (t.value){
                $.ajax({
                    url: "{{ route('user.google.google_calender_disconnected') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success){
                            toast('Google Calender Successfully Disconnected', "Success", 'success', 1200);
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('Some Thing went Wrong', "Error!", 'error');
                    }
                });
            }
        });

    });

    $("#date").flatpickr({
        minDate: "today",
        dateFormat: "Y-m-d"
    });

    $(".time-selector").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 1,
        defaultDate: null
    });

    function formatDateForInput(rawDate) {
        var year = rawDate.getFullYear();
        var month = (rawDate.getMonth() + 1).toString().padStart(2, '0');
        var day = rawDate.getDate().toString().padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
    function formatTimeForInput(hours, minutes) {
        return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
    }
    $(document).on('click','#add-event',function(){
        $('#EventForm')[0].reset();
        $('#addEventModal').modal('show');
    });

    $(document).ready(function() {
        validations = $("#EventForm").validate();
        $('#EventForm').submit(function(e) {
            e.preventDefault();
            validations = $("#EventForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {
            },true);
        });
    });
</script>
@endsection