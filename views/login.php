<?php
function view_login($t)
{
    // Mostrar mensaje de error de login (si existe)
    $error = $_SESSION['login_error'] ?? null;
    unset($_SESSION['login_error']);
    $lang = $_SESSION['lang'] ?? 'es';
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h2 class="h4 fw-semibold mb-1"><?=html($t['loginTitle'] ?? 'Iniciá sesión')?></h2>
      <p class="text-muted small mb-4">
        <?=html($t['loginSubtitle'] ?? 'Accedé a tus viajes, grupos y planes con tu cuenta.')?>
      </p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2 small">
          <?=html($error)?>
        </div>
      <?php endif; ?>

      <form method="post" action="?screen=login&amp;lang=<?=html($lang)?>" class="row g-4">
        <input type="hidden" name="action" value="login">

        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label small" for="loginEmail">
              <?=html($t['email'] ?? 'Email')?>
            </label>
            <input
              type="email"
              class="form-control form-control-sm"
              id="loginEmail"
              name="email"
              placeholder="tu@email.com"
              required
            >
          </div>

          <div class="mb-3">
            <label class="form-label small" for="loginPassword">
              <?=html($t['password'] ?? 'Contraseña')?>
            </label>
            <input
              type="password"
              class="form-control form-control-sm"
              id="loginPassword"
              name="password"
              placeholder="••••••••"
              required>
          </div>

          <button type="submit" class="btn btn-warning btn-sm text-dark fw-semibold w-100">
            <?=html($t['loginButton'] ?? 'Iniciar sesión')?>
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
    <?php
}
