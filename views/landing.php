<?php
function view_landing($t)
{
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <div class="row align-items-center g-4">
        <div class="col-md-6">
          <h2 class="h4 fw-semibold mb-3"><?=html($t['heroTitle'])?></h2>
          <p class="text-muted mb-3"><?=html($t['heroSub'])?></p>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="?screen=login&lang=<?=html($_SESSION['lang'])?>" class="btn btn-warning btn-sm text-dark fw-semibold">
              <?=html($t['getStarted'])?>
            </a>
            <button type="button" class="btn btn-outline-warning btn-sm">
              <?=html($t['tryBeta'])?>
            </button>
          </div>
          <div class="d-flex flex-wrap gap-3 small text-muted">
            <div class="d-flex align-items-center gap-1">
              <span>👥</span><span><?=html($t['heroBadge1'] ?? 'Viajeros verificados')?></span>
            </div>
            <div class="d-flex align-items-center gap-1">
              <span>✅</span><span><?=html($t['heroBadge2'] ?? 'Match por idioma e intereses')?></span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded-3 p-3 bg-light">
            <div class="ratio ratio-16x9 rounded-3 bg-white d-flex flex-column justify-content-center align-items-center mb-3">
              <div class="mb-2 small text-muted">📍 Barcelona, Roma, Lisboa…</div>
             
             
            </div>
             <div class="small text-muted">1.231 <?=html($t['activeTravelers'] ?? 'viajeros activos esta semana')?></div>
            <p class="small text-muted mb-0">
              <?=html($t['mapTeaser'] ?? 'Explorá qué viajeros estarán en tu mismo destino y fechas.')?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body">
          <h3 class="h6 fw-semibold mb-2"><?=html($t['howItWorks'] ?? 'Cómo funciona')?></h3>
          <ol class="small ps-3 mb-0">
            <li>Creá tu perfil e idiomas.</li>
            <li>Agendá tus viajes (destino + fechas).</li>
            <li>Sumate a grupos y armá planes con otros.</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body">
          <h3 class="h6 fw-semibold mb-2">CTA</h3>
          <form class="d-flex gap-2">
            <input class="form-control form-control-sm" type="email" placeholder="email@ejemplo.com" />
            <button type="button" class="btn btn-outline-warning btn-sm">
              <?=html($t['cta'])?>
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body">
          <h3 class="h6 fw-semibold mb-2"><?=html($t['faq'])?></h3>
          <ul class="small mb-0">
            <li><b>Privacidad:</b> controlás tu visibilidad.</li>
            <li><b>Seguridad:</b> SOS + reportes.</li>
            <li><b>Precio:</b> prueba 7 días, luego USD 2/año.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
    <?php
}
