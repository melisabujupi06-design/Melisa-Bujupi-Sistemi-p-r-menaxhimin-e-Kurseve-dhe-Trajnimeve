
<?php
session_start();
require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT user_id, full_name, password_hash
         FROM users
         WHERE email = ? AND role = 'participant'"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user["password_hash"])) {
            session_regenerate_id(true);
            $_SESSION["student_id"] = $user["user_id"];
            $_SESSION["student_name"] = $user["full_name"];

            header("Location: student_dashboard.php");
            exit;
        }
    }

    $error = "Email or password is incorrect.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | SkillCRAFT</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/register.css">
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <h1>SkillCRAFT</h1>
        <h2>Student Login</h2>
        <p>Sign in to view your courses.</p>

        <?php if ($error): ?>
            <p style="color:#b3261e;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>
            <p style="text-align:right; margin-top:10px;">
    <a href="forgot_password.php">Forgot Password?</a>
</p>

            <button type="submit">LOGIN</button>
        </form>

        <p>Don't have an account?
            <a href="register.php">Register here</a>
        </p>

        <a href="index.php">Back to Home</a>
    </div>
</div>

</body>
</html>
