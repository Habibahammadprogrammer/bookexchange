<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch exchange requests where the logged-in user is either the owner or the requester
$sql = "
    SELECT er.Id, er.BookId, er.OwnerId, er.RequesterId, er.OfferedBookId,
           er.Status, er.Message, er.CreatedAt, er.Rating,
           b1.Title AS RequestedBookTitle, 
           b2.Title AS OfferedBookTitle,
           u1.Name AS OwnerName, 
           u2.Name AS RequesterName
    FROM exchangerequests er
    LEFT JOIN books b1 ON er.BookId = b1.Id
    LEFT JOIN books b2 ON er.OfferedBookId = b2.Id
    LEFT JOIN users u1 ON er.OwnerId = u1.Id
    LEFT JOIN users u2 ON er.RequesterId = u2.Id
    WHERE er.OwnerId = ? OR er.RequesterId = ?
    ORDER BY er.CreatedAt DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdfaf6;
            padding: 20px;
        }
        h2 { color: #333; }
        .notification {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 12px;
        }
        .pending { border-left: 6px solid orange; }
        .accepted { border-left: 6px solid green; }
        .rejected { border-left: 6px solid red; }
        .date { font-size: 0.85rem; color: gray; }
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
    <h2>📢 Notifications</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification 
                <?php 
                    if ($row['Status'] === 'Pending') echo 'pending';
                    elseif ($row['Status'] === 'Accepted') echo 'accepted';
                    elseif ($row['Status'] === 'Rejected') echo 'rejected';
                ?>
            ">
                <p>
                    <?php if ($row['OwnerId'] == $user_id): ?>
                        📚 <strong><?= htmlspecialchars($row['RequesterName']) ?></strong> 
                        requested your book 
                        <em><?= htmlspecialchars($row['RequestedBookTitle']) ?></em>
                        <?php if (!empty($row['OfferedBookTitle'])): ?>
                            and offered <em><?= htmlspecialchars($row['OfferedBookTitle']) ?></em> in exchange.
                        <?php endif; ?>
                    <?php else: ?>
                        ✅ You requested 
                        <em><?= htmlspecialchars($row['RequestedBookTitle']) ?></em> 
                        from <strong><?= htmlspecialchars($row['OwnerName']) ?></strong>.
                    <?php endif; ?>
                </p>
                <p>Status: <strong><?= htmlspecialchars($row['Status']) ?></strong></p>
                <?php if (!empty($row['Message'])): ?>
                    <p>Message: <?= htmlspecialchars($row['Message']) ?></p>
                <?php endif; ?>
                <p class="date">⏰ <?= date("F j, Y, g:i a", strtotime($row['CreatedAt'])) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications yet.</p>
    <?php endif; ?>
    <div class="bottom-btn">
    <a href="home.php" class="btn-back">Back to Home</a>
</div>
</body>
</html>

