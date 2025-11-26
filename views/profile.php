<?php
function view_profile($t, $user)
{
    $langs = json_decode($user['languages'] ?? '[]', true);
    if (!is_array($langs)) {
        $langs = [];
    }
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h2 class="h4 fw-semibold mb-3"><?=html($t['profile'])?></h2>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile"/>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label small"><?=html($t['name'])?></label>
              <input
                name="name"
                class="form-control form-control-sm"
                placeholder="<?=html($t['name'])?>"
                value="<?=html($user['name'] ?? '')?>"
              />
            </div>

            <div class="mb-3">
              <label class="form-label small"><?=html($t['country'])?></label>
              <input
                name="country"
                class="form-control form-control-sm"
                placeholder="<?=html($t['country'])?>"
                value="<?=html($user['country'] ?? '')?>"
              />
            </div>

            <div class="mb-3">
              <label class="form-label small"><?=html($t['email'] ?? 'Email')?></label>
              <input
                type="email"
                name="email"
                class="form-control form-control-sm"
                placeholder="tu@email.com"
                value="<?=html($user['email'] ?? '')?>"
              />
            </div>

            <div class="mb-3">
              <label class="form-label small"><?=html($t['languages'] ??'Idioma')?></label>
              <input
                name="languages"
                class="form-control form-control-sm"
                placeholder="<?=html($t['languages'])?> (coma separada)"
                value="<?=html(implode(', ', $langs))?>"
              />
            </div>

            <div class="mb-3">
              <label class="form-label small"><?=html($t['bio'] ?? 'Bio')?></label>
              <textarea
                name="bio"
                rows="3"
                class="form-control form-control-sm"
                placeholder="<?=html($t['bioPlaceholder'] ?? 'Contá un poco sobre vos')?>"
              ><?=html($user['bio'] ?? '')?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label small"><?=html($t['newPassword'] ?? 'Nueva contraseña')?></label>
              <input
                type="password"
                name="new_password"
                class="form-control form-control-sm"
                placeholder="••••••••"
              />
              <div class="form-text small">
                <?=html($t['newPasswordHelp'] ?? 'Dejá este campo vacío si no querés cambiar tu contraseña.')?>
              </div>
            </div>

            <button
              class="btn btn-warning btn-sm text-white fw-semibold mt-1"
              type="submit"
            >
              <?=html($t['saveProfile'] ?? ($t['save'] ?? 'Guardar cambios'))?>
            </button>
          </div>

          <div class="col-md-6">
            <div class="border rounded-3 p-3 bg-warning-subtle mb-2">
              <div class="small text-muted mb-2">
                <?=html($t['preview'] ?? '')?>
              </div>
              
        <div class="d-flex align-items-center gap-3 mb-2">
          <div class="position-relative">
          <?php $avatar = $user['avatar'] ?? null; ?>
          <?php if (!empty($avatar)): ?>
            <img
              src="uploads/avatars/<?=html($avatar)?>"
              alt="<?=html($user['name'] ?? 'Usuario')?>"
              class="profile-avatar"
            >
          <?php else: ?>
            <div class="profile-avatar d-flex align-items-center justify-content-center bg-warning-subtle">
              <span class="fw-semibold">
                <?=html(mb_substr($user['name'] ?? 'TM', 0, 2))?>
              </span>
            </div>
          <?php endif; ?>
            <button
              type="button"
              class="btn btn-light btn-sm rounded-circle shadow-sm avatar-edit-btn"
              id="avatarEditBtn"
              title="<?=html($t['changePhoto'] ?? 'Cambiar foto de perfil')?>"
            >
              ✏️
            </button>
          </div>

          <div>
            <div class="fw-semibold">
              <?=html($user['name'] ?? 'Tu nombre')?>
            </div>
            <div class="small text-muted">
              <?=html($user['country'] ?? 'País / ciudad')?>
            </div>
          </div>
        </div>

              </div>

              <?php if (!empty($langs)): ?>
                <div class="mb-2">
                  <?php foreach ($langs as $lg): ?>
                    <span class="badge rounded-pill text-bg-light me-1 mb-1">
                      <?=html($lg)?>
                    </span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="mt-2">
                <div class="small text-muted mb-1">
                  <?=html($t['bio'] ?? 'Bio')?>
                </div>
                <p class="small mb-0">
                  <?=html($user['bio'] ?? 'Acá se va a mostrar tu bio.')?>
                </p>

                <?php if (!empty($user['show_bio'])): ?>
                  <span class="badge bg-success-subtle text-success-emphasis rounded-pill mt-2">
                    Visible para otros viajeros
                  </span>
                <?php else: ?>
                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill mt-2">
                    Solo vos podés ver esta bio
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      
            <div class="mb-3 d-none">
              <input
                type="file"
                name="avatar"
                id="avatarInput"
                class="form-control form-control-sm"
                accept="image/*"
              />
            </div>

</form>

    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var avatarBtn = document.getElementById('avatarEditBtn');
    var avatarInput = document.getElementById('avatarInput');
    if (avatarBtn && avatarInput) {
      avatarBtn.addEventListener('click', function () {
        avatarInput.click();
      });
    }
  });
</script>

</section>
    <?php
}
