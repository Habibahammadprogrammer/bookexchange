<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $exchange_id = intval($_POST['exchange_id'] ?? 0);

    if ($exchange_id && $action === 'Rate') {
        $rating = intval($_POST['rating'] ?? 0);

        if ($rating >= 1 && $rating <= 5) {
    $stmt = $conn->prepare("
        UPDATE exchangerequests 
        SET Rating=? 
        WHERE Id=? 
        AND (RequesterId=? OR OwnerId=?)
    ");
    $stmt->bind_param("iiii", $rating, $exchange_id, $user_id, $user_id);

    if (!$stmt->execute()) {
        die("Failed to save rating: " . $stmt->error);
    }
}
            if (!$stmt->execute()) {
                die("Failed to save rating: " . $stmt->error);
            }
        }

        header("Location: exchanges.php");
        exit();
    }



$sql = "SELECT e.Id, e.BookId, e.OwnerId, e.RequesterId, e.Status, e.Message, e.Rating,
               b.Title AS BookTitle,
               CASE WHEN e.OwnerId = ? THEN u2.Name ELSE u1.Name END AS OtherUser
        FROM exchangerequests e
        LEFT JOIN books b ON e.BookId = b.Id
        LEFT JOIN users u1 ON e.RequesterId = u1.Id
        LEFT JOIN users u2 ON e.OwnerId = u2.Id
        WHERE e.RequesterId = ? OR e.OwnerId = ?
        ORDER BY e.CreatedAt DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$exchanges = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Exchange | Literary Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F6F3;
            color: #2C2C2C;
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem 1rem;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
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
            font-size: 2.5rem;
            font-weight: 600;
            color: #2C2C2C;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #E8A87C;
            font-weight: 400;
            opacity: 0.9;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 500;
            color: #2C2C2C;
            text-align: center;
            margin-bottom: 2rem;
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.4s forwards;
        }

        .exchange-grid {
            display: grid;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .exchange-card {
            background: white;
            border-radius: 15px;
            padding: 1.8rem;
            box-shadow: 0 4px 20px rgba(44, 44, 44, 0.08);
            transition: all 0.3s ease;
            transform: translateY(0);
            opacity: 0;
            animation: slideInFade 0.6s ease-out forwards;
            position: relative;
            overflow: hidden;
        }

        .exchange-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #E8A87C, #F2C5A1);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .exchange-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 35px rgba(232, 168, 124, 0.15);
        }

        .exchange-card:hover::before {
            transform: scaleX(1);
        }
        .exchange-grid form{
            display: flex;
             align-items: center; 
             gap: 0.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(44, 44, 44, 0.08);
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.6s forwards;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #2C2C2C;
        }

        .empty-state p {
            color: #666;
            font-size: 1.1rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: #2C2C2C;
            margin-bottom: 0.3rem;
            flex: 1;
            min-width: 200px;
        }

        .user-info {
            font-size: 0.95rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #E8A87C, #F2C5A1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .card-body {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1.5rem;
            align-items: center;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
            min-width: 100px;
        }

        .status-pending {
            background: rgba(232, 168, 124, 0.1);
            color: #E8A87C;
            border: 2px solid rgba(232, 168, 124, 0.2);
        }

        .status-accepted {
            background: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
            border: 2px solid rgba(76, 175, 80, 0.2);
        }

        .status-rejected {
            background: rgba(244, 67, 54, 0.1);
            color: #F44336;
            border: 2px solid rgba(244, 67, 54, 0.2);
        }

        .action-buttons {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.7rem 1.4rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .btn-primary {
            background: #E8A87C;
            color: white;
            border: 2px solid #E8A87C;
        }

        .btn-primary:hover {
            background: #d69660;
            border-color: #d69660;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(232, 168, 124, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #2C2C2C;
            border: 2px solid #E8A87C;
        }

        .btn-secondary:hover {
            background: #E8A87C;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(232, 168, 124, 0.2);
        }
        .btn-back {
            background: #F2C5A1;
            color: #2C2C2C;
            border: 2px solid #F2C5A1;
            margin-top: 2rem;
            font-weight: 600;
        }

         .btn-back:hover {
            background: #E8A87C;
            border-color: #E8A87C;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(232, 168, 124, 0.3);
        }



        .rating-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

.rating-select {
    padding: 0.4rem 0.6rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fff;
    color: #333;
    outline: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.rating-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
}


        .rating-display {
            font-size: 1.1rem;
            font-weight: 500;
            color: #E8A87C;
        }

        .back-button-container {
            text-align: center;
            margin-top: 3rem;
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.8s forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideInFade {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 0.5rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-body {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
                min-width: 120px;
            }
            
            .logo {
                font-size: 2rem;
            }
        }

        /* Add staggered animation delays */
        .exchange-card:nth-child(1) { animation-delay: 0.1s; }
        .exchange-card:nth-child(2) { animation-delay: 0.2s; }
        .exchange-card:nth-child(3) { animation-delay: 0.3s; }
        .exchange-card:nth-child(4) { animation-delay: 0.4s; }
        .exchange-card:nth-child(n+5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="logo">Literary Haven</h1>
            <p class="subtitle">Where Stories Find New Homes</p>
        </header>

        <h2 class="page-title">Exchange Requests</h2>

<div class="exchange-grid">
    <?php if (!empty($exchanges)): ?>
        <?php foreach ($exchanges as $row): ?>
            <div class="exchange-card">
                <div class="card-header">
                    <h3 class="book-title"><?php echo htmlspecialchars($row['BookTitle']); ?></h3>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($row['OtherUser']); ?></span>
                    </div>
                    <div class="status-badge status-<?php echo strtolower($row['Status']); ?>">
                        <?php echo htmlspecialchars($row['Status']); ?>
                    </div>
                </div>

                <div class="card-body">
<?php if ($row['Status'] === 'Accepted'): ?>
    <?php if (empty($row['Rating'])): ?>
        <form method="POST" action="exchanges.php">
            <input type="hidden" name="exchange_id" value="<?php echo $row['Id']; ?>">
            <select name="rating" class="rating-select" required>
                <option value="">Rate Experience</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="5">5 ⭐</option>
            </select>
            <button type="submit" name="action" value="Rate" class="btn btn-primary">
                Submit
            </button>
        </form>
    <?php else: ?>
        <div class="rating-display">
            Rated: <?php echo htmlspecialchars($row['Rating']); ?> ⭐
        </div>
    <?php endif; ?>
<?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <h3>No Exchange Requests Yet</h3>
            <p>Your book exchanges will appear here once you start connecting with other readers.</p>
        </div>
    <?php endif; ?>
</div>
<div class="back-button-container">
    <a href="inventory.php" class="btn btn-back">← Back to Inventory</a>
</div>

<script>
    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

</script>
</body>
</html>
