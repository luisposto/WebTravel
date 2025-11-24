<?php
// Marca que el usuario está escribiendo en un grupo
require __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$user = load_current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
    exit;
}
$tripId = (int)($_GET['trip_id'] ?? 0);
if ($tripId <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_trip_id']);
    exit;
}

$pdo = db();

// Validar participación
$stmt = $pdo->prepare('SELECT 1 FROM trips WHERE id = :tid AND user_id = :uid
                       UNION
                       SELECT 1 FROM trip_participants WHERE trip_id = :tid AND user_id = :uid
                       LIMIT 1');
$stmt->execute([
    ':tid' => $tripId,
    ':uid' => $user['id'],
]);
if (!$stmt->fetchColumn()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

// Creamos tabla si no existe (simple, para no romper en hosting nuevos)
$pdo->exec('CREATE TABLE IF NOT EXISTS group_typing (
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    last_typing_at DATETIME NOT NULL,
    PRIMARY KEY (trip_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

// Insert/update
$stmtUp = $pdo->prepare('INSERT INTO group_typing (trip_id, user_id, last_typing_at)
    VALUES (:tid, :uid, NOW())
    ON DUPLICATE KEY UPDATE last_typing_at = NOW()');
$stmtUp->execute([
    ':tid' => $tripId,
    ':uid' => $user['id'],
]);

echo json_encode(['ok' => true]);
