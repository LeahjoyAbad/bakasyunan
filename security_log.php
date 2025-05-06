<?php
function logIncident($message, $user) {
    $file = 'incident_logs.txt';
    $logMessage = date('Y-m-d H:i:s') . " - User: $user - Incident: $message\n";
    file_put_contents($file, $logMessage, FILE_APPEND);
}
?>
