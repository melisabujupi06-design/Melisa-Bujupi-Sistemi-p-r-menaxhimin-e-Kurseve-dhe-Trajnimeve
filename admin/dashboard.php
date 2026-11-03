
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

function getCount($conn, $table) {
    $allowedTables = ["courses", "enrollments", "contact_messages"];

    if (!in_array($table, $allowedTables, true)) {
        return 0;
    }

    try {
        $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
        if ($result) {
            return (int)$result->fetch_assoc()["total"];
        }
    } catch (mysqli_sql_exception $e) {
        return 0;
    }

    return 0;
}

$totalCourses = getCount($conn, "courses");
$totalEnrollments = getCount($conn, "enrollments");
$totalMessages = getCount($conn, "contact_messages");

$adminName = htmlspecialchars($_SESSION["admin_name"] ?? "Administrator");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SkillCRAFT</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f0e8;
            font-family: 'DM Sans', sans-serif;
            color: #30251e;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #604632;
            color: #fffdf9;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 27px;
            color: #fffdf9;
            text-decoration: none;
            margin-bottom: 45px;
        }

        .sidebar-logo span {
            color: #d7b99a;
        }

        .sidebar h4 {
            color: #d7b99a;
            font-size: 11px;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .sidebar a.menu-link {
            display: block;
            color: #f5f0e8;
            text-decoration: none;
            padding: 13px 15px;
            margin-bottom: 7px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar a.menu-link:hover,
        .sidebar a.active {
            background: #80634b;
            color: white;
        }

        .logout-link {
            margin-top: auto;
            border-top: 1px solid #80634b;
            padding-top: 20px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            min-width: 0;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
        }

        .topbar h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #604632;
            margin: 0;
        }

        .admin-label {
            background: #fffdf9;
            border: 1px solid #e6ddd1;
            padding: 12px 18px;
            font-size: 14px;
        }

        .welcome-box {
            background: #fffdf9;
            padding: 30px;
            border: 1px solid #e6ddd1;
            margin-bottom: 30px;
        }

        .welcome-box h2 {
            font-family: 'Playfair Display', serif;
            font-size: 25px;
            color: #604632;
            margin: 0 0 10px;
        }

        .welcome-box p {
            color: #887b6e;
            font-size: 14px;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .stat-card {
            background: #fffdf9;
            border: 1px solid #e6ddd1;
            padding: 28px;
        }

        .stat-card p {
            color: #887b6e;
            font-size: 14px;
            margin: 0 0 15px;
        }

        .stat-card h2 {
            color: #604632;
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            margin: 0;
        }

        .quick-section {
            margin-top: 40px;
        }

        .quick-section h2 {
            font-family: 'Playfair Display', serif;
            color: #604632;
            font-size: 25px;
            margin-bottom: 20px;
        }

        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .quick-links a {
            background: #604632;
            color: #fffdf9;
            text-decoration: none;
            padding: 14px 22px;
            font-size: 13px;
        }

        .quick-links a:hover {
            background: #30251e;
        }

        @media(max-width: 850px) {
            .admin-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .sidebar-logo {
                margin-bottom: 20px;
            }

            .logout-link {
                margin-top: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 25px 18px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">

        <a href="../index.php" class="sidebar-logo">
            Skill<span>CRAFT.</span>
        </a>

        <h4>ADMIN MENU</h4>

        <a href="dashboard.php" class="menu-link active">Dashboard</a>
        <a href="courses.php" class="menu-link">Manage Courses</a>
        <a href="enrollments.php" class="menu-link">Enrollments</a>
        <a href="messages.php" class="menu-link">Contact Messages</a>

        <div class="logout-link">
            <a href="logout.php" class="menu-link">Log Out</a>
        </div>

    </aside>

    <main class="main-content">

        <div class="topbar">
            <h1>Dashboard</h1>
            <div class="admin-label">
                Administrator: <strong><?= $adminName ?></strong>
            </div>
        </div>

        <div class="welcome-box">
            <h2>Welcome back, <?= $adminName ?>!</h2>
            <p>Manage your courses, student enrollments and contact messages from one place.</p>
        </div>

        <div class="stats-grid">

            <div class="stat-card">
                <p>Total Courses</p>
                <h2><?= $totalCourses ?></h2>
            </div>

            <div class="stat-card">
                <p>Total Enrollments</p>
                <h2><?= $totalEnrollments ?></h2>
            </div>

            <div class="stat-card">
                <p>Contact Messages</p>
                <h2><?= $totalMessages ?></h2>
            </div>

        </div>

        <div class="quick-section">
            <h2>Quick Access</h2>

            <div class="quick-links">
                <a href="courses.php">Manage Courses</a>
                <a href="enrollments.php">View Enrollments</a>
                <a href="messages.php">View Messages</a>
            </div>
        </div>

    </main>

</div>

</body>
</html>
