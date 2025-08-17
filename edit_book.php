<?php
session_start();
require_once 'includes/config.php';

$errors = [];
if (!isset($_SESSION['user_id'])) {
    header("Location:login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Book ID.");
}

$book_id = $_GET["id"];
$sql = "SELECT * FROM books WHERE Id=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Book not found.");
}
$book = $result->fetch_assoc();

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $condition = $_POST['condition'];
    $availability = $_POST['availability']; // hidden input

    // Debug: check what is received
    // var_dump($_POST); exit;

    $update_sql = "UPDATE books SET Title=?, Author=?, BookCondition=?, Availability=? WHERE Id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $title, $author, $condition, $availability, $book_id);

    if ($update_stmt->execute()) {
        header("Location: inventory.php");
        exit();
    } else {
        $errors[] = "Error updating book: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - BookExchange</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Assets/EditBook.css">
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f2ed;
            color: #2c2c2c;
            line-height: 1.6;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: #ffffff;
            padding: 60px 40px 40px;
            text-align: center;
            border-bottom: 1px solid #e8e4df;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 1rem;
            color: #6b6b6b;
            font-weight: 300;
            letter-spacing: 0.3px;
        }

        .form-container {
            padding: 60px 40px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 35px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #2c2c2c;
            margin-bottom: 12px;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid #d4cfc7;
            border-radius: 4px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #2c2c2c;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #a69b8f;
            box-shadow: 0 0 0 2px rgba(166, 155, 143, 0.1);
        }

        .form-input::placeholder {
            color: #9b9b9b;
        }

        .form-select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 45px;
        }

        .availability-section {
            margin-top: 20px;
        }

        .availability-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 15px;
        }

.availability-option { 
    padding: 24px 20px; 
    border: 1px solid #d4cfc7; 
    border-radius: 4px; 
    text-align: center; 
    cursor: pointer; 
    transition: all 0.2s ease; 
    background: #ffffff; 
}

/* Hover effect for all options */
.availability-option:hover { 
    border-color: #a69b8f; 
    background: #fafaf8; 
}

/* Selected states with different colors for each availability */
.availability-option.selected.Available { 
    border-color: #28a745; 
    background-color: #d4edda; 
    color: #155724; 
}

.availability-option.selected.Pending { 
    border-color: #ffc107; 
    background-color: #fff3cd; 
    color: #856404; 
}

.availability-option.selected.Sold { 
    border-color: #dc3545; 
    background-color: #f8d7da; 
    color: #721c24; 
}

/* Icon inside each option */
.availability-icon { 
    font-size: 1.2rem; 
    margin-bottom: 8px; 
    display: block; 
}

/* Label text inside each option */
.availability-label { 
    font-weight: 500; 
    font-size: 0.9rem; 
    letter-spacing: 0.2px; 
}


        .btn-container {
            margin-top: 50px;
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .btn {
            padding: 16px 40px;
            border: 1px solid #d4cfc7;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            letter-spacing: 0.3px;
            min-width: 140px;
        }

        .btn-primary {
            background: #2c2c2c;
            color: #ffffff;
            border-color: #2c2c2c;
        }

        .btn-primary:hover {
            background: #1a1a1a;
            border-color: #1a1a1a;
        }

        .btn-secondary {
            background: #ffffff;
            color: #2c2c2c;
            border-color: #d4cfc7;
        }

        .btn-secondary:hover {
            background: #fafaf8;
            border-color: #a69b8f;
        }

        .divider {
            height: 1px;
            background: #e8e4df;
            margin: 40px 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .container {
                border-radius: 4px;
            }
            
            .header {
                padding: 40px 25px 30px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .form-container {
                padding: 40px 25px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 35px;
            }
            
            .availability-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: #2c2c2c;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .section-subtitle {
            font-size: 0.9rem;
            color: #6b6b6b;
            margin-bottom: 25px;
            font-weight: 300;
        }

        /* Loading state */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
   
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Book</h1>
            <p>Update your book listing information</p>
        </div>
        
        <div class="form-container">
            <form method="POST" action="edit_book.php?id=<?php echo $book_id; ?>" id="editBookForm">
                
                <div class="section-title">Book Details</div>
                <div class="section-subtitle">Essential information about your book</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($book['Title']); ?>" 
                               required 
                               placeholder="Enter book title">
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" 
                               id="author" 
                               name="author" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($book['Author']); ?>" 
                               required 
                               placeholder="Enter author name">
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section-title">Condition & Status</div>
                <div class="section-subtitle">Current state and availability of your book</div>

                <div class="form-group">
                    <label for="condition">Book Condition</label>
                    <select name="condition" id="condition" class="form-select" required>
                        <option value="">Select condition</option>
                        <option value="new" <?php if ($book['BookCondition'] === 'new') echo 'selected'; ?>>New</option>
                        <option value="like-new" <?php if ($book['BookCondition'] === 'like-new') echo 'selected'; ?>>Like New</option>
                        <option value="good" <?php if ($book['BookCondition'] === 'good') echo 'selected'; ?>>Good</option>
                        <option value="fair" <?php if ($book['BookCondition'] === 'fair') echo 'selected'; ?>>Fair</option>
                        <option value="poor" <?php if ($book['BookCondition'] === 'poor') echo 'selected'; ?>>Poor</option>
                    </select>
                </div>

<div class="form-group availability-section">
    <label>Availability Status</label>
    <div class="availability-grid">
        <?php
        $statuses = ['Available' => '●', 'Pending' => '○', 'Sold' => '✓'];
        foreach ($statuses as $status => $icon) {
            $isSelected = ($book['Availability'] === $status) ? 'selected' : '';
            echo '<div class="availability-option '.$status.' '.$isSelected.'" 
                      onclick="selectAvailability(\''.$status.'\', this)">
                      <span class="availability-icon">'.$icon.'</span>
                      <div class="availability-label">'.$status.'</div>
                  </div>';
        }
        ?>
    </div>
    <!-- Single hidden input -->
    <input type="hidden" name="availability" id="availability" value="<?php echo htmlspecialchars($book['Availability']); ?>">
</div>

<script>
function selectAvailability(status, element) {
    // Remove 'selected' from all
    document.querySelectorAll('.availability-option').forEach(option => {
        option.classList.remove('selected');
    });
    // Add 'selected' to clicked
    element.classList.add('selected');
    // Update hidden input
    document.getElementById('availability').value = status;
}
</script>
                </div>
                    <input type="hidden" name="availability" id="availability" value="<?php echo htmlspecialchars($book['Availability']); ?>">
                </div>

                <div class="divider"></div>

                <div class="btn-container">
                    <button type="button" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" >
                        Update Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
function selectAvailability(status, element) {
    // Remove 'selected' from all options
    document.querySelectorAll('.availability-option').forEach(option => {
        option.classList.remove('selected');
    });
    // Add 'selected' to clicked option
    element.classList.add('selected');
    // Update hidden input value
    document.getElementById('availability').value = status;
}
</script>
</html>