<?php
require_once 'includes/config.php';

$errors = [];
$success = '';

if(isset($_POST['register'])){
    $Name = trim($_POST['name']);
    $Email = trim($_POST['email']);
    $Phone = trim($_POST['phone']);
    $Password = $_POST['password'];
    if(empty($Name) || empty($Email) || empty($Password)){
        $errors[] = "Name, Email, and Password are required";
    }
    if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format";
    }

    if(empty($errors)){
        $stmt = $conn->prepare("SELECT Id FROM users WHERE Email=?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $errors[] = "Email already registered!";
        } else {
            $PasswordHash = password_hash($Password, PASSWORD_DEFAULT);
            $CreatedAt = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("INSERT INTO users (Name, Email, Phone, PasswordHash, CreatedAt) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $Name, $Email, $Phone, $PasswordHash, $CreatedAt);
            if($stmt->execute()){
                $success = "Registration Successful! You can now log in.";
                header("Location:login.php");
            } else {
                $errors[] = "Database Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <?php
    if(!empty($errors)){
        echo '<ul style="color:red;">';
        foreach($errors as $error){
            echo "<li>$error</li>";
        }
        echo '</ul>';
    }

    if($success){
        echo '<p style="color:green;">'.$success.'</p>';
    }
    ?>

    <form action="" method="POST">
        <input type="text" name="name" placeholder="Full name" value="<?= htmlspecialchars($Name ?? '') ?>" required><br>
        <input type="text" name="email" placeholder="Email" value="<?= htmlspecialchars($Email ?? '') ?>" required><br>
        <input type="text" name="phone" placeholder="Phone Number (optional)" value="<?= htmlspecialchars($Phone ?? '') ?>"><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="register">Sign Up</button>
    </form>
</body>
</html>