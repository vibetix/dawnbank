let lastMessageId = 0;

$(document).ready(function () {
    loadUsers(); // Load users when the page loads
    $(document).on("click", "#send-button", sendMessage);

    $(document).on("keypress", "#message-input", function (e) {
        if (e.which === 13 && !e.shiftKey) { // Enter key without Shift
            e.preventDefault();
            sendMessage();
        }
    });

   setInterval(() => {
    let userId = $("#messages-container").data("user-id");
    loadMessages(userId); // always call it, even if userId is undefined
}, 3000);
});

// Load users
function loadUsers() {
    $.ajax({
        url: "../includes/get_users.php",
        type: "GET",
        dataType: "json",
        success: function (users) {
            let userList = $(".chat-list");
            userList.html(""); // Clear existing users

            users.forEach(user => {
                let lastMessage = user.last_message || "No messages yet";
                let time = user.last_time || "";
                let unreadCount = user.unread_count > 0 ? `<div class="unread-badge">${user.unread_count}</div>` : "";
                let daysAgo = user.days_since_last_message === 0 
                    ? "Today" 
                    : user.days_since_last_message === 1 
                        ? "Yesterday" 
                        : `${user.days_since_last_message} days ago`;

                userList.append(`
                    <div class="chat-item" onclick="setActiveChat(${user.user_id}, '${user.full_name}'); toggleSidebar()">
                        <div class="avatar">${getInitials(user.full_name)}</div>
                        <div class="chat-info">
                            <div class="chat-top-row">
                                <div class="chat-name">${user.full_name}</div>
                                <div class="chat-time">${time} <span class="days-ago">${daysAgo}</span></div>
                            </div>
                            <div class="chat-message">
                                <span>${lastMessage}</span>
                                ${unreadCount}
                            </div>
                        </div>
                    </div>
                `);
            });
        }
    });
}

// Set active chat
function setActiveChat(userId, userName) {
    $("#active-chat-name").text(userName);
    $("#messages-container").data("user-id", userId);
    markMessagesAsRead(userId); // ✅ Mark as read when chat is opened
    loadMessages(userId);
}

// Mark messages as read
function markMessagesAsRead(userId) {
    $.ajax({
        url: "../includes/mark_messages_read.php",
        type: "POST",
        data: { user_id: userId },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                loadUsers(); // ✅ Refresh user list to update unread counts
            }
        }
    });
}

// Function to load messages for a specific user
function loadMessages(userId) {
    if (!userId) {
        console.log("userId not available");
        let messageContainer = $("#messages-container");
        messageContainer.html(`
            <div class="error" style="
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: black;
                opacity:0.6;
                border-radius:50px;
                color:  white;
                text-align: center;
                padding: 20px;
                font-size: 16px;
                font-weight: bold;
            "> select a chat to start messaging </div>
        `);
        $(".input-area").hide();
        $(".scroll-down").hide();
        return;
    }

    $.ajax({
        url: "../includes/fetch_admin_messages.php",
        type: "GET",
        data: { user_id: userId },
        dataType: "json",
        success: function (messages) {
            if (messages.error) return;

            $(".input-area").show();
            $(".scroll-down").show();
            $(".error").hide();
            let messageContainer = $("#messages-container");
            let isAtBottom = messageContainer[0].scrollHeight - messageContainer.scrollTop() - messageContainer.outerHeight() < 100;

            messages.forEach(msg => {
                if ($("#message-" + msg.id).length === 0) {
                    appendMessage(msg);
                }
                if (msg.id > lastMessageId) {
                    lastMessageId = msg.id;
                }
            });

            if (isAtBottom) {
                messageContainer.animate({ scrollTop: messageContainer[0].scrollHeight }, 300);
            }
        }
    });
}

function appendMessage(msg) {
    let messageContainer = $("#messages-container");
    let messageClass = msg.sender === "ADMIN" ? "message-mine" : "message-theirs";

    messageContainer.append(`
        <div class="message ${messageClass}" id="message-${msg.id}">
            <div>${msg.message}</div>
            <div class="message-time">${formatTime(msg.created_at)}</div>
        </div>
    `);
}

// Send message
function sendMessage() {
    let messageInput = $("#message-input");
    let messageText = messageInput.val().trim();
    let userId = $("#messages-container").data("user-id");

    if (messageText === "" || !userId) return;
    $(".send-button").prop("disabled", true);

    $.ajax({
        url: "../includes/send_admin_message.php",
        type: "POST",
        data: { user_id: userId, message: messageText },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                messageInput.val("");
                $(".send-button").prop("disabled", false);
                // loadUsers(); // ✅ Refresh user list to update last message and unread
            }
        }
    });
}

// Helpers
function formatTime(timestamp) {
    let date = new Date(timestamp);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
}

function getInitials(name) {
    return name.split(" ").map(n => n.charAt(0)).join("").toUpperCase();
}
