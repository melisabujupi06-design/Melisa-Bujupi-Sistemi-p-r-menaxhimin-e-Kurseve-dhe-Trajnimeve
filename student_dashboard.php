
<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit;
}

$student_id = (int)$_SESSION["student_id"];
$student_name = $_SESSION["student_name"] ?? "Student";

$error = "";

$stmt = $conn->prepare("
    SELECT
        c.title,
        c.description,
        c.start_date,
        c.end_date,
        c.price,
        e.enrollment_date,
        e.status
    FROM enrollments e
    INNER JOIN courses c ON e.course_id = c.course_id
    WHERE e.user_id = ?
    ORDER BY e.enrollment_date DESC
");

if (!$stmt) {
    $error = "Could not load your courses. Please try again.";
    $courses = false;
} else {
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $courses = $stmt->get_result();
    } else {
        $error = "Could not load your courses. Please try again.";
        $courses = false;
    }
}

$status_labels = [
    "registered" => "Registered",
    "approved" => "Approved",
    "completed" => "Completed",
    "cancelled" => "Cancelled"
];
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

        <h2>
            Welcome, <?= htmlspecialchars($student_name) ?>!
        </h2>

        <p>Here you can view your registered courses.</p>

        <?php if ($error !== ""): ?>

            <p style="color:#b3261e;">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php elseif ($courses && $courses->num_rows > 0): ?>

            <?php while ($course = $courses->fetch_assoc()): ?>

                <div style="
                    text-align:left;
                    border:1px solid #e6ddd1;
                    padding:20px;
                    margin:20px 0;
                    border-radius:8px;
                ">

                    <h3>
                        <?= htmlspecialchars($course["title"]) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($course["description"] ?? "") ?>
                    </p>

                    <p>
                        <strong>Start date:</strong>
                        <?= $course["start_date"]
                            ? date("d.m.Y", strtotime($course["start_date"]))
                            : "Not specified" ?>
                    </p>

                    <p>
                        <strong>End date:</strong>
                        <?= $course["end_date"]
                            ? date("d.m.Y", strtotime($course["end_date"]))
                            : "Not specified" ?>
                    </p>

                    <p>
                        <strong>Price:</strong>
                        €<?= number_format((float)$course["price"], 2) ?>
                    </p>

                    <p>
                        <strong>Enrollment date:</strong>
                        <?= date("d.m.Y H:i", strtotime($course["enrollment_date"])) ?>
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <?= htmlspecialchars(
                            $status_labels[$course["status"]] ?? $course["status"]
                        ) ?>
                    </p>

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

<?php
if ($stmt) {
    $stmt->close();
}
?>
