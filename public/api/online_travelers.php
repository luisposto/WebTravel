<?php
require __DIR__ . '/../../app/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = db();

    // Ajustá esta consulta a tu esquema real:
    // Se asume tabla users con columnas:
    //  - last_lat, last_lng (float)
    //  - last_seen (datetime)
    //  - country, city, name
    $stmt = $pdo->prepare("
        SELECT
            name,
            country,
            city,
            last_lat AS lat,
            last_lng AS lng
        FROM users
        WHERE last_lat IS NOT NULL
          AND last_lng IS NOT NULL
          AND last_seen >= (NOW() - INTERVAL 30 MINUTE)
        LIMIT 200
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows ?: []);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
