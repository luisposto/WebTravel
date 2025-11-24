<?php
function nav_link($label, $screen)
{
    $lang = $_SESSION['lang'] ?? 'es';
    return '<a class="nav-link small" href="?screen='
        . htmlspecialchars($screen, ENT_QUOTES)
        . '&lang=' . htmlspecialchars($lang, ENT_QUOTES) . '">'
        . html($label) . '</a>';
}

function render_header($t, $STRINGS, $locale, $user)
{
    global $APP_NAME;
    ?>
<!DOCTYPE html>
<html lang="<?=html($locale)?>">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?=html($t['brand'] ?? $APP_NAME)?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #fff7f0;
    }
    .navbar {
      background: linear-gradient(90deg, #ffe0b2, #fff3e0);
    }
    .navbar .nav-link.active,
    .navbar .nav-link:hover {
      color: #bf360c !important;
    }
    .card {
      border-radius: 1rem;
    }
    .badge.rounded-pill {
      background-color: #fff3e0;
      color: #6d4c41;
    }
  </style>
</head>
<body class="bg-light text-dark">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="?screen=landing&lang=<?=html($locale)?>">
        <?=html($t['brand'] ?? $APP_NAME)?>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
              aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <!-- Language selector (hidden form + dropdown) -->
          <li class="nav-item dropdown me-2">
            <form id="langForm" method="post" class="d-none">
              <input type="hidden" name="action" value="set_locale" />
              <input type="hidden" name="lang" id="langInput" value="<?=html($locale)?>" />
            </form>
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center"
                    id="langDropdown" data-bs-toggle="dropdown" type="button" aria-expanded="false">
              <span class="me-1">
                <?=html($STRINGS[$locale]['i18n'] ?? strtoupper($locale))?>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
              <?php foreach (array_keys($STRINGS) as $k): ?>
                <li>
                  <button type="button"
                          class="dropdown-item small lang-option"
                          data-lang="<?=$k?>">
                    <?=html($STRINGS[$k]['i18n'] ?? strtoupper($k))?>
                  </button>
                </li>
              <?php endforeach; ?>
            
</ul>
          </li>

          <?php if (is_logged_in($user)): ?>
          <li class="nav-item">
            <?= nav_link('Inicio', 'landing'); ?>
          </li>
          <li class="nav-item">
            <?= nav_link($t['myTrips'], 'trips'); ?>
          </li>
          <?php endif; ?>

          <?php if (!is_logged_in($user)): ?>
            <!-- Usuario NO logueado: botón "Comenzar" -->
            <li class="nav-item">
              <?= nav_link($t['getStarted'], 'login'); ?>
            </li>
          <?php else: ?>
            <!-- Usuario logueado: avatar + nombre + menú -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle small d-flex align-items-center gap-2"
                 href="#"
                 role="button"
                 data-bs-toggle="dropdown">
                <span class="rounded-circle bg-warning d-inline-flex justify-content-center align-items-center"
                      style="width: 28px; height: 28px; font-size: 0.8rem;">
                  <?=html(strtoupper(substr($user['name'] ?? 'U', 0, 1)))?>
                </span>
                <span><?=html($user['name'] ?? 'Usuario')?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="?screen=profile&lang=<?=html($_SESSION['lang'] ?? 'es')?>">
                    Perfil
                  </a>
                </li>
               <li class="nav-item">
                <a class="dropdown-item" href="?screen=settings&lang=<?=html($_SESSION['lang'] ?? 'es')?>">
                        Ajustes
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="logout">
                    <button class="dropdown-item text-danger" type="submit">
                      <?=html($t['logout'] ?? 'Cerrar sesión')?>
                    </button>
                  </form>
                </li>
              
</ul>
            </li>
          <?php endif; ?>
        
</ul>
      </div>
    </div>
  </nav>

  <main class="container my-4">
    <?php
}
