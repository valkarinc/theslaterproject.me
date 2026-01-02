<?php
// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "website";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $item = $conn->real_escape_string($_POST['item']);
  $issue = $conn->real_escape_string($_POST['issue']);
  $notes = $conn->real_escape_string($_POST['notes']);

  $sql = "INSERT INTO reports (item, issue, notes) VALUES ('$item', '$issue', '$notes')";
  $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Homestead PvE | Missing Items</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body { background:#0c0c0c; color:#fff; font-family: 'Rajdhani', sans-serif; }
    .glass-card { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:15px; padding:2rem; margin:2rem 0; }
    table { color:#fff; }
    th { color:#ff2d2d; }
  </style>
</head>
<body>
<div class="container">
  <div class="glass-card">
    <h1 class="mb-4">Report Missing / Despawning Items</h1>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Item Name</label>
        <input type="text" name="item" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Issue</label>
        <select name="issue" class="form-control" required>
          <option value="missing">Despawns</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Notes (optional)</label>
        <textarea name="notes" class="form-control" rows="3"></textarea>
      </div>
      <button type="submit" class="btn btn-danger">Submit Report</button>
    </form>
  </div>

  <div class="glass-card">
    <h2>All Submitted Reports</h2>
    <table class="table table-dark table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Item</th>
          <th>Issue</th>
          <th>Notes</th>
          <th>Submitted</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM reports ORDER BY created_at DESC");
        while($row = $result->fetch_assoc()) {
          echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['item']}</td>
            <td>{$row['issue']}</td>
            <td>{$row['notes']}</td>
            <td>{$row['created_at']}</td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
