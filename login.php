<?php
require_once 'includes/config.php';
$errors = [];
$success = '';
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("Select Id, PasswordHash from users WHERE Email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userID, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $success = "Login Successful";
            session_start();
            $_SESSION["user_id"] = $userID;
            $_SESSION["email"]= $email;
            header("Location: index.php");
            exit();
        } else {
            $errors[]= "Incorrect Password";
        }
    } else {
        $errors[]= "User not found";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php
    if (!empty($errors)) {
        echo '<ul style="color:red;">';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '/ul';
    }
    if($success){
        echo '<p style="color:green;">'.$success.'</p>';
    }
    ?>
    <form action="login.php" method="POST">
          <input type="email" name="email" placeholder="Email" required><br>
          <input type="password" name="password" placeholder="Password" required><br>
          <button type="submit" name="login">Log in!</button>
    </form>
</body>
</html>
<html></html>