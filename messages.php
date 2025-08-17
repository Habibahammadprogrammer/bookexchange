<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = intval($_POST['request_id'] ?? 0); // must link to a request
    $message = trim($_POST['body'] ?? '');
    $thread_id = intval($_POST['thread_id'] ?? 0);

    if (!empty($message)) {

        // Create thread if it doesn't exist
        if ($thread_id <= 0) {
            $stmt_thread = $conn->prepare("INSERT INTO threads (RequestId, CreatedAt) VALUES (?, NOW())");
            $stmt_thread->bind_param("i", $request_id);
            if ($stmt_thread->execute()) {
                $thread_id = $conn->insert_id; // new thread ID
            } else {
                $errors[] = "Failed to create a new thread.";
            }
            $stmt_thread->close();
        }

        // Insert message
        if ($thread_id > 0) {
            $stmt_msg = $conn->prepare("INSERT INTO messages (ThreadId, SenderId, Body, CreatedAt) VALUES (?, ?, ?, NOW())");
            $stmt_msg->bind_param("iis", $thread_id, $user_id, $message);
            if ($stmt_msg->execute()) {
                $success = "Message sent!";
            } else {
                $errors[] = "Failed to send message.";
            }
            $stmt_msg->close();
        }

    } else {
        $errors[] = "Please enter a message.";
    }
}


$threads = $conn->query("
    SELECT t.Id, t.RequestId,
           u.Id AS OtherUserId,
           u.Name AS OtherUserName
    FROM threads t
    JOIN messages m ON t.Id = m.ThreadId
    JOIN users u ON u.Id != $user_id AND u.Id = m.SenderId
    GROUP BY t.Id
    ORDER BY t.CreatedAt DESC
");



// Selected thread messages
$selected_thread_id = intval($_GET['thread_id'] ?? 0);
$messages = [];

if ($selected_thread_id) {
    $stmt_msg = $conn->prepare("SELECT m.*, u.Name
                                FROM messages m
                                JOIN users u ON m.SenderId = u.Id
                                WHERE ThreadId = ?
                                ORDER BY CreatedAt ASC");
    $stmt_msg->bind_param("i", $selected_thread_id);
    $stmt_msg->execute();
    $result_msg = $stmt_msg->get_result();
    $messages = $result_msg->fetch_all(MYSQLI_ASSOC);
    $stmt_msg->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages</title>
<style>
body { font-family: Arial; background-color: #fdfaf6; margin:0; padding:40px; color:#4a4a4a;}
h2 { text-align:center; color:#2c2c2c; font-size:2rem; margin-bottom:30px; }
.container { display:flex; gap:20px; }
.inbox { width:30%; background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
.chat { width:70%; background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
.chat-box { border:1px solid #f0e9df; border-radius:8px; padding:12px; height:300px; overflow-y:auto; margin-bottom:12px; background:#fdfaf6; }
textarea { width:100%; border:1px solid #ddd; border-radius:8px; padding:10px; font-size:14px; resize:none; font-family:inherit; }
button { padding:10px 18px; background-color:#d6b98c; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:500; transition:0.3s; }
button:hover { background-color:#c0a672; }
ul { list-style:none; padding-left:0; }
ul li { margin-bottom:8px; }
ul li a { text-decoration:none; color:#4a4a4a; }
ul li a:hover { text-decoration:underline; }
.bottom-btn{
    position:fixed;
    bottom:20px;
    left:50%;
    transform:translateX(-50%);
    z-index:1000;
}
.btn-back {
    display: inline-block;
    padding: 10px 20px;
    background-color: #E8A87C; /* soft orange */
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-back:hover {
    background-color: #d17a5f;
    transform: scale(1.05);
}
</style>
</head>
<body>
<h2>Your Messages</h2>

<div class="container">

    <!-- Threads sidebar -->
    <div class="inbox">
        <h3>Threads</h3>
        <ul>
        <?php while($thread = $threads->fetch_assoc()): ?>
            <li>
                <a href="?thread_id=<?php echo $thread['Id']; ?>">
                   <?php echo $thread['OtherUserName']; ?> 
                    <?php if (!empty($thread['LastSender'])): ?>
                        - Last: <?php echo htmlspecialchars($thread['LastSender']); ?>
                    <?php endif; ?>
                </a>
            </li>
        <?php endwhile; ?>
        </ul>
    </div>

    <!-- Chat window -->
    <div class="chat">
        <?php if ($selected_thread_id): ?>
            <h3>Conversation</h3>
            <div class="chat-box">
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <p>
                            <strong><?php echo htmlspecialchars($msg['Name']); ?>:</strong>
                            <?php echo htmlspecialchars($msg['Body']); ?><br>
                            <small><?php echo $msg['CreatedAt']; ?></small>
                        </p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No messages yet. Start the conversation below!</p>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="thread_id" value="<?php echo $selected_thread_id; ?>">
                <input type="hidden" name="request_id" value="<?php echo $selected_thread_id; ?>"> 
                <textarea name="body" placeholder="Type your message..." required></textarea><br>
                <button type="submit">Send</button>
            </form>
        <?php else: ?>
            <p>Select a thread to start chatting.</p>
        <?php endif; ?>
    </div>
</div>
<div class="bottom-btn">
    <a href="home.php" class="btn-back">Back to Home</a>
</div>
</body>
</html>