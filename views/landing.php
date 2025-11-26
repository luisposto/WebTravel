<?php
function view_landing($t)
{
    ?>
<section class="mb-4">
  <div class="card shadow-soft border-0 mb-3 tm-hero-card">
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
            <div class="tm-map-wrapper mb-3">
              <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                <span>📍</span><span>Barcelona, Roma, Lisboa…</span>
              </div>
              <div id="tm-map" class="tm-map"></div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
  var mapEl = document.getElementById('tm-map');
  if (!mapEl || typeof L === 'undefined') return;

  var map = L.map('tm-map', {
    zoomControl: false
  }).setView([40.0, 0.0], 3);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Cargar usuarios online
  fetch('api/online_travelers.php')
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (!Array.isArray(data)) return;
      data.forEach(function (u) {
        if (!u.lat || !u.lng) return;
        var marker = L.circleMarker([u.lat, u.lng], {
          radius: 6,
          fillOpacity: 0.9
        });
        marker.addTo(map).bindPopup(
          (u.name || 'Viajero') + '<br/>' +
          (u.city || '') +
          (u.country ? ', ' + u.country : '')
        );
      });
    })
    .catch(function (err) {
      console.error('Error cargando viajeros online', err);
    });
});
</script>
    <?php
}
