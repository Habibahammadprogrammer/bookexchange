<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Start a chat with a selected user
$start_with = $_GET['start_with'] ?? 0;
$selected_thread_id = 0;

if ($start_with) {
    // Check if thread already exists
    $check_thread = $conn->query("
        SELECT Id
        FROM chat_threads
        WHERE (User1Id = $user_id AND User2Id = $start_with)
           OR (User1Id = $start_with AND User2Id = $user_id)
        LIMIT 1
    ");

    if ($check_thread->num_rows > 0) {
        $thread = $check_thread->fetch_assoc();
        $selected_thread_id = $thread['Id'];
    } else {
        // Create new thread
        $conn->query("
            INSERT INTO chat_threads (User1Id, User2Id) VALUES ($user_id, $start_with)
        ");
        $selected_thread_id = $conn->insert_id;
    }
}

// Send a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['body'])) {
    $thread_id = intval($_POST['thread_id']);
    $body = $conn->real_escape_string($_POST['body']);

    $conn->query("
        INSERT INTO messages (ThreadId, SenderId, Body, CreatedAt)
        VALUES ($thread_id, $user_id, '$body', NOW())
    ");

    $selected_thread_id = $thread_id;
}

// Fetch all users except logged-in user
$users = $conn->query("
    SELECT Id, Name
    FROM users
    WHERE Id != $user_id
    ORDER BY Name ASC
");

// Fetch messages for the selected thread
$messages = [];
if ($selected_thread_id) {
    $msg_query = $conn->query("
        SELECT m.*, u.Name
        FROM messages m
        JOIN users u ON m.SenderId = u.Id
        WHERE m.ThreadId = $selected_thread_id
        ORDER BY m.CreatedAt ASC
    ");
    $messages = $msg_query->fetch_all(MYSQLI_ASSOC);
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
<div class="container">
    <!-- Users sidebar -->
    <div class="inbox">
        <h3>Users</h3>
        <ul>
        <?php while ($user = $users->fetch_assoc()): ?>
            <li>
                <a href="messages.php?start_with=<?= $user['Id']; ?>">
                    <?= htmlspecialchars($user['Name']); ?>
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
                            <strong><?= htmlspecialchars($msg['Name']); ?>:</strong>
                            <?= htmlspecialchars($msg['Body']); ?><br>
                            <small><?= $msg['CreatedAt']; ?></small>
                        </p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No messages yet. Start the conversation below!</p>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="thread_id" value="<?= $selected_thread_id; ?>">
                <textarea name="body" placeholder="Type your message..." required></textarea><br>
                <button type="submit">Send</button>
            </form>
        <?php else: ?>
            <p>Select a user to start chatting.</p>
        <?php endif; ?>
    </div>
</div>

<div class="bottom-btn">
    <a href="home.php" class="btn-back">Back to Home</a>
</div>
</body>
</html>
