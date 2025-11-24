<?php
function view_trips($t, $trips, $filters = [], $user = null)
{
    $lang       = $_SESSION['lang'] ?? 'es';
    $cityFilter = $filters['city']        ?? '';
    $fromFilter = $filters['from']        ?? '';
    $toFilter   = $filters['to']          ?? '';
    $onlyOthers = !empty($filters['only_others']);
    $onlyMine   = !empty($filters['only_mine']);
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h2 class="h4 fw-semibold mb-0"><?=html($t['myTrips'] ?? 'Viajes')?></h2>
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
        <input type="hidden" name="screen" value="trips" />
        <input type="hidden" name="lang" value="<?=html($lang)?>" />

        <div class="col-md-3 col-6">
          <label class="form-label small mb-1"><?=html($t['destination'] ?? 'Destino')?></label>
          <input name="trip_city"
                 class="form-control form-control-sm"
                 placeholder="<?=html($t['destination'] ?? 'Ciudad destino')?>"
                 value="<?=html($cityFilter)?>" />
        </div>

        <div class="col-md-2 col-6">
          <label class="form-label small mb-1"><?=html($t['start'] ?? 'Desde')?></label>
          <input type="date"
                 name="trip_from"
                 class="form-control form-control-sm"
                 value="<?=html($fromFilter)?>" />
        </div>

        <div class="col-md-2 col-6">
          <label class="form-label small mb-1"><?=html($t['end'] ?? 'Hasta')?></label>
          <input type="date"
                 name="trip_to"
                 class="form-control form-control-sm"
                 value="<?=html($toFilter)?>" />
        </div>

        <div class="col-md-3 col-6 d-flex align-items-end gap-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1"
                   id="onlyOthersCheck" name="only_others"
                   <?=$onlyOthers ? 'checked' : ''?> />
            <label class="form-check-label small" for="onlyOthersCheck">
              <?=html($t['onlyOthersTrips'] ?? 'Sólo viajes de otros usuarios')?>
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1"
                   id="onlyMineCheck" name="only_mine"
                   <?=$onlyMine ? 'checked' : ''?> />
            <label class="form-check-label small" for="onlyMineCheck">
              <?=html($t['onlyMyTrips'] ?? 'Mis viajes')?>
            </label>
          </div>
        </div>

        <div class="col-md-2 col-12 d-flex align-items-end gap-2">
          <button class="btn btn-warning btn-sm text-dark fw-semibold" type="submit">
            <?=html($t['search'] ?? 'Filtrar')?>
          </button>
          <a href="?screen=trips&lang=<?=html($lang)?>" class="btn btn-outline-secondary btn-sm">
            <?=html($t['clear'] ?? 'Limpiar')?>
          </a>
        </div>
      </form>

      <div class="row g-4">
        <div class="col-md-8">
          <?php if (empty($trips)): ?>
            <p class="small text-muted mb-0">
              <?=html($t['noTrips'] ?? 'Todavía no hay viajes que coincidan con el filtro.')?>
            </p>
          <?php else: ?>
            <?php foreach ($trips as $trip): ?>
              <?php
                $isOwner   = !empty($trip['is_owner']);
                $isJoined  = !empty($trip['is_joined']);
                $ownerName = $trip['owner_name'] ?? '';
              ?>
              <div class="border rounded-3 p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                  <div class="fw-medium">
                    <?=html($trip['city'] ?? '')?>
                    <?php if ($isOwner): ?>
                      <span class="badge text-bg-warning text-dark ms-1 small">Tu viaje</span>
                    <?php endif; ?>
                  </div>
                  <div class="small text-muted">
                    <?=html($trip['start_date'] ?? '')?> →
                    <?=html($trip['end_date'] ?? '')?>
                  </div>
                  <div class="small text-muted">
                    👤 <?=html($ownerName)?>
                  </div>
                  <div class="small text-muted">
                    👥 <?=html((int)($trip['participants_count'] ?? 0))?> <?=html($t['travelers'] ?? 'viajeros')?>
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                  <?php if ($isOwner): ?>
                    <span class="badge text-bg-light small">
                      <?=html($t['youHost'] ?? 'Sos el anfitrión')?>
                    </span>
                  <?php else: ?>
                    <form method="post" class="d-inline">
                      <input type="hidden" name="trip_id" value="<?=html($trip['id'])?>" />
                      <?php if ($isJoined): ?>
                        <input type="hidden" name="action" value="leave_trip" />
                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                          <?=html($t['leave'] ?? 'Salir del viaje')?>
                        </button>
                      <?php else: ?>
                        <input type="hidden" name="action" value="join_trip" />
                        <button class="btn btn-warning btn-sm text-dark fw-semibold" type="submit">
                          <?=html($t['join'] ?? 'Unirme')?>
                        </button>
                      <?php endif; ?>
                    </form>
                  <?php endif; ?>

                  <a class="btn btn-outline-warning btn-sm"
                     href="?screen=group&trip_id=<?=html($trip['id'])?>&lang=<?=html($lang)?>">
                    <?=html($t['group'] ?? 'Grupo')?>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="col-md-4">
          <div class="border rounded-3 p-3 bg-light-subtle">
            <h3 class="h6 fw-semibold mb-3">➕ <?=html($t['addTrip'] ?? 'Agregar viaje')?></h3>
            <form method="post" class="vstack gap-2">
              <input type="hidden" name="action" value="add_trip" />
              <input class="form-control form-control-sm" name="city"
                     placeholder="<?=html($t['destination'] ?? 'Destino')?>"
                     list="cities" />
              <datalist id="cities">
                <option>Barcelona</option>
                <option>Madrid</option>
                <option>Lisboa</option>
                <option>Buenos Aires</option>
                <option>Roma</option>
              </datalist>
              <input class="form-control form-control-sm" type="date" name="start"
                     placeholder="<?=html($t['start'] ?? 'Fecha inicio')?>" />
              <input class="form-control form-control-sm" type="date" name="end"
                     placeholder="<?=html($t['end'] ?? 'Fecha fin')?>" />
              <button class="btn btn-warning btn-sm mt-1 text-dark fw-semibold" type="submit">
                <?=html($t['create'] ?? 'Crear')?>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    <?php
}
