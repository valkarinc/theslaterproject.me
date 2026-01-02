<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$host = "localhost";
$user = "root"; // Change if needed
$pass = "";     // Change if you set a password in XAMPP
$dbname = "valk_adminpanel";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle new project submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_project'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    $progress = (int)$_POST['progress'];
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $priority = $conn->real_escape_string($_POST['priority']);
    $owner = "Valk"; // Hardcoded

    $sql = "INSERT INTO projects (title, category, status, progress, start_date, end_date, priority, owner)
            VALUES ('$title', '$category', '$status', '$progress', '$start_date', '$end_date', '$priority', '$owner')";
    $conn->query($sql);
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM projects WHERE id=$id");
}

$projects = $conn->query("SELECT * FROM projects ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Valk Dashboard</title>
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #0a0a0a;
        color: #e5e7eb;
        margin: 0;
        padding: 0;
    }
    header {
        background-color: #111;
        padding: 20px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #00b4ff;
        border-bottom: 2px solid #222;
    }
    .container {
        max-width: 1100px;
        margin: 40px auto;
        background: #1a1a1a;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    h2 {
        text-align: center;
        color: #00b4ff;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #333;
    }
    th {
        background-color: #111;
        color: #00b4ff;
    }
    tr:hover { background-color: #222; }
    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: #fff;
    }
    .delete { background-color: #d9534f; }
    .add { background-color: #0078ff; }
    .status {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
    }
    .OnTrack { background: #1e7e34; }
    .AtRisk { background: #c69500; }
    .OffTrack { background: #a94442; }
    .progress-bar {
        background-color: #333;
        border-radius: 4px;
        height: 10px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background-color: #00b4ff;
        width: 0%;
        transition: width 0.4s ease;
    }
    form {
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }
    input, select {
        padding: 8px;
        border: 1px solid #333;
        border-radius: 5px;
        background-color: #111;
        color: #fff;
    }
</style>
</head>
<body>
<header>Welcome home, Valk</header>

<div class="container">
    <h2>Project Overview</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Progress</th>
            <th>Dates</th>
            <th>Priority</th>
            <th>Owner</th>
            <th>Action</th>
        </tr>
        <?php while($row = $projects->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><span class="status <?= str_replace(' ', '', $row['status']) ?>"><?= $row['status'] ?></span></td>
            <td>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $row['progress'] ?>%;"></div>
                </div>
                <?= $row['progress'] ?>%
            </td>
            <td><?= $row['start_date'] ?> → <?= $row['end_date'] ?></td>
            <td><?= $row['priority'] ?></td>
            <td><?= $row['owner'] ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" class="btn delete">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add New Project</h2>
    <form method="post">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="category" placeholder="Category" required>
        <select name="status">
            <option>On Track</option>
            <option>At Risk</option>
            <option>Off Track</option>
        </select>
        <input type="number" name="progress" placeholder="Progress %" min="0" max="100">
        <input type="date" name="start_date">
        <input type="date" name="end_date">
        <select name="priority">
            <option>Low</option>
            <option>Medium</option>
            <option>High</option>
        </select>
        <input type="submit" name="add_project" value="Add Project" class="btn add">
    </form>
</div>
</body>
</html>
