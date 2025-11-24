<?php
session_start();

require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/i18n.php';
require __DIR__ . '/helpers.php';

//
// Locale desde GET / POST / SESSION
//
$locale = $_GET['lang'] ?? ($_POST['lang'] ?? ($_SESSION['lang'] ?? $APP_DEFAULT_LOCALE));
if (!valid_locale($locale, $STRINGS)) {
    $locale = $APP_DEFAULT_LOCALE;
}
$_SESSION['lang'] = $locale;
$t = $STRINGS[$locale];

//
// Helpers de dominio
//
function load_current_user()
{
    $pdo = db();

    // Si ya hay un usuario en sesión, lo buscamos
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            return $user;
        }
    }

    // Si no hay usuario logueado, devolvemos null (sin fallback demo)
    return null;
}


/**
 * Carga viajes, permitiendo ver los de otros usuarios y aplicar filtros.
 *
 * Filtros soportados:
 *  - city         => string (coincidencia parcial)
 *  - from         => fecha YYYY-MM-DD (start_date >= from)
 *  - to           => fecha YYYY-MM-DD (end_date   <= to)
 *  - only_others  => bool  (true = excluir mis propios viajes)
 */
function load_trips($currentUserId, array $filters = [])
{
    $pdo = db();

    $sql = "SELECT t.*,
                   u.name AS owner_name,
                   EXISTS(
                       SELECT 1 FROM trip_participants tp
                       WHERE tp.trip_id = t.id AND tp.user_id = :uid
                   ) AS is_joined,
                   (SELECT COUNT(*) FROM trip_participants tp2 WHERE tp2.trip_id = t.id) AS participants_count
            FROM trips t
            JOIN users u ON u.id = t.user_id
            WHERE 1 = 1";
    $params = [':uid' => $currentUserId];
if (!empty($filters['trip_id'])) {
    $sql .= " AND p.trip_id = :trip_id";
    $params[':trip_id'] = (int)$filters['trip_id'];
}


    // Filtro ciudad
    if (!empty($filters['city'])) {
        $sql .= " AND t.city LIKE :city";
        $params[':city'] = '%' . $filters['city'] . '%';
    }

    // Rango de fechas
    if (!empty($filters['from'])) {
        $sql .= " AND t.start_date >= :from";
        $params[':from'] = $filters['from'];
    }
    if (!empty($filters['to'])) {
        $sql .= " AND t.end_date <= :to";
        $params[':to'] = $filters['to'];
    }

    // Sólo mis viajes (donde soy dueño o participante)
    if (!empty($filters['only_mine'])) {
        $sql .= " AND (t.user_id = :mineUid
                       OR EXISTS(
                           SELECT 1 FROM trip_participants tp3
                           WHERE tp3.trip_id = t.id AND tp3.user_id = :mineUid
                       ))";
        $params[':mineUid'] = $currentUserId;
    }

    // Sólo viajes de otros usuarios (se ignora si está activo 'only_mine')
    if (!empty($filters['only_others']) && empty($filters['only_mine'])) {
        $sql .= " AND t.user_id <> :onlyOthersUid";
        $params[':onlyOthersUid'] = $currentUserId;
    }

    $sql .= " ORDER BY t.start_date";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $trips = $stmt->fetchAll();

    // Enriquecemos con flags simples
    foreach ($trips as &$trip) {
        $trip['is_owner']   = ((int)($trip['user_id'] ?? 0) === (int)$currentUserId);
        $trip['is_joined']  = !empty($trip['is_joined']);
        $trip['participants_count'] = (int)($trip['participants_count'] ?? 0);
    }

    return $trips;
}

/**
 * Carga planes con conteo de participantes y nombres.
 *
 * Filtros soportados:
 *  - city            => string (busca en where_text)
 *  - from            => fecha/hora (YYYY-MM-DD o YYYY-MM-DD HH:MM)
 *  - to              => fecha/hora
 *  - only_with_spots => bool (true = sólo planes con cupos libres)
 */
function load_plans($currentUserId, array $filters = [])
{
    $pdo = db();
    $sql = "SELECT p.*,
                   u.name AS owner_name,
                   (SELECT COUNT(*) FROM plan_participants pp WHERE pp.plan_id = p.id) AS participants_count,
                   EXISTS(
                       SELECT 1 FROM plan_participants pp2
                       WHERE pp2.plan_id = p.id AND pp2.user_id = :uid
                   ) AS is_joined
            FROM plans p
            JOIN users u ON u.id = p.created_by
            WHERE 1 = 1";
    $params = [':uid' => $currentUserId];
if (!empty($filters['trip_id'])) {
    $sql .= " AND p.trip_id = :trip_id";
    $params[':trip_id'] = (int)$filters['trip_id'];
}


    if (!empty($filters['city'])) {
        $sql .= " AND p.where_text LIKE :pcity";
        $params[':pcity'] = '%' . $filters['city'] . '%';
    }
    if (!empty($filters['from'])) {
        $sql .= " AND p.when_at >= :pfrom";
        $params[':pfrom'] = $filters['from'];
    }
    if (!empty($filters['to'])) {
        $sql .= " AND p.when_at <= :pto";
        $params[':pto'] = $filters['to'];
    }

    $sql .= " ORDER BY p.when_at";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $plans = $stmt->fetchAll();

    // Para cada plan, traemos nombres de participantes
    foreach ($plans as &$plan) {
        $stmt2 = $pdo->prepare('SELECT u.name
                                FROM plan_participants pp
                                JOIN users u ON u.id = pp.user_id
                                WHERE pp.plan_id = :pid');
        $stmt2->execute([':pid' => $plan['id']]);
        $plan['participants_names'] = array_column($stmt2->fetchAll(), 'name');

        $plan['is_joined']          = !empty($plan['is_joined']);
        $plan['participants_count'] = (int)($plan['participants_count'] ?? 0);
        $plan['capacity']           = isset($plan['capacity']) ? (int)$plan['capacity'] : 0;
    }

    // Filtro de sólo planes con lugares libres (se aplica en PHP)
    if (!empty($filters['only_with_spots'])) {
        $plans = array_values(array_filter($plans, function ($p) {
            if (empty($p['capacity'])) {
                return true; // sin límite explícito
            }
            return $p['participants_count'] < $p['capacity'];
        }));
    }

    return $plans;
}

function load_tips($tripId = null)
{
    $pdo = db();
    $sql = "SELECT t.*, u.name AS user_name
            FROM tips t
            JOIN users u ON u.id = t.user_id
            WHERE 1 = 1";
    $params = [];

    if (!empty($tripId)) {
        $sql .= " AND t.trip_id = :trip_id";
        $params[':trip_id'] = (int)$tripId;
    }

    $sql .= " ORDER BY t.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
