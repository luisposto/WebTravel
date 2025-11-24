<?php
// Devuelve quienes están escribiendo en el grupo (últimos segundos)
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

// Por si la tabla no existe aún
$pdo->exec('CREATE TABLE IF NOT EXISTS group_typing (
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    last_typing_at DATETIME NOT NULL,
    PRIMARY KEY (trip_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

// Buscamos usuarios que escribieron en los últimos 7 segundos, excepto el actual
$stmtT = $pdo->prepare('SELECT u.name
    FROM group_typing gt
    JOIN users u ON u.id = gt.user_id
    WHERE gt.trip_id = :tid
      AND gt.user_id <> :uid
      AND gt.last_typing_at >= (NOW() - INTERVAL 7 SECOND)
    ORDER BY gt.last_typing_at DESC
    LIMIT 5');
$stmtT->execute([
    ':tid' => $tripId,
    ':uid' => $user['id'],
]);
$users = $stmtT->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'ok' => true,
    'users' => $users,
]);
