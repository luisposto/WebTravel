<?php
require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/../app/actions.php';
require __DIR__ . '/../views/layout_header.php';
require __DIR__ . '/../views/layout_footer.php';
require __DIR__ . '/../views/landing.php';
require __DIR__ . '/../views/login.php';
require __DIR__ . '/../views/profile.php';
require __DIR__ . '/../views/trips.php';
require __DIR__ . '/../views/group.php';
require __DIR__ . '/../views/plans.php';
require __DIR__ . '/../views/recommendations.php';
require __DIR__ . '/../views/settings.php';

// Cargamos usuario actual (o demo)
$user = load_current_user();

// Procesamos acciones POST (puede actualizar $user por referencia)
handle_post($APP_SCREENS, $STRINGS, $user);

$screen = $_GET['screen'] ?? 'landing';
if (!in_array($screen, $APP_SCREENS, true)) {
    $screen = 'landing';
}

// Middleware de autenticación: si no está logueado, solo puede ver landing y login
require_auth($user, $screen);

// Permitir que usuarios logueados también vean la landing si tocan INICIO
// (antes se redirigía siempre a trips)
// Render header con navbar
render_header($t, $STRINGS, $locale, $user);

switch ($screen) {
    case 'landing':
        view_landing($t);
        break;

    case 'login':
        view_login($t);
        break;

    case 'profile':
        view_profile($t, $user);
        break;

    case 'trips':
        // Filtros para viajes
        $tripFilters = [
            'city'        => $_GET['trip_city'] ?? '',
            'from'        => $_GET['trip_from'] ?? '',
            'to'          => $_GET['trip_to'] ?? '',
            'only_others' => !empty($_GET['only_others']),
            'only_mine'   => !empty($_GET['only_mine']),
        ];
        $trips = load_trips($user['id'], $tripFilters);
        view_trips($t, $trips, $tripFilters, $user);
        break;

    case 'group':
        view_group($t);
        break;

    case 'plans':
        // Filtros para planes
        $planFilters = [
            'city'            => $_GET['plan_city'] ?? '',
            'from'            => $_GET['plan_from'] ?? '',
            'to'              => $_GET['plan_to'] ?? '',
            'only_with_spots' => !empty($_GET['only_spots']),
            'trip_id'         => isset($_GET['trip_id']) ? (int)$_GET['trip_id'] : null,
        ];
        $plans = load_plans($user['id'], $planFilters);
        view_plans($t, $plans, $planFilters, $user);
        break;

    case 'recommendations':
        $tripIdTips = isset($_GET['trip_id']) ? (int)$_GET['trip_id'] : null;
        $tips = load_tips($tripIdTips);
        view_recommendations($t, $tips);
        break;

    case 'settings':
        view_settings($t, $STRINGS, $locale, $user);
        break;
}

render_footer();
