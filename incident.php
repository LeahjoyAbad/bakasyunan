<?php
require_once __DIR__ . '/database.php';

class Incident {
    public static function createIncident($title, $description, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO incidents (title, description, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $description, $user_id]);
    }

    public static function getIncidents() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM incidents");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
