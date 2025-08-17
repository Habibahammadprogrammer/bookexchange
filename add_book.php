<?php
session_start();
$errors=[];
$success = "";
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn=trim($_POST['isbn']);
    $condition = trim($_POST['condition']);
    $availability = trim($_POST['availability']);
    $genre_id = intval($_POST['genre']);
    $owner_id = $_SESSION['user_id'];
    $description=trim($_POST['description']);
    if (!empty($title) && !empty($author) && !empty($condition) && !empty($availability) && $genre_id > 0) {
        $stmt = $conn->prepare("INSERT INTO books (Title, Author, BookCondition, Availability, GenreId,OwnerId,Description,ISBN) VALUES (?, ?, ?, ?, ?, ?,?,?,?)");
        $stmt->bind_param("ssssiiss", $title, $author, $condition, $availability, $genre_id, $owner_id, $description, $isbn);

        if ($stmt->execute()) {
            header("Location:inventory.php?success=1");        } else {
            $errors[]= " Error: " . $stmt->error ;
        }
        $stmt->close();
    } else {
        $errors[]= "Please fill in all required fields.";
    }
}

$genres_result = $conn->query("SELECT Id, Name FROM genres ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #F8F6F3;
            --charcoal: #2C2C2C;
            --orange: #E8A87C;
            --dark-orange: #D4956B;
            --light-gray: #E5E5E5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream);
            color: var(--charcoal);
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem;
        }

        .contact-section {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 3rem;
            transition: all 0.3s ease;
        }

        .contact-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .contact-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--charcoal);
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .contact-section h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--orange);
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-row > div {
            display: flex;
            flex-direction: column;
        }

        label {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: var(--charcoal);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        input, select, textarea {
            padding: 0.75rem 1rem;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(232, 168, 124, 0.1);
        }

        input:hover, select:hover, textarea:hover {
            border-color: var(--orange);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        button {
            background: var(--orange);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            align-self: center;
            min-width: 200px;
        }

        button:hover {
            background: var(--dark-orange);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(232, 168, 124, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        /* Single column items */
        .contact-form > div:not(.form-row) {
            display: flex;
            flex-direction: column;
        }

        /* Fade in animation */
        .contact-section {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
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

        /* Responsive design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .contact-section {
                padding: 2rem;
            }

            .contact-section h2 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            button {
                width: 100%;
            }
        }

        /* Additional hover effects for form elements */
        .form-row > div {
            transition: all 0.3s ease;
        }

        .form-row > div:hover {
            transform: translateY(-2px);
        }

        /* Focus styles for better accessibility */
        input:focus, select:focus, textarea:focus {
            transform: translateY(-1px);
        }

        /* Style improvements for select options */
        option {
            padding: 0.5rem;
            background: white;
            color: var(--charcoal);
        }

        /* Loading state for button */
        button:disabled {
            background: var(--light-gray);
            cursor: not-allowed;
            transform: none;
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
    
    <section class="contact-section">
    <h2>Add Book</h2>
    <form class="contact-form" method="POST" action="">
        <div class="form-row">
            <div>
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>
        </div>
        <div>
            <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" required>
        </div>

        <div class="form-row">
            <div>
                <label for="genre">Genre</label>
                <select id="genre" name="genre" required>
                    <option value="">Select a genre</option>
                    <?php while ($genre = $genres_result->fetch_assoc()): ?>
    <option value="<?= $genre['Id'] ?>"><?= htmlspecialchars($genre['Name']) ?></option>
<?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label for="availability">Availability</label>
                <select id="availability" name="availability" required>
                    <option>---Select Availability</option>
                    <option value="Avaliable">Available</option>
                    <option value="Pending">Pending</option>
                    <option value="Sold">Sold</option>
                </select>
            </div>
        </div>
        <div class="form-row">
      <div>
        <label for="condition">Condition</label>
        <select id="condition" name="condition" required>
        <option value="">-- Select Condition --</option>
        <option value="new">New</option>
        <option value="like-new">Like New</option>
        <option value="good">Good</option>
        <option value="fair">Fair</option>
        <option value="poor">Poor</option>
        </select>
      </div>
      </div>
        <label for="description">Description</label>
    <textarea id="description" name="description" rows="5"></textarea>
        <button  action="inventory.php" type="submit">Add Book</button>
    </form>
</section>
<div class="bottom-btn">
    <a href="home.php" class="btn-back">Back to Home</a>
</div>
<script>
    // Add smooth form interactions
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.contact-form');
        const inputs = form.querySelectorAll('input, select, textarea');
        
        // Add focus animations
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Form submission feedback
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button');
            button.textContent = 'Adding Book...';
            button.disabled = true;
            
            // Re-enable after 3 seconds (adjust based on your needs)
            setTimeout(() => {
                button.textContent = 'Add Book';
                button.disabled = false;
            }, 3000);
        });
    });
</script>
</body>
</html>