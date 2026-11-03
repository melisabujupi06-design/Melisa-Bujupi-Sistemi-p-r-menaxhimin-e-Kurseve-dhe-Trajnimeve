<?php
session_start();
require_once "config/db.php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id FROM users
             WHERE email = ? AND role = 'participant'"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $token = bin2hex(random_bytes(32));
            $token_hash = hash("sha256", $token);

            $expires_at = date("Y-m-d H:i:s", time() + 1800);

            $delete = $conn->prepare(
                "DELETE FROM password_resets WHERE user_id = ?"
            );
            $delete->bind_param("i", $user["user_id"]);
            $delete->execute();

            $insert = $conn->prepare(
                "INSERT INTO password_resets
                 (user_id, token_hash, expires_at)
                 VALUES (?, ?, ?)"
            );
            $insert->bind_param(
                "iss",
                $user["user_id"],
                $token_hash,
                $expires_at
            );
            $insert->execute();
        }

        $message = "If this email belongs to an account, a password reset link has been created.";
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SkillCRAFT</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/register.css">
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <h1>SkillCRAFT</h1>
        <h2>Forgot Password?</h2>
        <p>Enter your email address to request a password reset.</p>

        <?php if ($message !== ""): ?>
            <p style="color:<?= $success ? '#47734d' : '#b3261e' ?>;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your registered email"
                required
            >

            <button type="submit">RESET PASSWORD</button>
        </form>

        <p><a href="student_login.php">Back to Student Login</a></p>
    </div>
</div>

</body>
</html>