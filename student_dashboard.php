
<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION["student_id"];

$stmt = $conn->prepare("
    SELECT c.title, c.description, c.start_date,
           c.end_date, c.price, e.enrollment_date, e.status
    FROM enrollments e
    JOIN courses c ON e.course_id = c.course_id
    WHERE e.user_id = ?
    ORDER BY e.enrollment_date DESC
");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | SkillCRAFT</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/register.css">
</head>
<body>

<div class="register-container">
    <div class="register-card" style="max-width:850px;">
        <h1>SkillCRAFT</h1>

        <h2>Welcome, <?= htmlspecialchars($_SESSION["student_name"]) ?>!</h2>
        <p>Here you can view your registered courses.</p>

        <?php if ($courses->num_rows > 0): ?>

            <?php while ($course = $courses->fetch_assoc()): ?>
                <div style="text-align:left; border:1px solid #e6ddd1;
                            padding:20px; margin:20px 0;">
                    <h3><?= htmlspecialchars($course["title"]) ?></h3>

                    <p><?= htmlspecialchars($course["description"]) ?></p>

                    <p><strong>Start date:</strong>
                        <?= htmlspecialchars($course["start_date"]) ?></p>

                    <p><strong>End date:</strong>
                        <?= htmlspecialchars($course["end_date"]) ?></p>

                    <p><strong>Price:</strong>
                        €<?= htmlspecialchars($course["price"]) ?></p>

                    <p><strong>Enrollment date:</strong>
                        <?= htmlspecialchars($course["enrollment_date"]) ?></p>

                    <p><strong>Status:</strong>
                        <?= htmlspecialchars($course["status"]) ?></p>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <p>You are not registered for any courses yet.</p>
        <?php endif; ?>

        <a href="index.php">Back to Home</a> |
        <a href="student_logout.php">Logout</a>
    </div>
</div>

</body>
</html>
