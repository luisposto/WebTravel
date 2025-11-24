<?php
function view_group($t)
{
    global $user;

    $trip_id = intval($_GET['trip_id'] ?? 0);

    // Cargar viaje
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM trips WHERE id = :id");
    $stmt->execute([':id' => $trip_id]);
    $trip = $stmt->fetch();

    if (!$trip) {
        echo "<p class='text-danger'>Viaje no encontrado.</p>";
        return;
    }
    // Verificar si el usuario actual pertenece al viaje (dueño o participante)
    $is_member = false;
    if (!empty($user)) {
        $stmtMember = $pdo->prepare('SELECT 1 FROM trips WHERE id = :tid AND user_id = :uid
                                   UNION
                                   SELECT 1 FROM trip_participants WHERE trip_id = :tid AND user_id = :uid
                                   LIMIT 1');
        $stmtMember->execute([':tid' => $trip_id, ':uid' => $user['id']]);
        $is_member = (bool)$stmtMember->fetchColumn();
    }

    if (!$is_member) {
        echo "<p class='text-danger'>Tenés que sumarte al viaje para acceder al grupo.</p>";
        echo "<p><a class='btn btn-outline-secondary btn-sm' href='?screen=trips&lang=" . html($_SESSION['lang']) . "'>Volver a viajes</a></p>";
        return;
    }


    // Cargar participantes (dueño + viajeros que se sumaron al viaje) y mensajes
    $participants = [];

    // Dueño del viaje
    $stmtOwner = $pdo->prepare("SELECT u.id, u.name, u.country, u.bio, u.show_bio
                                FROM users u
                                JOIN trips t ON t.user_id = u.id
                                WHERE t.id = :tid");
    $stmtOwner->execute([':tid' => $trip_id]);
    if ($owner = $stmtOwner->fetch()) {
        $owner['is_owner'] = true;
        $participants[$owner['id']] = $owner;
    }

    // Participantes adicionales que se unieron al viaje
    $stmtP = $pdo->prepare("SELECT u.id, u.name, u.country, u.bio, u.show_bio
                            FROM trip_participants tp
                            JOIN users u ON u.id = tp.user_id
                            WHERE tp.trip_id = :tid
                            ORDER BY tp.created_at ASC");
    $stmtP->execute([':tid' => $trip_id]);

    foreach ($stmtP->fetchAll() as $row) {
        if (!isset($participants[$row['id']])) {
            $row['is_owner'] = false;
            $participants[$row['id']] = $row;
        }
    }

    // Normalizar a array indexado
    $participants = array_values($participants);

    // Verificar si el usuario actual puede escribir en el grupo
    $can_post = $is_member;



    $stmtM = $pdo->prepare("SELECT gm.*, u.name AS user_name
                            FROM group_messages gm
                            JOIN users u ON u.id = gm.user_id
                            WHERE gm.trip_id = :tid
                            ORDER BY gm.created_at ASC");
    $stmtM->execute([':tid' => $trip_id]);
    $messages = $stmtM->fetchAll();
    ?>
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h2 class="h5 fw-semibold mb-4">
        <?=html($trip['city'])?> ·
        <?=html($trip['start_date'])?> → <?=html($trip['end_date'])?>
      </h2>
      <div class="row g-4">
        <div class="col-md-8">
          <div id="group-messages" class="vstack gap-2 mb-3">
            <?php if (!empty($messages)): ?>
              <?php foreach ($messages as $m): ?>
                <div class="small">
                  <b><?=html($m['user_name'])?>:</b>
                  <?=html($m['message'])?>
                  <div class="text-muted tiny">
                    <?=html($m['created_at'])?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-muted small mb-0">Todavía no hay mensajes en este grupo.</p>
            <?php endif; ?>
          </div>
          <div id="typing-indicator" class="text-muted small mb-2" style="display:none;"></div>
          <?php if ($can_post): ?>
            <form class="d-flex gap-2" method="post">
              <input type="hidden" name="action" value="send_group_message" />
              <input type="hidden" name="trip_id" value="<?=html($trip_id)?>" />
              <input class="form-control form-control-sm" name="message" placeholder="Escribí un mensaje" required />
              <button class="btn btn-primary btn-sm" type="submit">Enviar</button>
            </form>
          <?php else: ?>
            <p class="text-muted small">Sumate al viaje para poder escribir en el grupo.</p>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
          <div class="border rounded-3 p-3 mb-3">
            <h6 class="fw-semibold mb-2">Participantes</h6>
            <?php if (!empty($participants)): ?>
              <ul class="list-unstyled small mb-0">
                <?php foreach ($participants as $p): ?>
                  <li>
                    <?php if (!empty($p['is_owner']) && !empty($user) && isset($user['id']) && (int)$user['id'] === (int)$p['id']): ?>
                      • <?=html($p['name'])?>
                      <span class="badge bg-primary ms-1">Organizador (vos)</span>
                      <?php if (!empty($user['bio'])): ?>
                        <div class="small text-muted mt-1">
                          <!--<?=html($user['bio'])?>!-->
                        </div>
                      <?php endif; ?>
                    <?php else: ?>
                      • <button type="button"
                                class="btn btn-link btn-sm p-0 align-baseline participant-bio-trigger"
                                data-user-name="<?=html($p['name'])?>"
                                data-user-country="<?=html($p['country'] ?? '')?>"
                                data-user-bio="<?=html($p['bio'] ?? '')?>"
                                data-user-show-bio="<?=!empty($p['show_bio']) ? '1' : '0'?>">
                          <?=html($p['name'])?>
                        </button>
                      <?php if (!empty($p['is_owner'])): ?>
                        <span class="badge bg-primary ms-1">Organizador</span>
                      <?php endif; ?>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="small text-muted mb-0">Sin participantes registrados.</p>
            <?php endif; ?>
          </div>
          <div class="vstack gap-2">
            <a class="btn btn-outline-secondary btn-sm"
               href="?screen=trips&lang=<?=html($_SESSION['lang'])?>">
              <?=html($t['myTrips'])?>
            </a>
            <a class="btn btn-outline-secondary btn-sm"
               href="?screen=plans&lang=<?=html($_SESSION['lang'])?>&trip_id=<?=html($trip_id)?>&plan_city=<?=urlencode($trip['city'])?>&plan_from=<?=html($trip['start_date'])?>&plan_to=<?=html($trip['end_date'])?>">
              <?=html($t['plans'])?>
            </a>
            <a class="btn btn-outline-secondary btn-sm"
               href="?screen=recommendations&lang=<?=html($_SESSION['lang'])?>&trip_id=<?=html($trip_id)?>">
              <?=html($t['recommendations'])
              ?></a>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tripId = <?= isset($trip_id) ? (int)$trip_id : (isset($trip['id']) ? (int)$trip['id'] : 0) ?>;
  const container = document.getElementById('group-messages');
  const typingEl = document.getElementById('typing-indicator');
  const form = document.querySelector('form.d-flex.gap-2');
  const input = form ? form.querySelector('input[name="message"]') : null;

  let lastMessageCount = 0;
  let lastTypingSentAt = 0;

  function scrollToBottom() {
    if (!container) return;
    container.scrollTop = container.scrollHeight;
  }

  function beep() {
    try {
      const AC = window.AudioContext || window.webkitAudioContext;
      if (!AC) return;
      const ctx = new AC();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.value = 880;
      osc.connect(gain);
      gain.connect(ctx.destination);
      const now = ctx.currentTime;
      gain.gain.setValueAtTime(0.0001, now);
      gain.gain.exponentialRampToValueAtTime(0.12, now + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.18);
      osc.start(now);
      osc.stop(now + 0.2);
    } catch (e) {
      // ignore audio errors
    }
  }

  function cargarMensajes() {
    if (!tripId || !container) return;

    fetch('api/group_messages.php?trip_id=' + encodeURIComponent(tripId) + '&_=' + Date.now(), {
      cache: 'no-store'
    })
      .then(function (res) {
        if (!res.ok) throw new Error('Respuesta no OK');
        return res.json();
      })
      .then(function (data) {
        if (!data.ok) {
          console.warn('Error desde API mensajes:', data.error);
          return;
        }
        // count before
        const prevCount = container.querySelectorAll('.small').length;
        container.innerHTML = data.html || '';
        const newCount = container.querySelectorAll('.small').length;

        if (newCount > prevCount && prevCount !== 0) {
          scrollToBottom();
          beep();
        } else if (prevCount === 0 && newCount > 0) {
          scrollToBottom();
        }
      })
      .catch(function (err) {
        console.error('Error cargando mensajes del grupo:', err);
      });
  }

  function enviarMensajeAjax(event) {
    if (!form || !input) return;
    event.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    const formData = new FormData(form);
    formData.append('ajax', '1');

    fetch('index.php?screen=group&trip_id=' + encodeURIComponent(tripId), {
      method: 'POST',
      body: formData
    })
    .then(function (res) {
      if (!res.ok) throw new Error('Error al enviar mensaje');
      return res.text();
    })
    .then(function () {
      input.value = '';
      cargarMensajes();
      scrollToBottom();
    })
    .catch(function (err) {
      console.error('Error enviando mensaje:', err);
    });
  }

  function sendTyping() {
    const now = Date.now();
    if (now - lastTypingSentAt < 2000) {
      return;
    }
    lastTypingSentAt = now;
    if (!tripId) return;

    fetch('api/group_typing.php?trip_id=' + encodeURIComponent(tripId), {
      method: 'POST'
    }).catch(function (err) {
      console.warn('Error enviando typing:', err);
    });
  }

  function cargarTyping() {
    if (!tripId || !typingEl) return;
    fetch('api/group_typing_status.php?trip_id=' + encodeURIComponent(tripId) + '&_=' + Date.now(), {
      cache: 'no-store'
    })
      .then(function (res) {
        if (!res.ok) throw new Error('Respuesta no OK (typing)');
        return res.json();
      })
      .then(function (data) {
        if (!data.ok) {
          typingEl.style.display = 'none';
          typingEl.textContent = '';
          return;
        }
        const names = data.users || [];
        if (names.length === 0) {
          typingEl.style.display = 'none';
          typingEl.textContent = '';
        } else {
          let text = '';
          if (names.length === 1) {
            text = names[0] + ' está escribiendo…';
          } else if (names.length === 2) {
            text = names[0] + ' y ' + names[1] + ' están escribiendo…';
          } else {
            text = names[0] + ' y otros están escribiendo…';
          }
          typingEl.textContent = text;
          typingEl.style.display = '';
        }
      })
      .catch(function (err) {
        console.warn('Error estado typing:', err);
      });
  }

  if (form && input) {
    form.addEventListener('submit', enviarMensajeAjax);
    input.addEventListener('input', function () {
      sendTyping();
    });
  }

  // Primera carga
  cargarMensajes();
  cargarTyping();
  // Refrescos periódicos
  setInterval(cargarMensajes, 3000);
  setInterval(cargarTyping, 3000);

  // --- Bio de participantes ---
  var bioModalEl = document.getElementById('bioModal');
  var bioModal = null;
  if (bioModalEl && window.bootstrap && typeof bootstrap.Modal === 'function') {
    bioModal = new bootstrap.Modal(bioModalEl);
  }

  function onParticipantClick(ev) {
    var btn = ev.currentTarget;
    if (!btn) return;

    var show = btn.getAttribute('data-user-show-bio') || '0';
    var bio = btn.getAttribute('data-user-bio') || '';
    var name = btn.getAttribute('data-user-name') || '';
    var country = btn.getAttribute('data-user-country') || '';

    // Opción A: si la bio no está habilitada o está vacía, no mostramos nada
    if (show !== '1' || !bio.trim()) {
      return;
    }

    var nameNode = document.getElementById('bioModalName');
    var countryNode = document.getElementById('bioModalCountry');
    var bioNode = document.getElementById('bioModalBio');

    if (nameNode) nameNode.textContent = name;
    if (countryNode) countryNode.textContent = country;
    if (bioNode) bioNode.textContent = bio;

    if (bioModal) {
      bioModal.show();
    }
  }

  function bindParticipantClicks() {
    var triggers = document.querySelectorAll('.participant-bio-trigger');
    triggers.forEach(function (btn) {
      btn.removeEventListener('click', onParticipantClick);
      btn.addEventListener('click', onParticipantClick);
    });
  }

  // Enlazar al cargar la página
  bindParticipantClicks();

});
</script>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Modal para ver la bio de un viajero -->
<div class="modal fade" id="bioModal" tabindex="-1" aria-labelledby="bioModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bioModalLabel">Viajero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1 fw-semibold" id="bioModalName"></p>
        <p class="text-muted small mb-2" id="bioModalCountry"></p>
        <p class="mb-0" id="bioModalBio"></p>
      </div>
    </div>
  </div>
</div>
</section>
    <?php
}