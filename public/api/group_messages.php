<?php
// Devuelve los mensajes del grupo en HTML dentro de JSON (para AJAX polling)
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

// Verificamos que el usuario pertenezca al viaje (dueño o participante)
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

// Cargamos mensajes
$stmtM = $pdo->prepare('SELECT gm.*, u.name AS user_name
                        FROM group_messages gm
                        JOIN users u ON u.id = gm.user_id
                        WHERE gm.trip_id = :tid
                        ORDER BY gm.created_at ASC');
$stmtM->execute([':tid' => $tripId]);
$messages = $stmtM->fetchAll();

ob_start();
?>
<?php if (!empty($messages)): ?>
  <?php foreach ($messages as $m): ?>
    <div class="small">
      <b><?=html($m['user_name'])?>:</b>
      <?=html($m['message'])?>
      <div class="text-muted tiny">
        <?=html($m['created_at'])?>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p class="text-muted small mb-0">Todavía no hay mensajes en este grupo.</p>
<?php endif; ?>
<?php
$html = ob_get_clean();

echo json_encode([
    'ok'   => true,
    'html' => $html,
]);
