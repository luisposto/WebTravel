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
      <form method="post">
        <input type="hidden" name="action" value="update_profile"/>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label small"><?=html($t['name'])?></label>
              <input name="name" class="form-control form-control-sm"
                     placeholder="<?=html($t['name'])?>"
                     value="<?=html($user['name'] ?? '')?>" />
            </div>
            <div class="mb-3">
              <label class="form-label small"><?=html($t['country'])?></label>
              <input name="country" class="form-control form-control-sm"
                     placeholder="<?=html($t['country'])?>"
                     value="<?=html($user['country'] ?? '')?>" />
            </div>
            <div class="mb-3">
              <label class="form-label small"><?=html($t['email'] ?? 'Email')?></label>
              <input type="email" name="email" class="form-control form-control-sm"
                     placeholder="tu@email.com"
                     value="<?=html($user['email'] ?? '')?>" />
            </div>
            <div class="mb-3">
              <label class="form-label small"><?=html($t['languages'])?></label>
              <input name="languages" class="form-control form-control-sm"
                     placeholder="<?=html($t['languages'])?> (coma separada)"
                     value="<?=html(implode(', ', $langs))?>" />
            </div>
            <div class="mb-3">
              <label class="form-label small"><?=html($t['bio'] ?? 'Bio')?></label>
              <textarea name="bio" rows="3" class="form-control form-control-sm" placeholder="<?=html($t['bioPlaceholder'] ?? 'Contá un poco sobre vos')?>"><?=html($user['bio'] ?? '')?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label small"><?=html($t['newPassword'] ?? 'Nueva contraseña')?></label>
              <input type="password" name="new_password" class="form-control form-control-sm"
                     placeholder="••••••••" />
              <div class="form-text small">
                <?=html($t['newPasswordHelp'] ?? 'Dejá este campo vacío si no querés cambiar tu contraseña.')?>
              </div>
            </div>
            <button class="btn btn-warning btn-sm text-dark fw-semibold" type="submit">
              <?=html($t['saveProfile'] ?? ($t['save'] ?? 'Guardar cambios'))?>
            </button>
          </div>
<class="form-control form-control-sm"
                        placeholder="<?=html($t['bioPlaceholder'] ?? 'Contá un poco sobre vos')?>"><?=html($user['bio'] ?? '')?></textarea>
            </div>
            <button class="btn btn-warning btn-sm text-dark fw-semibold" type="submit">
              <?=html($t['saveProfile'] ?? ($t['save'] ?? 'Guardar cambios'))?>
            </button>
          </div>

          <div class="col-md-6">
            <div class="border rounded-3 p-3 bg-warning-subtle h-100">
              <div class="small text-muted mb-2"><?=html($t['preview'] ?? 'Vista previa del perfil')?></div>
              <div class="d-flex align-items-center gap-3 mb-2">
                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center bg-opacity-75"
                     style="width: 3rem; height: 3rem;">
                  <span class="fw-semibold text-dark">
                    <?=html(mb_substr($user['name'] ?? '', 0, 1))?>
                  </span>
                </div>
                <div>
                  <div class="fw-medium"><?=html($user['name'] ?? '')?></div>
                  <div class="small text-muted">
                    <?=html($user['country'] ?? '')?>
                    <?php if (!empty($langs)): ?>
                      • <?=html(implode(', ', $langs))?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <p class="small mb-0"><?=html($user['bio'] ?? '')?></p>
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
      </form>
    </div>
  </div>
</section>
    <?php
}
