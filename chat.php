<!-- PHP -->
<?php
include 'database.php'; // Include your database connection
session_start(); // Ensure sessions are started

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    // Redirect to login page if not logged in or no user_id passed
    header('Location: login.php');
    exit;
}

$my_id = $_SESSION['user_id'];
$friend_id = $_GET['user_id'];

// Update unread messages to read upon opening the chat
$update_query = "UPDATE messages SET is_read = TRUE WHERE sender_id = ? AND receiver_id = ? AND is_read = FALSE";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("ii", $friend_id, $my_id);
$update_stmt->execute();
$update_stmt->close();

// Fetch username for the friend
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $friend_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $friend = $result->fetch_assoc();
    $friend_username = $friend['username'];
} else {
    echo "No user found.";
    exit;
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($friend_username); ?></title>
    <!-- CSS -->
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #212121;
        }

        /* Title Header */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 20px 25px;
        }

        .box-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 0 25px 0 25px;
        }

        /* For chats */
        .chat-box {
            padding: 10px;
            margin: 5px 30px;
            color: white;
        }

        .chat-messages {
            height: 300px; 
            overflow-y: auto; 
            border: 1px solid #ccc; 
            margin-bottom: 10px; 
            padding: 5px;
            border-radius: 10px;
        }

        .message-input {
            font-family: "Poppins", sans-serif;
            width: 100%; 
            height: 50px;
            border-radius: 20px;
            padding: 10px;
            margin-bottom: 5px;
        }

        /* Button */
        .send-button {
            width: 150px;
            padding: 7px;
            margin: 4px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-family: "Poppins", sans-serif;
            background-color: rgba(0, 136, 169, 1);
        }

        .send-button:hover {
            background-color: rgba(0, 136, 169, 0.8);
        }

        .back-button {
            font-family: "Poppins", sans-serif;
            padding: 10px;
            margin: 0 0 10px 25px;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            width: 200px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sender {
            color: #7AA3CC; /* Light blue color for usernames and timestamps */
            font-weight: bold;
        }

        .timestamp {
            color: #7AA3CC; /* Same color for timestamp */
        }

    </style>
    </style>
</head>
<body>
    <!-- Header -->
    <?php require 'header.php'; ?>

    <!-- Title page -->
    <h2 class="title-page">Chat with <?php echo htmlspecialchars($friend_username); ?></h2>

    <!-- Back button -->
    <button class="back-button" onclick="window.location.href='friend_list.php'">
        Back
    </button>
    
    <!-- Chat -->
    <div id="chat-box" class="chat-box">
        <div id="messages" class="chat-messages">
            <!-- Messages will be loaded here by JavaScript -->
        </div>

        <textarea id="message-input" class="message-input"></textarea>

        <button onclick="sendMessage()" class="send-button">Send</button>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        function fetchMessages() {
            $.ajax({
                url: 'fetch_messages.php',
                type: 'POST',
                data: { friend_id: '<?php echo $friend_id; ?>' },
                dataType: 'json',
                success: function(messages) {
                    $('#messages').empty();
                    messages.forEach(function(message) {
                        var sender = message.sender === '<?php echo htmlspecialchars($friend_username); ?>' ? 'You' : message.sender;
                        var timestamp = new Date(message.timestamp).toLocaleString(); // Converts date to local string
                        $('#messages').append('<div><strong class="sender">' + sender + '</strong> <span class="timestamp">(' + timestamp + ')</span>: ' + message.message_text + '</div>');
                    });
                    $('#messages').scrollTop($('#messages')[0].scrollHeight);
                }
            });
        }

        function sendMessage() {
            var messageText = $('#message-input').val();
            if (messageText !== '') {
                $.post('send_message.php', {
                    receiver_id: '<?php echo $friend_id; ?>',
                    message_text: messageText
                }, function(response) {
                    $('#message-input').val('');
                    fetchMessages(); // Reload messages
                });
            }
        }

        $(document).ready(function() {
            fetchMessages(); // Initial load
            setInterval(fetchMessages, 5000); // Poll for new messages every 5 seconds
        });
    </script>
</body>
</html>
