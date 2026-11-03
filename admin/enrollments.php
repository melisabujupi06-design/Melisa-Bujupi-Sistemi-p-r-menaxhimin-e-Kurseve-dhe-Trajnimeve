
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$error = "";
$success = "";

// UPDATE ENROLLMENT STATUS
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $enrollment_id = (int)($_POST["enrollment_id"] ?? 0);
    $status = $_POST["status"] ?? "";

    $allowed_statuses = ["registered", "approved", "completed", "cancelled"];

    if ($enrollment_id > 0 && in_array($status, $allowed_statuses, true)) {
        $stmt = $conn->prepare("
            UPDATE enrollments
            SET status = ?
            WHERE enrollment_id = ?
        ");

        $stmt->bind_param("si", $status, $enrollment_id);

        if ($stmt->execute()) {
            $success = "Enrollment status updated successfully!";
        } else {
            $error = "Could not update enrollment status.";
        }

        $stmt->close();
    }

    if ($error !== "") {
        $_SESSION["enrollment_error"] = $error;
    } else {
        $_SESSION["enrollment_success"] = $success;
    }

    header("Location: enrollments.php");
    exit;
}

// DELETE ENROLLMENT
if (isset($_GET["delete"])) {
    $enrollment_id = (int)$_GET["delete"];

    $stmt = $conn->prepare("
        DELETE FROM enrollments
        WHERE enrollment_id = ?
    ");

    $stmt->bind_param("i", $enrollment_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION["enrollment_success"] = "Enrollment deleted successfully!";
    } else {
        $_SESSION["enrollment_error"] = "Enrollment could not be deleted.";
    }

    $stmt->close();

    header("Location: enrollments.php");
    exit;
}

// DISPLAY MESSAGES
$success = $_SESSION["enrollment_success"] ?? "";
$error = $_SESSION["enrollment_error"] ?? "";

unset($_SESSION["enrollment_success"]);
unset($_SESSION["enrollment_error"]);

// LOAD ENROLLMENTS
$enrollments = $conn->query("
    SELECT
        e.enrollment_id,
        e.enrollment_date,
        e.status,
        u.full_name,
        u.email,
        c.title
    FROM enrollments e
    INNER JOIN users u ON e.user_id = u.user_id
    INNER JOIN courses c ON e.course_id = c.course_id
    ORDER BY e.enrollment_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Enrollments | SkillCRAFT</title>

    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/admin-courses.css">
    <link rel="stylesheet" href="../assets/admin-enrollments.css">
</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">

        <a href="../index.php" class="sidebar-logo">
            Skill<span>CRAFT.</span>
        </a>

        <h4>ADMIN MENU</h4>

        <a href="dashboard.php" class="menu-link">Dashboard</a>
        <a href="courses.php" class="menu-link">Manage Courses</a>
        <a href="enrollments.php" class="menu-link active">Enrollments</a>
        <a href="messages.php" class="menu-link">Contact Messages</a>

        <div class="logout-link">
            <a href="logout.php" class="menu-link">Log Out</a>
        </div>

    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h1>Enrollments</h1>
            <p>View and manage student course registrations.</p>
        </div>

        <?php if ($success !== ""): ?>
            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="table-container">

            <h2>All Enrollments</h2>

            <div class="table-scroll">

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Enrollment Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($enrollments && $enrollments->num_rows > 0): ?>

                        <?php while ($row = $enrollments->fetch_assoc()): ?>

                            <tr>
                                <td><?= (int)$row["enrollment_id"] ?></td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row["full_name"]) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row["email"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row["title"]) ?>
                                </td>

                                <td>
                                    <?= date("d.m.Y", strtotime($row["enrollment_date"])) ?>
                                </td>

                                <td>
                                    <form method="POST" action="enrollments.php" class="enrollment-status-form">

                                        <input type="hidden" name="enrollment_id"
                                               value="<?= (int)$row["enrollment_id"] ?>">

                                        <select name="status">
                                            <option value="registered"
                                                <?= $row["status"] === "registered" ? "selected" : "" ?>>
                                                Registered
                                            </option>

                                            <option value="approved"
                                                <?= $row["status"] === "approved" ? "selected" : "" ?>>
                                                Approved
                                            </option>

                                            <option value="completed"
                                                <?= $row["status"] === "completed" ? "selected" : "" ?>>
                                                Completed
                                            </option>

                                            <option value="cancelled"
                                                <?= $row["status"] === "cancelled" ? "selected" : "" ?>>
                                                Cancelled
                                            </option>
                                        </select>

                                        <button type="submit" name="update_status"
                                                class="save-status-btn">
                                            Save
                                        </button>

                                    </form>
                                </td>

                                <td>
                                    <a
                                        class="delete-btn"
                                        href="enrollments.php?delete=<?= (int)$row["enrollment_id"] ?>"
                                        onclick="return confirm('Are you sure you want to delete this enrollment?');"
                                    >Delete</a>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7">No enrollments found.</td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>
