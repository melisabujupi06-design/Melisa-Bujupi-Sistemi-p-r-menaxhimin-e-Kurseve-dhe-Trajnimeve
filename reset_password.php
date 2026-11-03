
<?php
session_start();
require_once "config/db.php";

$message = "";
$valid_token = false;
$token = trim($_GET["token"] ?? $_POST["token"] ?? "");

if ($token !== "" && preg_match('/^[a-f0-9]{64}$/', $token)) {
    $token_hash = hash("sha256", $token);

    $stmt = $conn->prepare("
        SELECT reset_id, user_id
        FROM password_resets
        WHERE token_hash = ? AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();

    if ($reset) {
        $valid_token = true;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $password = $_POST["password"] ?? "";
            $confirm_password = $_POST["confirm_password"] ?? "";

            if (strlen($password) < 8) {
                $message = "Password must contain at least 8 characters.";
            } elseif ($password !== $confirm_password) {
                $message = "Passwords do not match.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $update = $conn->prepare("
                    UPDATE users
                    SET password_hash = ?
                    WHERE user_id = ? AND role = 'participant'
                ");
                $update->bind_param(
                    "si",
                    $password_hash,
                    $reset["user_id"]
                );

                if ($update->execute() && $update->affected_rows === 1) {
                    $delete = $conn->prepare(
                        "DELETE FROM password_resets WHERE user_id = ?"
                    );
                    $delete->bind_param("i", $reset["user_id"]);
                    $delete->execute();

                    $message = "Your password has been changed successfully.";
                    $valid_token = false;
                } else {
                    $message = "Unable to update the password. Please try again.";
                }
            }
        }
    } else {
        $message = "This reset link is invalid or has expired. Please request a new one.";
    }
} else {
    $message = "Please use a valid password reset link.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SkillCRAFT</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/register.css">
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <h1>SkillCRAFT</h1>
        <h2>Reset Password</h2>

        <?php if ($message !== ""): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($valid_token): ?>
            <p>Choose a new password for your account.</p>

            <form method="POST">
                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($token) ?>"
                >

                <label>New Password</label>
                <input
                    type="password"
                    name="password"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <label>Confirm New Password</label>
                <input
                    type="password"
                    name="confirm_password"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <button type="submit">UPDATE PASSWORD</button>
            </form>
        <?php endif; ?>

        <p><a href="student_login.php">Back to Student Login</a></p>
    </div>
</div>

</body>
</html>
