<?php
function render_footer()
{
    ?>
  </main>
  <footer class="border-top py-4 mt-5 small text-muted">
    <div class="container d-flex align-items-center gap-2">
      <span>🛡️ GDPR/CCPA • Términos • Privacidad</span>
    </div>
  </footer>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
          integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
          crossorigin=""></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var langForm = document.getElementById('langForm');
      var langInput = document.getElementById('langInput');
      var options = document.querySelectorAll('.lang-option');

      if (langForm && langInput && options.length) {
        options.forEach(function (btn) {
          btn.addEventListener('click', function () {
            var lang = this.getAttribute('data-lang');
            if (!lang) return;
            langInput.value = lang;
            langForm.submit();
          });
        });
      }
    });
  </script>
</body>
</html>
    <?php
}
