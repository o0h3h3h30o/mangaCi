<?php
$title   = esc($siteTitle ?? 'Site');
$logo    = $siteLogo ?? '';
$message = trim((string) ($message ?? ''));
if ($message === '') {
    $message = "We're performing scheduled maintenance. We'll be back shortly — thanks for your patience.";
}
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= $title ?> — Maintenance</title>
<style>
  :root {
    --bg: #0b0d12;
    --panel: #141821;
    --border: rgba(255,255,255,.06);
    --text: #e6e9ef;
    --muted: #8a93a4;
    --accent: #34d399;
  }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; min-height: 100%; }
  body {
    background:
      radial-gradient(1200px 600px at 80% -10%, rgba(52,211,153,.08), transparent 60%),
      radial-gradient(900px 500px at -10% 100%, rgba(99,102,241,.10), transparent 60%),
      var(--bg);
    color: var(--text);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
  }
  .card {
    width: 100%;
    max-width: 520px;
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 40px 32px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
  }
  .logo {
    height: 40px;
    max-width: 200px;
    margin: 0 auto 24px;
    display: block;
    object-fit: contain;
  }
  .icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(52,211,153,.10);
    color: var(--accent);
  }
  h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 12px;
    letter-spacing: -0.01em;
  }
  p {
    font-size: 14px;
    line-height: 1.65;
    color: var(--muted);
    margin: 0 0 24px;
    white-space: pre-wrap;
  }
  .meta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--muted);
    background: rgba(255,255,255,.03);
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 6px 14px;
  }
  .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 0 rgba(52,211,153,.6);
    animation: pulse 1.8s infinite;
  }
  @keyframes pulse {
    0%   { box-shadow: 0 0 0 0 rgba(52,211,153,.6); }
    70%  { box-shadow: 0 0 0 10px rgba(52,211,153,0); }
    100% { box-shadow: 0 0 0 0 rgba(52,211,153,0); }
  }
  .site {
    margin-top: 20px;
    font-size: 12px;
    color: var(--muted);
    letter-spacing: .02em;
  }
</style>
</head>
<body>
  <div class="card" role="alert" aria-live="polite">
    <?php if ($logo !== ''): ?>
      <img src="<?= esc($logo) ?>" alt="<?= $title ?>" class="logo">
    <?php else: ?>
      <div class="icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
      </div>
    <?php endif; ?>
    <h1>We&rsquo;ll be right back</h1>
    <p><?= esc($message) ?></p>
    <div class="meta"><span class="dot"></span><span>Maintenance in progress</span></div>
    <div class="site"><?= $title ?></div>
  </div>
</body>
</html>
