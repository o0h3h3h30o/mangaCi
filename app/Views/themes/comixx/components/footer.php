  <!-- Footer -->
  <footer class="footer">
    <div class="footer-inner container">
      <div class="footer-notice">
        <p><?= esc(site_setting('footer_copyright', 'Does not store any files on our servers, we only linked to the media which is hosted on 3rd party services.')) ?></p>
      </div>
      <div class="footer-links-row">
        <div class="footer-col">
          <h4>Lectura</h4>
          <a href="/search?sort=-updated_at">ÚLTIMAS ACTUALIZACIONES</a>
          <a href="/search?sort=-views">POPULARES</a>
          <a href="/search?sort=-created_at">MÁS NUEVOS</a>
        </div>
        <div class="footer-col">
          <h4>Enlaces</h4>
          <a href="/search">VER TODO</a>
          <a href="/sitemap.xml">SITEMAP</a>
        </div>
      </div>
      <div class="footer-brand">
        <h2>La Nueva<br>Experiencia Manga.</h2>
        <div class="footer-bottom">
          <div class="footer-logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect x="2" y="3" width="20" height="18" rx="2" stroke="white" stroke-width="2"/>
              <path d="M8 3v18M16 3v18" stroke="white" stroke-width="2"/>
            </svg>
            <span><?= esc(site_setting('site_title', 'COMIX')) ?></span>
          </div>
          <p class="footer-copyright">&copy; <?= date('Y') ?> <?= esc(site_setting('site_title', 'COMIX')) ?></p>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
