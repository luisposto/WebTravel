<?php
function view_plans($t, $plans, $filters = [], $user = null)
{
    $lang        = $_SESSION['lang'] ?? 'es';
    $cityFilter  = $filters['city']            ?? '';
    $fromFilter  = $filters['from']            ?? '';
    $toFilter    = $filters['to']              ?? '';
    $onlySpots   = !empty($filters['only_with_spots']);
    $tripIdFilter = $filters['trip_id'] ?? null;
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h2 class="h4 fw-semibold mb-0"><?=html($t['plans'] ?? 'Planes')?></h2>
        <span class="badge text-bg-light small">
          <?=html($t['map'] ?? 'Mapa')?> · <?=html($t['group'] ?? 'Grupos')?> · <?=html($t['plans'] ?? 'Planes')?>
        </span>
      </div>

        <?php if ($user): ?>
          <p class="small text-muted mb-3 mb-0">
            <?=html(sprintf($t['helloUser'] ?? 'Hola, %s 👋', $user['name'] ?? ''))?>
          </p>
        <?php endif; ?>


      <!-- Filtros -->
      <form method="get" class="row g-2 mb-4">
        <input type="hidden" name="screen" value="plans" />
        <input type="hidden" name="lang" value="<?=html($lang)?>" />
        <?php if (!empty($tripIdFilter)): ?>
          <input type="hidden" name="trip_id" value="<?=html($tripIdFilter)?>" />
        <?php endif; ?>

        <div class="col-md-3 col-6">
          <label class="form-label small mb-1"><?=html($t['where'] ?? 'Lugar')?></label>
          <input name="plan_city"
                 class="form-control form-control-sm"
                 placeholder="<?=html($t['where'] ?? 'Ciudad / zona')?>"
                 value="<?=html($cityFilter)?>" />
        </div>

        <div class="col-md-2 col-6">
          <label class="form-label small mb-1"><?=html($t['start'] ?? 'Desde')?></label>
          <input type="date"
                 name="plan_from"
                 class="form-control form-control-sm"
                 value="<?=html(substr($fromFilter, 0, 10))?>" />
        </div>

        <div class="col-md-2 col-6">
          <label class="form-label small mb-1"><?=html($t['end'] ?? 'Hasta')?></label>
          <input type="date"
                 name="plan_to"
                 class="form-control form-control-sm"
                 value="<?=html(substr($toFilter, 0, 10))?>" />
        </div>

        <div class="col-md-3 col-6 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1"
                   id="onlySpotsCheck" name="only_spots"
                   <?=$onlySpots ? 'checked' : ''?> />
            <label class="form-check-label small" for="onlySpotsCheck">
              <?=html($t['onlySpots'] ?? 'Sólo planes con lugares libres')?>
            </label>
          </div>
        </div>

        <div class="col-md-2 col-12 d-flex align-items-end gap-2">
          <button class="btn btn-warning btn-sm text-dark fw-semibold" type="submit">
            <?=html($t['search'] ?? 'Filtrar')?>
          </button>
          <a href="?screen=plans&lang=<?=html($lang)?><?=!empty($tripIdFilter) ? '&trip_id=' . urlencode($tripIdFilter) : ''?>"
             class="btn btn-outline-secondary btn-sm">
            <?=html($t['clear'] ?? 'Limpiar')?>
          </a>
        </div>
      </form>

      <div class="row g-4">
        <div class="col-md-7">
          <?php if (empty($plans)): ?>
            <p class="small text-muted mb-0">
              <?=html($t['noPlans'] ?? 'Todavía no hay planes que coincidan con el filtro.')?>
            </p>
          <?php else: ?>
            <?php foreach ($plans as $p):
              $left     = seats_left((int)($p['capacity'] ?? 0), (int)($p['participants_count'] ?? 0));
              $isJoined = !empty($p['is_joined']);
              $isOwner  = isset($p['created_by'], $user['id']) && (int)$p['created_by'] === (int)$user['id'];
            ?>
              <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <div class="fw-medium">
                    <?=html($p['title'])?>
                    <?php if ($isOwner): ?>
                      <span class="badge text-bg-warning text-dark ms-1 small">Tu plan</span>
                    <?php endif; ?>
                  </div>
                  <div class="small text-muted">
                    <?=html($p['participants_count'] ?? 0)?> / <?=html($p['capacity'] ?? 0)?>
                    <?=html($t['travelers'] ?? 'viajeros')?>
                  </div>
                </div>
                <div class="small text-muted mb-1">
                  🗓️ <?=html($p['when_at'] ?? '')?>
                </div>
                <div class="small text-muted mb-1">
                  📍 <?=html($p['where_text'] ?? '')?>
                </div>
                <div class="small text-muted mb-2">
                  <?=$left?> <?=html($t['spotsLeft'] ?? 'lugares libres')?>
                </div>
                <?php if (!empty($p['participants_names'])): ?>
                  <div class="d-flex flex-wrap gap-1 mb-3 small">
                    <?php foreach ($p['participants_names'] as $name): ?>
                      <span class="badge rounded-pill bg-light text-dark border"><?=html($name)?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <form method="post" class="d-flex flex-wrap gap-2">
                  <input type="hidden" name="plan_id" value="<?=html($p['id'])?>" />
                  <?php if ($isOwner): ?>
                    <span class="badge text-bg-light small">
                      <?=html($t['youHost'] ?? 'Sos el anfitrión')?>
                    </span>
                  <?php else: ?>
                    <?php if ($isJoined): ?>
                      <input type="hidden" name="action" value="leave_plan" />
                      <button class="btn btn-outline-secondary btn-sm" type="submit">
                        <?=html($t['leave'] ?? 'Salir del plan')?>
                      </button>
                    <?php else: ?>
                      <input type="hidden" name="action" value="join_plan" />
                      <?php if ($left > 0): ?>
                        <button class="btn btn-warning btn-sm text-dark fw-semibold" type="submit">
                          <?=html($t['join'] ?? 'Unirme')?>
                        </button>
                      <?php else: ?>
                        <button class="btn btn-secondary btn-sm" type="button" disabled>
                          <?=html($t['full'] ?? 'Completo')?>
                        </button>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endif; ?>

                  <button type="button"
                          class="btn btn-outline-secondary btn-sm"
                          onclick="alert('Mini chat del plan (demo)')">
                    Mini chat
                  </button>
                </form>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="col-md-5">
          <div class="border rounded-3 p-3 bg-light-subtle h-100">
            <div class="fw-semibold mb-2">
              ➕ <?=html($t['createPlan'] ?? 'Crear nuevo plan')?>
            </div>
            <form method="post" class="row g-2">
              <input type="hidden" name="action" value="create_plan" />
              <?php if (!empty($tripIdFilter)): ?>
                <input type="hidden" name="trip_id" value="<?=html($tripIdFilter)?>" />
              <?php endif; ?>
              <div class="col-12">
                <input name="title" class="form-control form-control-sm"
                       placeholder="<?=html($t['planTitle'] ?? 'Título del plan')?>" />
              </div>
              <div class="col-12">
                <input name="when" class="form-control form-control-sm"
                       placeholder="<?=html($t['when'] ?? 'Cuándo (YYYY-MM-DD HH:mm)')?>" />
              </div>
              <div class="col-12">
                <input name="where" class="form-control form-control-sm"
                       placeholder="<?=html($t['where'] ?? 'Dónde')?>" />
              </div>
              <div class="col-6">
                <input type="number" min="1" name="capacity"
                       class="form-control form-control-sm"
                       placeholder="<?=html($t['maxPeople'] ?? 'Máx. personas')?>"
                       value="6" />
              </div>
              <div class="col-6">
                <input type="number" min="1" name="min"
                       class="form-control form-control-sm"
                       placeholder="<?=html($t['minPeople'] ?? 'Mínimo personas')?>"
                       value="1" />
              </div>
              <div class="col-12">
                <button class="btn btn-warning btn-sm mt-1 text-dark fw-semibold" type="submit">
                  <?=html($t['create'] ?? 'Crear')?>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    <?php
}
