<?php

function html($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function seats_left($capacity, $participants_count)
{
    return max(0, (int)$capacity - (int)$participants_count);
}

function valid_locale($loc, $STRINGS)
{
    return isset($STRINGS[$loc]) && isset($STRINGS[$loc]['brand']);
}


function is_logged_in($user)
{
    return is_array($user ?? null) && !empty($user['id']);
}

/**
 * Middleware simple de autenticación.
 *
 * - $user: array del usuario actual (o null).
 * - $screen: string con la pantalla actual (landing, login, trips, etc.).
 * - $publicScreens: pantallas accesibles sin login.
 */
function require_auth($user, $screen, array $publicScreens = ['landing', 'login'])
{
    if (!is_logged_in($user) && !in_array($screen, $publicScreens, true)) {
        $lang = $_SESSION['lang'] ?? 'es';
        header('Location: ?screen=login&lang=' . urlencode($lang));
        exit;
    }
}
