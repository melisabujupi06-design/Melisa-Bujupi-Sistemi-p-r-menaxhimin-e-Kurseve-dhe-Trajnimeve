
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$success = "";
$error = "";

// MARK MESSAGE AS READ OR UNREAD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["toggle_read"])) {
    $message_id = (int)($_POST["message_id"] ?? 0);
    $is_read = (int)($_POST["is_read"] ?? 0);

    if ($message_id > 0 && in_array($is_read, [0, 1], true)) {
        $stmt = $conn->prepare("
            UPDATE contact_messages
            SET is_read = ?
            WHERE message_id = ?
        ");

        $stmt->bind_param("ii", $is_read, $message_id);

        if ($stmt->execute()) {
            $_SESSION["message_success"] = "Message status updated successfully!";
        } else {
            $_SESSION["message_error"] = "Could not update message.";
        }

        $stmt->close();
    }

    header("Location: messages.php");
    exit;
}

// DELETE MESSAGE
if (isset($_GET["delete"])) {
    $message_id = (int)$_GET["delete"];

    $stmt = $conn->prepare("
        DELETE FROM contact_messages
        WHERE message_id = ?
    ");

    $stmt->bind_param("i", $message_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION["message_success"] = "Message deleted successfully!";
    } else {
        $_SESSION["message_error"] = "Message could not be deleted.";
    }

    $stmt->close();

    header("Location: messages.php");
    exit;
}

// DISPLAY MESSAGES
$success = $_SESSION["message_success"] ?? "";
$error = $_SESSION["message_error"] ?? "";

unset($_SESSION["message_success"]);
unset($_SESSION["message_error"]);

// LOAD CONTACT MESSAGES
$messages = $conn->query("
    SELECT message_id, sender_name, sender_email,
           subject, message, is_read, created_at
    FROM contact_messages
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Messages | SkillCRAFT</title>

    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/admin-courses.css">
    <link rel="stylesheet" href="../assets/admin-messages.css">
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
        <a href="enrollments.php" class="menu-link">Enrollments</a>
        <a href="messages.php" class="menu-link active">Contact Messages</a>

        <div class="logout-link">
            <a href="logout.php" class="menu-link">Log Out</a>
        </div>

    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h1>Contact Messages</h1>
            <p>View and manage messages received from website visitors.</p>
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

            <h2>All Messages</h2>

            <div class="table-scroll">

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sender</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($messages && $messages->num_rows > 0): ?>

                        <?php while ($row = $messages->fetch_assoc()): ?>

                            <tr>
                                <td><?= (int)$row["message_id"] ?></td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row["sender_name"]) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row["sender_email"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row["subject"]) ?>
                                </td>

                                <td class="message-content">
                                    <?= nl2br(htmlspecialchars($row["message"])) ?>
                                </td>

                                <td>
                                    <?= date("d.m.Y H:i", strtotime($row["created_at"])) ?>
                                </td>

                                <td>
                                    <span class="message-status <?= $row["is_read"] ? "read" : "unread" ?>">
                                        <?= $row["is_read"] ? "Read" : "Unread" ?>
                                    </span>

                                    <form method="POST" action="messages.php" class="read-form">

                                        <input type="hidden" name="message_id"
                                               value="<?= (int)$row["message_id"] ?>">

                                        <input type="hidden" name="is_read"
                                               value="<?= $row["is_read"] ? 0 : 1 ?>">

                                        <button type="submit" name="toggle_read" class="read-btn">
                                            <?= $row["is_read"] ? "Mark Unread" : "Mark Read" ?>
                                        </button>

                                    </form>
                                </td>

                                <td>
                                    <a
                                        class="delete-btn"
                                        href="messages.php?delete=<?= (int)$row["message_id"] ?>"
                                        onclick="return confirm('Are you sure you want to delete this message?');"
                                    >Delete</a>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8">No messages found.</td>
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
