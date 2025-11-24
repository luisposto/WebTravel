<?php
function view_settings($t, $STRINGS, $locale, $user)
{
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h2 class="h4 fw-semibold mb-3"><?=html($t['settings'])?> &amp; <?=html($t['safety'])?></h2>

      <form method="post" class="mb-3">
        <input type="hidden" name="action" value="update_settings">
        <div class="mb-3">
          <div class="form-check form-switch small">
            <input class="form-check-input" type="checkbox" id="sosSwitch" name="sos_enabled" value="1"
                   <?php if (!empty($user['sos_enabled'])): ?>checked<?php endif; ?>
                   onchange="this.form.submit()">
            <label class="form-check-label" for="sosSwitch"><?=html($t['sos'])?></label>
          </div>
          <div class="form-check form-switch small">
            <input class="form-check-input" type="checkbox" id="shareSwitch" name="share_location" value="1"
            <?php if (!empty($user['share_location'])): ?>checked<?php endif; ?>
            onchange="this.form.submit()">
            <label class="form-check-label" for="shareSwitch"><?=html($t['shareLocation'])?></label>
          </div>
          <div class="form-check form-switch small">
            <input class="form-check-input" type="checkbox"
                   id="showBioSwitch"
                   name="show_bio"
                   value="1"
                   <?php if (!empty($user['show_bio'])): ?>checked<?php endif; ?>
                   onchange="this.form.submit()">
            <label class="form-check-label" for="showBioSwitch">
              <?=html($t['shareBio'] ?? 'Permitir que otros vean mi bio')?>
            </label>
          </div>
        </div>
      </form>


      <form method="post" class="row g-2 mb-3">
        <input type="hidden" name="action" value="set_locale" />
        <div class="col-md-6">
          <label class="form-label small mb-1"><?=html($t['language'] ?? 'Idioma de la interfaz')?></label>
          <select name="lang" class="form-select form-select-sm">
            <?php foreach (array_keys($STRINGS) as $k): ?>
              <option value="<?=$k?>" <?=$k===$locale ? 'selected' : ''?>>
                <?=html($STRINGS[$k]['i18n'] ?? strtoupper($k))?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <button class="btn btn-outline-secondary btn-sm" type="submit">
            <?=html($t['i18n'])?>
          </button>
        </div>
      </form>

      <form method="post" class="mt-3">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="btn btn-outline-danger btn-sm">
          <?=html($t['logout'] ?? 'Cerrar sesión')?>
        </button>
      </form>

      <button type="button" class="btn btn-outline-secondary btn-sm">
        Exportar datos
      </button>
    </div>
  </div>
</section>
    <?php
}
