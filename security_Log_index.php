<?php
require_once __DIR__ . '/securityLog.php';

$logs = SecurityLog::getLogs();
foreach ($logs as $log) {
    echo "<p>{$log['event']} - {$log['created_at']}</p>";
}
?>
