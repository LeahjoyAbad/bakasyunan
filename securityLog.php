
<?php
require_once __DIR__ . '/database.php';

class SecurityLog {
    public static function logEvent($event, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO security_logs (event, user_id) VALUES (?, ?)");
        return $stmt->execute([$event, $user_id]);
    }

    public static function getLogs() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM security_logs ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Logs - Bakasyunan Resort</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .search-box {
            text-align: right;
            margin-bottom: 10px;
        }
        input[type="text"] {
            padding: 8px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-shield-alt"></i> Security Logs</h2>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search logs...">
    </div>

    <table id="logsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Event</th>
                <th>User ID</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs) && is_array($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['id'] ?></td>
                        <td><?= htmlspecialchars($log['event']) ?></td>
                        <td><?= $log['user_id'] ?></td>
                        <td><?= $log['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No logs found or an error occurred.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Live search for logs
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#logsTable tbody tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
</body>
</html> 