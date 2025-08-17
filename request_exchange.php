<?php
session_start();
require_once('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to request a book.");
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';


if (!isset($_GET['book_id'])) {
    die("No book specified.");
}

$book_id = intval($_GET['book_id']);

// Fetch the specific book
$stmt = $conn->prepare("
    SELECT books.*, users.Name AS OwnerName 
    FROM books 
    LEFT JOIN users ON books.OwnerId = users.Id 
    WHERE books.Id = ? 
    LIMIT 1
");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['book_id'], $_POST['owner_id'])) {
        $error = "Invalid request.";
    } else {
        $book_id_post = intval($_POST['book_id']);
        $owner_id_post = intval($_POST['owner_id']);
        $message = trim($_POST['message'] ?? '');

        // Prevent requesting your own book
        if ($owner_id_post == $user_id) {
            $error = "You cannot request your own book.";
        } else {
            // Optional: check for duplicate pending requests
            $check = $conn->prepare("SELECT * FROM exchangerequests WHERE BookId=? AND RequesterId=? AND Status='Pending'");
            $check->bind_param("ii", $book_id_post, $user_id);
            $check->execute();
            $check_result = $check->get_result();
            if ($check_result->num_rows > 0) {
                $error = "You have already requested this book.";
            } else {
                // Insert exchange request
                $insert = $conn->prepare("
                    INSERT INTO exchangerequests 
                        (BookId, OwnerId, RequesterId, OfferedBookId, Status, Message, CreatedAt, Rating)
                    VALUES (?, ?, ?, NULL, 'Pending', ?, NOW(), NULL)
                ");
                $insert->bind_param("iiis", $book_id_post, $owner_id_post, $user_id, $message);

                if ($insert->execute()) {
                    $request_id = $conn->insert_id;

                    // Insert thread
                    $thread = $conn->prepare("INSERT INTO threads (RequestId, CreatedAt) VALUES (?, NOW())");
                    $thread->bind_param("i", $request_id);
                    $thread->execute();

                    header("Location:inventory.php");
                } else {
                    $error = "Failed to send request: " . $insert->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Exchange | Literary Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F6F3;
            color: #2C2C2C;
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            opacity: 0;
            animation: fadeInDown 0.8s ease-out forwards;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 1rem;
            color: #E8A87C;
            font-weight: 400;
            opacity: 0.9;
        }

        .exchange-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 8px 30px rgba(44, 44, 44, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: slideInFade 0.8s ease-out 0.4s forwards;
        }

        .exchange-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #E8A87C, #F2C5A1);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .exchange-card:hover::before {
            opacity: 1;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 500;
            color: #2C2C2C;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.3;
        }

        .book-highlight {
            color: #E8A87C;
            font-weight: 600;
        }

        .exchange-form {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #2C2C2C;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-textarea {
            width: 100%;
            min-height: 120px;
            padding: 1rem;
            border: 2px solid #E8E5E0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            color: #2C2C2C;
            background: #FEFEFE;
            transition: all 0.3s ease;
            resize: vertical;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #E8A87C;
            box-shadow: 0 0 0 3px rgba(232, 168, 124, 0.1);
            background: white;
        }

        .form-textarea::placeholder {
            color: #999;
            font-style: italic;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.9rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 150px;
            justify-content: center;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #E8A87C;
            color: white;
            border: 2px solid #E8A87C;
            box-shadow: 0 4px 15px rgba(232, 168, 124, 0.2);
        }

        .btn-primary:hover {
            background: #d69660;
            border-color: #d69660;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(232, 168, 124, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #2C2C2C;
            border: 2px solid #E8A87C;
        }

        .btn-secondary:hover {
            background: #E8A87C;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(232, 168, 124, 0.2);
        }

        /* زر Back بلون #F2C5A1 */
        .btn-back {
            background: #F2C5A1;
            color: #2C2C2C;
            border: 2px solid #F2C5A1;
        }

        .btn-back:hover {
            background: #E8A87C;
            border-color: #E8A87C;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(232, 168, 124, 0.3);
        }

        .restriction-message {
            background: rgba(244, 67, 54, 0.05);
            border: 2px solid rgba(244, 67, 54, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            color: #C62828;
            font-weight: 500;
        }

        .restriction-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .back-section {
            margin-top: 2rem;
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(232, 168, 124, 0.2);
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.8s forwards;
        }

        .book-info {
            background: rgba(232, 168, 124, 0.05);
            border: 1px solid rgba(232, 168, 124, 0.2);
            border-radius: 10px;
            padding: 1.2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .book-icon {
            font-size: 2.5rem;
            margin-bottom: 0.8rem;
            display: block;
            opacity: 0.8;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: #2C2C2C;
            margin-bottom: 0.3rem;
        }

        .book-subtitle {
            color: #666;
            font-size: 0.95rem;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInFade {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @media (max-width: 768px) {
            .container { padding: 0 0.5rem; }
            .exchange-card { padding: 2rem; }
            .page-title { font-size: 1.5rem; }
            .button-group { flex-direction: column; }
            .btn { width: 100%; }
            .logo { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <h1 class="logo">Chapter & Verse</h1>
        <p class="subtitle">Where Stories Find New Homes</p>
    </header>

    <div class="exchange-card">
        <h2 class="page-title">
            Request Exchange for <span class="book-highlight">"<?php echo htmlspecialchars($book['Title']); ?>"</span>
        </h2>

        <div class="book-info">
            <span class="book-icon">📖</span>
            <div class="book-title"><?php echo htmlspecialchars($book['Title']); ?></div>
            <div class="book-subtitle">Available for exchange</div>
            <div class="book-owner">Owner: <?php echo htmlspecialchars($book['OwnerName']); ?></div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['user_id'] != $book['OwnerId']): ?> 
            <form method="POST" action="" class="exchange-form" id="exchangeForm"> 
                <input type="hidden" name="book_id" value="<?php echo $book['Id']; ?>"> 
                <input type="hidden" name="owner_id" value="<?php echo $book['OwnerId']; ?>"> 

                <div class="form-group"> 
                    <label for="message" class="form-label">Message to Owner (Optional)</label> 
                    <textarea  
                        id="message"  
                        name="message"  
                        class="form-textarea"  
                        placeholder="Share why you're interested in this book or tell the owner about yourself..." 
                        rows="4" 
                    ></textarea> 
                </div> 

                <div class="button-group"> 
                    <button type="submit" class="btn btn-primary" id="submitBtn"> 
                        📚 Request Exchange 
                    </button> 
                </div> 
            </form> 
        <?php else: ?> 
            <div class="restriction-message"> 
                <span class="restriction-icon">🚫</span> 
                <strong>You cannot request your own book.</strong><br> 
                This book belongs to you and is already in your library. 
            </div> 
        <?php endif; ?> 

        <div class="back-section"> 
            <a href="inventory.php" class="btn btn-back">← Back to Inventory</a> 
        </div>
    </div>
</div>
    <script>
        // Add loading state to form submission
        document.getElementById('exchangeForm')?.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Sending Request...';
            }
        });

        // Auto-resize textarea
        const textarea = document.getElementById('message');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.max(120, this.scrollHeight) + 'px';
            });
        }

        // Focus textarea on load
        window.addEventListener('load', function() {
            if (textarea) textarea.focus();
        });
    </script>
</body>
</html>
