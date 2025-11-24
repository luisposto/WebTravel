<?php
// Nombre de la app y configuración básica
$APP_NAME = 'TripMatch';
$APP_DEFAULT_LOCALE = 'es';

$APP_SCREENS = [
    'landing',
    'login',
    'profile',
    'trips',
    'group',
    'plans',
    'recommendations',
    'settings',
];

// Configuración de base de datos (ajustá según tu entorno)
define('DB_DSN', 'mysql:host=localhost;dbname=tripmatch;charset=utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', '');
