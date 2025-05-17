/** Task Detail */
var taskId
let messages = [];

$(document).ready(function () {
    // Initialize Sortable for task groups
    $('.task-container').each(function () {
        new Sortable(this, {
            group: 'shared',
            animation: 150,
            onEnd: function (evt) {
                const groupId = $(evt.to).attr('id').split('-')[1];
                const tasks = $.map($(evt.to).children(), function (item) {
                    return $(item).attr('data-task-id');
                });

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                let order = 1;
                for (let task of tasks) {
                    $.ajax({
                        url: updateTaskUrl,
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        data: {
                            task_id: task,
                            form_group_id: groupId,
                            order: order,
                        },
                        success: function (response) {
                            // console.log(response);
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                    order++;
                }
            }
        });
    });

    // Initialize Sortable for task board columns
    // new Sortable($('.row.flex-row')[0], {
    //     handle: '.card-title',
    //     animation: 150,
    //     onEnd: function (evt) {
    //         // Handle the column drop event
    //         const groupId = $(evt.item).attr('wire:sortable.item');
    //         // Update group order in your backend if necessary
    //     }
    // });

    $('.draggable').on('click', () => {
        $('#taskModal').modal('show');
    })

    let messages = [];
    var pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        encrypted: true
    });

    var channel = pusher.subscribe('user-message');
    // Function to update chat interface
    function updateChatInterface(messages) {
        // Clear existing chat items
        $('#chat-item').empty();

        // Iterate over messages and construct HTML
        $.each(messages, function (date, messagesArray) {
            $('#chat-item').append('<div class="conversation-start"><span class="text-bold">' + date + '</span></div>');
            $.each(messagesArray, function (index, message) {
                if (message) {
                    var bubbleClass = message.type_name == 'user' ? 'you' : 'me';
                    var bubbleContent = '<div class="bubble ' + bubbleClass + '">';
                    if (message.image && message.image.length > 0) {
                        message.image.forEach((item) => {
                            bubbleContent += `
                        <img class="image-preview" class="mx-2" src="${item}"
                            alt="image_chat.png">
                        <div class="overlay">
                            <a href="${item}" target="_blank"><i
                                    class="fas fa-eye"></i></a>
                        </div>
                        `
                        })
                    }
                    bubbleContent += message.message;
                    bubbleContent += '</div>'
                    var dateClass = message.type_name == 'user' ? 'you' : 'me';
                    var dateContent = '<span class="chat-msg-date ' + dateClass + '">' + message.time + '</span>';
                    var chatItem = '<div class="chat">' + bubbleContent + dateContent + '</div>';
                    $('#chat-item').append(chatItem);
                }
            });
        });

        // Scroll to bottom of chat
        var chatItem = document.getElementById("chat-item");
        chatItem.scrollTop = chatItem.scrollHeight;
    }

    $('#message-form').submit(function (e) {
        e.preventDefault();
        var message = $('#chat-text').val().trim();
        if (message !== '') {
            $.ajax({
                url: messageSend, // Replace with your server endpoint
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $("input[name='_token']").val()
                },
                data: {
                    form_request_id: taskId,
                    message: message,
                    to_user_id: $("#to_user_id").val(),
                    to_user_type: $("#to_user_type").val(),
                },
                success: function (response) {
                    // Clear the input field after successful submission
                    $('#chat-text').val('');
                    // Check if 'Today' key exists in the response
                    if (response.hasOwnProperty('formatted_created_at') && response.formatted_created_at === 'Today') {
                        // Check if 'Today' key exists in the messages
                        if (messages.hasOwnProperty('Today')) {
                            // Append new message to existing 'Today' key
                            messages['Today'].push(response);
                        } else {
                            // Create 'Today' key and add message to it
                            messages['Today'] = [];
                        }
                        // Update chat interface
                        updateChatInterface(messages);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });

    channel.bind('App\\Events\\MessageSent', function (data) {
        var bubbleClass = 'you';
        var bubbleContent = '<div class="bubble ' + bubbleClass + '">' + data.message + '</div>';
        var dateContent = '<span class="chat-msg-date">' + data.time + '</span>';
        var chatItem = '<div class="chat">' + bubbleContent + dateContent + '</div>';
        $('#chat-item').append(chatItem);

        // Scroll to bottom of chat
        var chatItemElement = document.getElementById("chat-item");
        chatItemElement.scrollTop = chatItemElement.scrollHeight;
    });

    $('.draggable').click(function () {
        taskId = this.dataset.taskId;
        $.ajax({
            url: showTaskUrl,
            method: "GET",
            data: {
                task_id: taskId,
            },
            success: function (response) {
                messages = response.messages;
                // console.log(messages);
                $('#chat-item').empty();

                // Check if 'Today' key exists in the response
                if (!messages.hasOwnProperty('Today')) {
                    messages['Today'] = [];
                }

                // Update chat interface
                updateChatInterface(messages);

                // Scroll to bottom of chat
                var chatItem = document.getElementById("chat-item");
                chatItem.scrollTop = chatItem.scrollHeight;
                $('.client-section').addClass('d-none');
                $('.project-section').addClass('d-none');

                // Task Detail
                $('#task-title').html(response.data.title)
                $('#task-slug').html(response.data.slug)
                $('#task-group').val(response.data.form_group_id)
                $('#task-priority').val(response.data.priority)
                $('#task-description').val(response.data.description)
                $('#task-due_date').val(response.data.due_date)
                if (response.data.client_id) {
                    $('#task-client').val(response.data.client_id);
                    $('.client-section').removeClass('d-none');
                }
                if (response.data.project_id) {
                    $('#task-project').val(response.data.project_id);
                    $('.project-section').removeClass('d-none');
                }
                $("#to_user_id").val(response.data.client_id)

                $('#task-assigns .assign-img').remove();
                refreshAssign();

                // Task Detail - Datas
                $('#task-datas-more').addClass('d-none');
                $('#show-less-task-datas').addClass('d-none');
                $('#show-more-task-datas').removeClass('d-none');
                $('#task-datas-more').empty();
                $('#task-datas').empty();
                setDatasList()

                // Task Detail - Subtasks
                $('#task-subtasks tr').remove();
                response.data.subtasks.forEach(function (subtask, index) {
                    setSubtaskRow(subtask);
                    $(`#subtask-${subtask.id}-due_date`).val(subtask.due_date)

                    setSubtaskDateRangeEvent(subtask)

                    refreshSubtaskAssign(subtask.id);
                });

                setSubtaskEvent();

                // Task Detail - Percentage
                if (response.data.subtasks_count > 0) {
                    $('#task-subtasks_percentage').show()
                    $('#task-subtasks_percentage').val(parseInt(response.data
                        .done_subtasks_percentage))
                    $('#task-subtasks_count').show()
                    $('#task-subtasks_count').text(
                        `${response.data.done_subtasks_count}/${response.data.subtasks_count}`
                    )
                } else {
                    $('#task-subtasks_percentage').hide()
                    $('#task-subtasks_count').hide()
                }

                // Task Detail - Files
                $('#task-files tr').remove();
                response.data.files.forEach(function (file, index) {
                    let filename = file.f_value.substring(file.f_value
                        .lastIndexOf('/') + 1);
                    let extension = filename.split('.').pop();
                    let icon = 'file';
                    if (extension == 'xls' || extension == 'xls') {
                        // $icon .= '-xls';
                    } else if (extension == 'csv') {
                        // $icon .= '-csv';
                    } else if (extension == 'jpg' || extension == 'jpeg' ||
                        extension == 'png') {
                        icon = 'file-image';
                    } else if (extension == 'doc' || extension == 'docx' ||
                        extension == 'docs') {
                        // $icon .= '-doc';
                    }
                    let date = new Date(file.created_at);

                    $('#task-files').append(`
                        <tr>
                            <td class="red-td">
                                <a href="${file.f_value}" download><i class="fa-solid fa-${icon}"></i>&nbsp;Upload Reference</a>
                            </td>
                            <td>Only you</td>
                            <td>${date.toLocaleString()}</td>
                        </tr>
                    `);
                });

                // Task Detail - Activities
                $('#task-activities-more').addClass('d-none');
                $('#show-less-task-activities').addClass('d-none');
                $('#show-more-task-activities').removeClass('d-none');
                $('#task-activities-more').empty();
                $('#task-activities').empty();
                setActivityList()
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Task Management - Event
    $('#task-group').change(function () {
        let group = $(this).val();
        updateTask({
            form_group_id: group
        });

        $(`#${taskId}`).appendTo(`#group-${group}`)
    })

    $('#task-priority').change(function () {
        let priority = $(this).val();
        updateTask({
            priority: priority
        });

        if (priority == 1) {
            $(`#task-${taskId}-priority`).html('Urgent');
            $(`#task-${taskId}-priority`).removeAttr('class');
            $(`#task-${taskId}-priority`).attr('class', 'badge bg-danger');
        } else if (priority == 2) {
            $(`#task-${taskId}-priority`).html('High');
            $(`#task-${taskId}-priority`).removeAttr('class');
            $(`#task-${taskId}-priority`).attr('class', 'badge bg-warning text-dark');
        } else {
            $(`#task-${taskId}-priority`).html('Normal');
            $(`#task-${taskId}-priority`).removeAttr('class');
            $(`#task-${taskId}-priority`).attr('class', 'badge bg-secondary');
        }
    })

    $('.assign-item').click(function () {
        let admin_id = this.dataset.adminId;
        updateTask({
            assign: admin_id
        });

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.assign-item').removeClass('active')
            $(this).addClass('active');
        }
        refreshAssign();
    });

    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
    }, function (start, end, label) {
        let due_date = `${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`;
        $(this).val(due_date);
        updateTask({
            due_date: due_date,
        });
    });

    $(`input[name="daterange"]`).on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        updateTask({
            due_date: 'empty',
        });
    });

    $('#task-client').change(function () {
        let client = $(this).val();
        updateTask({
            client_id: client
        });
    })

    $('#task-project').change(function () {
        let project = $(this).val();
        updateTask({
            project_id: project
        });
    })

    $('#task-description').on('input', function () {
        let description = $(this).val();
        updateTask({
            description: description
        });

        $(`#task-${taskId}-description`).html(description);
    })

    $('#show-more-task-datas').click(function () {
        $('#show-more-task-datas').addClass('d-none');
        $('#show-less-task-datas').removeClass('d-none');
        $('.data-question-more').removeClass('d-none');
        $('.data-answer-more').removeClass('d-none');
    })

    $('#show-less-task-datas').click(function () {
        $('#show-less-task-datas').addClass('d-none');
        $('#show-more-task-datas').removeClass('d-none');
        $('.data-question-more').addClass('d-none');
        $('.data-answer-more').addClass('d-none');
    })

    $('#show-more-task-activities').click(function () {
        $('#show-more-task-activities').addClass('d-none');
        $('#show-less-task-activities').removeClass('d-none');
        $('#task-activities-more').removeClass('d-none');
    })

    $('#show-less-task-activities').click(function () {
        $('#show-less-task-activities').addClass('d-none');
        $('#task-activities-more').addClass('d-none');
        $('#show-more-task-activities').removeClass('d-none');
    })

    $('.add-subtask').click(function () {
        storeSubtask(taskId);
    })

    $('#createTaskModal').modal({ backdrop: 'static', keyboard: false });

    $('.create-task').on('click', () => {
        $('#createTaskModal').modal('show');
    })

    $('.new-task-assign-item').click(function () {
        let adminId = this.dataset.adminId;
        refreshNewTaskAssign(adminId);
    });

    $('input[name="due_date"]').daterangepicker({
        opens: 'left',
    }, function (start, end, label) {
        let due_date = `${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`;
        $(this).val(due_date);
    });

    $('.new-task-assign-item').click(function () {
        let admin_id = this.dataset.adminId;

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.assign-item').removeClass('active')
            $(this).addClass('active');
        }
        refreshAssign();
    });

    $('#newTaskType').change(function () {
        const type = $(this).val();
        $('select[name="client_id"]').val('').removeAttr('required');
        $('select[name="client_id"]').addClass('d-none');
        $('select[name="project_id"]').val('').removeAttr('required');
        $('select[name="project_id"]').addClass('d-none');
        if (type === 'INTERNAL') {
            $('select[name="project_id"]').removeClass('d-none').attr('required', 'required');
        }
        if (type === 'CLIENT') {
            $('select[name="client_id"]').removeClass('d-none').attr('required', 'required');
        }
    });

    $('#createProjectModal').modal({ backdrop: 'static', keyboard: false });

    $('.create-project').on('click', () => {
        $('#createProjectModal').modal('show');
    })
});

// Task Management - Initiate Function
function setDatasList() {
    $.ajax({
        url: showTaskUrl,
        method: "GET",
        data: {
            task_id: taskId,
        },
        success: function (response) {
            $('.data-list li').remove();
            if (response.data.form_data.length <= 3) {
                $('#show-more-task-datas').addClass('d-none');
            }
            response.data.form_data.forEach(function (data, index) {
                if (index < 3) {
                    $('.data-list').append(`<p class="data-question"><b>${data.label}</b></p>`)
                    $('.data-list').append(`<p class="data-answer">${data.f_value}</p>`)
                } else {
                    $('.data-list').append(`<p class="data-question data-question-more d-none"><b>${data.label}</b></p>`)
                    $('.data-list').append(`<p class="data-answer data-answer-more d-none">${data.f_value}</p>`)
                }
            })
        }
    });
}

function setActivityList() {
    $.ajax({
        url: showTaskUrl,
        method: "GET",
        data: {
            task_id: taskId,
        },
        success: function (response) {
            $('.activity-list li').remove();
            if (response.data.activities.length <= 3) {
                $('#show-more-task-activities').addClass('d-none');
            }
            response.data.activities.reverse().forEach(function (activity, index) {
                if (index < 25) {
                    const formattedTime = formatRelativeTime(activity.created_at);
                    let text = '';
                    if (activity.field != 'assign') {
                        text = `${activity.admin.name} ${activity.description}`
                    } else {
                        text = `${activity.admin.name} ${activity.description} ${activity.target.name}`
                    }

                    let item = `<div class="task-activities_description row">
                        <div class="col-8">${text}</div>
                        <div class="col-4 task-activity_time">${formattedTime}</div>
                    <div>`
                    if (index < 3) {
                        $('#task-activities').prepend(item)
                    } else {
                        $('#task-activities-more').prepend(item)
                    }
                }
            });
        }
    });
}

// Task Management - Function
function updateTask(data) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: updateTaskUrl,
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            task_id: taskId,
            ...data,
        },
        success: function (response) {
            // console.log(response);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

// Task Management - Refresh
function refreshSubtaskPercentage() {
    $.ajax({
        url: showTaskUrl,
        method: "GET",
        data: {
            task_id: taskId,
        },
        success: function (response) {
            if (response.data.subtasks_count > 0) {
                // Card
                $(`#task-${taskId}-subtask_count`).html(`${response.data.done_subtasks_count}/${response.data.subtasks_count}`)
                $(`#task-${taskId}-subtask_percentage_value`).val(parseInt(response.data.done_subtasks_percentage))
                $(`#task-${taskId}-subtask_percentage`).html(parseInt(response.data.done_subtasks_percentage) + ` %`)
                // Popup
                $('#task-subtasks_percentage').show()
                $('#task-subtasks_percentage').val(parseInt(response.data.done_subtasks_percentage))
                $('#task-subtasks_count').show()
                $('#task-subtasks_count').text(`${response.data.done_subtasks_count}/${response.data.subtasks_count}`)
            } else {
                $('#task-subtasks_percentage').hide()
                $('#task-subtasks_count').hide()
                $(`#task-${taskId}-subtask`).hide();
            }
        }
    });
}

function refreshAssign() {
    $.ajax({
        url: showTaskUrl,
        method: "GET",
        data: {
            task_id: taskId,
        },
        success: function (response) {
            $('.popup-assigned').remove();
            response.data.assigns.forEach(function (assign, index) {
                if (index < 3) {
                    $('#task-assigns').prepend(`<img src="${assign.admin.image ? imagePath + assign.admin.image : defaultAvatar}" id="assign-img-${assign.admin.id}"
                    class="assign-img popup-assigned rounded-circle z-index-${3 - index}" alt="ava" />`);

                    $(`#assign-item-${assign.admin.id}`).addClass('active');
                }
            });

            // Card
            $(`#task-${taskId}-assign`).empty();
            response.data.assigns.forEach(function (assign, index) {
                if (index < 3) {
                    $(`#task-${taskId}-assign`).prepend(`<img src="${assign.admin.image ? imagePath + assign.admin.image : defaultAvatar}"
                    class="assign-img rounded-circle z-index-${3 - index}" alt="ava" />`);
                }
            });
        }
    });
}

// Subtask Management - Initiate
function setSubtaskRow(subtask) {
    $('#task-subtasks').append(`<tr class="subtask" id="subtask-${subtask.id}">
        ${setSubtaskTitleStatus(subtask)}
        ${setSubtaskAssign(subtask)}
        ${setSubtaskPriority(subtask)}
        ${setSubtaskDateRange(subtask)}
        ${isManager ? setSubtaskAction(subtask) : ``}
    </tr>`)
}

function setSubtaskTitleStatus(subtask) {
    return `<td class="name-table">
        <div class="subtask-status" id="subtask-${subtask.id}-status">
            <div class="dropdown d-inline">
                <button class="dropdown-toggle subtask-status_button subtask-${subtask.id}-status_button" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    ${subtask.done == 1 ? '<i class="fa-regular fa-circle-check"></i>' : '<i class="fa-regular fa-circle"></i>'}
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                        <button class="dropdown-item subtask-status-item" data-subtask-id="${subtask.id}" data-status="0"><i class="fa-regular fa-circle"></i> Open</button>
                    </li>
                    <li>
                        <button class="dropdown-item subtask-status-item" data-subtask-id="${subtask.id}" data-status="1"><i class="fa-regular fa-circle-check"></i> Completed</button>
                    </li>
                </ul>
            </div>
        </div>

        <input type="text" class="input-inline subtask-title" id="subtask-${subtask.id}-title" data-subtask-id="${subtask.id}" value="${subtask.title ?? '...'}" ${!isManager ? 'disabled' : ''} />
    </td>`
}

function setSubtaskAssign(subtask) {
    return `<td>
        <div class="assigned-detail" id="subtask-${subtask.id}-assigns">
            ${isManager ? `<div class="dropdown d-inline">
                <button class="dropdown-toggle assign-button" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fa-solid fa-user-plus"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    ${setSubtaskAssignDropdown(subtask.id)}
                </ul>
            </div>` : ''}
        </div>
    </td>`
}

function setSubtaskAssignDropdown(subtaskId) {
    return admins.map(admin => {
        return `<li>
            <button class="dropdown-item subtask-assign-item subtask-${subtaskId}-assign-item" id="subtask-${subtaskId}-assign-item-${admin.id}" data-subtask-id="${subtaskId}" data-admin-id="${admin.id}">
                <img src="${admin.image ? imagePath + admin.image : defaultAvatar}"
                    class="rounded-circle assign-option-img" alt="ava" />
                ${admin.name}
            </button>
        </li>`;
    }).join('');
}

function setSubtaskPriority(subtask) {
    return `<td>
        ${subtask.priority ?
            `<select data-subtask-id="${subtask.id}" name="priority" class="subtask-priority" ${!isManager ? 'disabled' : ''}>
            <option value="1" ${subtask.priority == 1 ? 'selected' : ''}><i class="fas fa-flag"></i>Urgent</option>
            <option value="2" ${subtask.priority == 2 ? 'selected' : ''}><i class="fas fa-flag"></i>High</option>
            <option value="3" ${subtask.priority == 3 ? 'selected' : ''}><i class="fas fa-flag"></i>Normal</option>
        </select>` :
            `<button class="buttons-table">
            <i class="fa-regular fa-flag"></i>
        </button>`}
    </td>`
}

function setSubtaskDateRange(subtask) {
    return `<td>
        <input type="text" name="daterange_${subtask.id}" id="subtask-${subtask.id}-due_date" data-subtask-id="${subtask.id}" class="form-control mb-2 subtask-due_date" ${!isManager ? 'disabled' : ''} />
    </td>`
}

function setSubtaskAction(subtask) {
    return `<td>
        <div class="dropdown">
            <button class="btn btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis"></i>
            </button>
            <ul class="dropdown-menu">
                <li><button class="dropdown-item delete-subtask" data-id="${subtask.id}">Delete</button></li>
            </ul>
        </div>
    </td>`;
}

// Subtask Management - Event
function setSubtaskEvent() {
    $(`.subtask-title`).on('input', function () {
        let subtaskId = this.dataset.subtaskId;
        let title = $(this).val();
        updateSubtask(subtaskId, {
            title: title
        });
    })

    $('.subtask-done').change(function () {
        let subtaskId = this.dataset.subtaskId;
        var done = 1;
        updateSubtask(subtaskId, {
            done: done
        });

        refreshSubtaskPercentage();
    })

    $('.subtask-status-item').click(function () {
        let subtaskId = this.dataset.subtaskId;
        let status = this.dataset.status;
        updateSubtask(subtaskId, {
            done: status
        });

        refreshSubtaskStatus(subtaskId, status);
        refreshSubtaskPercentage();
    });

    $('.subtask-assign-item').click(function () {
        let subtaskId = this.dataset.subtaskId;
        let adminId = this.dataset.adminId;
        updateSubtask(subtaskId, {
            admin_id: adminId
        });

        refreshSubtaskAssign(subtaskId);
    });

    $('.subtask-priority').change(function () {
        let subtaskId = this.dataset.subtaskId;
        let priority = $(this).val();
        updateSubtask(subtaskId, {
            priority: priority
        });
    })

    $('.delete-subtask').click(function () {
        let subtaskId = $(this).data('id');
        destroySubtask(subtaskId)
    });
}

function setSubtaskDateRangeEvent(subtaskId) {
    $(`input[name="daterange_${subtaskId}"]`).daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    }, function (start, end, label) {
        let due_date = `${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`;
        $(`#subtask-${subtaskId}-due_date`).val(due_date);

        updateSubtask(subtaskId, {
            due_date: due_date,
        });
    });

    $(`input[name="daterange_${subtaskId}"]`).on('cancel.daterangepicker', function (ev, picker) {
        $(`#subtask-${subtaskId}-due_date`).val('');

        updateSubtask(subtaskId, {
            due_date: 'empty',
        });
    });
}

// Subtask Managemet - Function
function storeSubtask(taskId) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: storeSubtaskUrl,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            task_id: taskId,
        },
        success: function (response) {
            setSubtaskRow(response.data)

            setSubtaskEvent();
            setSubtaskDateRangeEvent(response.data);

            refreshSubtaskPercentage();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

function updateSubtask(subtaskId, data) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: updateSubtaskUrl,
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            subtask_id: subtaskId,
            ...data,
        },
        success: function (response) {
            // console.log(response);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

function destroySubtask(subtaskId) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: destroySubtaskUrl,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            subtask_id: subtaskId,
        },
        success: function (response) {
            $(`#subtask-${subtaskId}`).remove();
            refreshSubtaskPercentage();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

// Subtask Management - Refresh
function refreshSubtaskStatus(subtaskId, status) {
    $.ajax({
        url: showSubtaskUrl,
        method: "GET",
        data: {
            subtask_id: subtaskId,
        },
        success: function (response) {
            $(`.subtask-${subtaskId}-status_button`).empty();
            let button = null;
            if (status == 1) {
                button = `<i class="fa-regular fa-circle-check"></i>`;
            } else {
                button = `<i class="fa-regular fa-circle"></i>`;
            }
            $(`.subtask-${subtaskId}-status_button`).append(button);
        }
    });
}

function refreshSubtaskAssign(subtaskId) {
    $.ajax({
        url: showSubtaskUrl,
        method: "GET",
        data: {
            subtask_id: subtaskId,
        },
        success: function (response) {
            // Remove active & image
            $(`.subtask-${subtaskId}-assign-item`).removeClass('active');
            $(`.subtask-${subtaskId}-assigned`).remove();
            // Set active & image
            if (response.data.admin) {
                $(`#subtask-${subtaskId}-assigns`).prepend(`<img src="${response.data.admin.image ? imagePath + response.data.admin.image : defaultAvatar}"
                class="assign-img subtask-${subtaskId}-assigned rounded-circle z-index-0" alt="ava" />`);

                $(`#subtask-${subtaskId}-assign-item-${response.data.admin_id}`).addClass('active')
            }
        }
    });
}

// Datetime
function formatRelativeTime(isoTime) {
    const currentTime = new Date();

    const parsedTime = new Date(isoTime);

    const timeDifference = currentTime - parsedTime;

    const minutesAgo = Math.floor(timeDifference / (1000 * 60));
    const hoursAgo = Math.floor(timeDifference / (1000 * 60 * 60));

    if (minutesAgo < 60) {
        return `${minutesAgo === 1 ? '1 minute' : `${minutesAgo} minutes`} ago`;
    } else if (hoursAgo < 24) {
        return `${hoursAgo === 1 ? '1 hour' : `${hoursAgo} hours`} ago`;
    } else {
        const options = { month: 'long', day: 'numeric', year: 'numeric' };
        return parsedTime.toLocaleDateString('en-US', options);
    }
}