<?php
function view_recommendations($t, $tips)
{
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <h2 class="h4 fw-semibold mb-3"><?=html($t['recommendations'] ?? 'Recomendaciones')?></h2>
      <div class="row g-3">
        <?php if (!empty($tips)): ?>
          <?php foreach ($tips as $tip): ?>
            <div class="col-md-6">
              <div class="border rounded-3 p-3 h-100 d-flex flex-column">
                <div class="small text-muted mb-1">
                  <?=html($t['created'] ?? 'Creado')?> <?=html($t['by'] ?? 'por')?> 
                  <b><?=html($tip['user_name'] ?? 'Usuario')?></b>
                </div>
                <p class="small mb-2 flex-grow-1">
                  <?=html($tip['text'] ?? ($tip['body'] ?? ($tip['tip'] ?? '')))?>
                </p>
                <?php if (!empty($tip['url'])): ?>
                  <a href="<?=html($tip['url'])?>" class="small text-decoration-none mb-2" target="_blank" rel="noopener">
                    <?=html($tip['url'])?>
                  </a>
                <?php endif; ?>
                <div class="mt-auto d-flex justify-content-between align-items-center small text-muted">
                  <span>
                    <?php if (!empty($tip['created_at'])): ?>
                      <?=html($tip['created_at'])?>
                    <?php endif; ?>
                  </span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-muted small mb-0"><?=html($t['no_recommendations'] ?? 'Todavía no hay recomendaciones.')?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h3 class="h6 fw-semibold mb-3"><?=html($t['add_recommendation'] ?? 'Agregar recomendación')?></h3>
      <form method="post">
        <input type="hidden" name="action" value="add_tip" />
        <div class="row g-2">
          <div class="col-12">
            <textarea name="text" rows="3" class="form-control form-control-sm" placeholder="<?=html($t['your_recommendation'] ?? 'Tu recomendación')?>"></textarea>
          </div>
          <div class="col-md-6">
            <input name="url" class="form-control form-control-sm" placeholder="URL (opcional)" />
          </div>
          <div class="col-12">
            <button class="btn btn-warning btn-sm mt-1 text-dark fw-semibold" type="submit">
              <?=html($t['publish'] ?? 'Publicar')?>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
    <?php
}
