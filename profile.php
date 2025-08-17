<?php
require_once 'includes/config.php';
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location:login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user info
$stmt = $conn->prepare("SELECT Name, Email FROM users WHERE Id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = ($result->num_rows > 0) ? $result->fetch_assoc() : ['Name' => 'Unknown', 'Email' => 'unknown@example.com'];

$history_stmt = $conn->prepare("
    SELECT er.Id, b1.Title AS RequestedBook, er.Status, er.CreatedAt, er.Rating
    FROM exchangerequests er
    LEFT JOIN books b1 ON er.BookId = b1.Id
    WHERE er.OwnerId = ? OR er.RequesterId = ?
    ORDER BY er.CreatedAt DESC
");
$history_stmt->bind_param("ii", $user_id, $user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

// Store history in array
$history = [];
if($history_result->num_rows > 0){
    while($row = $history_result->fetch_assoc()){
        $history[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans&display=swap" rel="stylesheet">
<style>
    body {
    margin: 0;
    font-family: 'Open Sans', sans-serif;
    background-color: #fff;
    color: #333;
}

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 40px;
    background-color: #f8f5f2;
    border-bottom: 1px solid #ddd;
    font-family: 'Playfair Display', serif;
}

.logo {
    font-size: 20px;
    font-weight: bold;
}

nav a {
    margin: 0 10px;
    text-decoration: none;
    color: #444;
}

nav a:hover {
    color: #000;
}

.search-login {
    display: flex;
    align-items: center;
}

.search-login input {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

.search-login button {
    margin-left: 5px;
    padding: 5px 10px;
    border: none;
    background: #ddd;
    cursor: pointer;
    border-radius: 3px;
}

.login-btn {
    display:inline-flex;
    background-color: #f4c4a4;
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    border-radius:10px;
    transition:0.3s ease;
    padding:10px 20px;
    align-items:center;
    justify-content: center;
    cursor:pointer;
}
.login-btn:hover{
    background: #ea580c; 
}
/* Profile Section */
.profile-container {
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.profile-card {
    text-align: center;
    max-width: 500px;
    padding: 20px;
    background: #fff;
}

.profile-pic {
    border-radius: 50%;
    margin-bottom: 15px;
}

.email {
    color: #666;
    font-size: 14px;
}

.ratings {
    margin-top: 10px;
}

.stars {
    font-size: 20px;
    color: gold;
}

/* Table */
.exchange-history {
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    border-bottom: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f8f5f2;
}
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
    <!-- Header -->
    <header class="navbar">
        <div class="logo">Chapter & Verse</div>
        <nav>
            <a href="home.php#featured">Popular Books</a>
            <a href="home.php#genres">Book Types</a>
            <a href="home.php#staff-picks">Our Picks</a>
            <a href="home.php#new-arrivals">New Books</a>
            <a href="home.php#about">About Us</a>
        </nav>
        <a href="logout.php" class="login-btn">Sign Out</a>
    </header>

    <!-- Profile Section -->
    <section class="profile-container">
        <div class="profile-card">
            <h2><?php echo htmlspecialchars($user['Name']); ?></h2>
            <p class="email"><?php echo htmlspecialchars($user['Email']); ?></p>

            <div class="ratings">
                <strong>User Ratings:</strong>
                <span class="stars">★★★★☆</span>
            </div>

            <!-- Exchange History -->
            <div class="exchange-history">
                <h3>Exchange History</h3>
                <table>
                    <tr>
                        <th>Requested Book</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Rating</th>
                    </tr>
                    <?php if(!empty($history)): ?>
                        <?php foreach($history as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['RequestedBook']); ?></td>
                                <td><?php echo date("Y-m-d", strtotime($row['CreatedAt'])); ?></td>
                                <td><?php echo htmlspecialchars($row['Status']); ?></td>
                                <td>
                                    <?php if($row['Status'] == 'Completed' && empty($row['Rating'])): ?>
                                        <form action="rate_exchange.php" method="POST">
                                            <input type="hidden" name="exchange_id" value="<?php echo $row['Id']; ?>">
                                            <select name="rating" required>
                                                <option value="">Select</option>
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                                                <?php endfor; ?>
                                            </select>
                                            <textarea name="review" placeholder="Optional review"></textarea>
                                            <button type="submit">Submit</button>
                                        </form>
                                    <?php elseif(!empty($row['Rating'])): ?>
                                        <?php echo $row['Rating']; ?> ★
                                        <?php if(!empty($row['Review'])) echo "<p>".htmlspecialchars($row['Review'])."</p>"; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No exchange history found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </section>
    <div class="bottom-btn">
    <a href="home.php" class="btn-back">Back to Home</a>
</div>
</body>
</html>