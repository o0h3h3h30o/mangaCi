<!DOCTYPE html>
<html lang="vi">

<head itemscope itemtype="http://schema.org/WebPage">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php $_pt = trim($title ?? ''); $_st = site_setting('site_title', 'COMIX'); ?>
  <title><?= $_pt ? esc($_pt) . ' - ' . esc($_st) : esc($_st) ?></title>

  <?php $_cdn = rtrim(env('CDN_COVER_URL', ''), '/'); ?>
  <?php if ($_cdn): ?>
  <?php $_cdnOrigin = parse_url($_cdn, PHP_URL_SCHEME) . '://' . parse_url($_cdn, PHP_URL_HOST); ?>
  <link rel="preconnect" href="<?= esc($_cdnOrigin) ?>">
  <link rel="dns-prefetch" href="<?= esc($_cdnOrigin) ?>">
  <?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Comixx stylesheet -->
  <link rel="stylesheet" href="<?= base_url('css/comixx.css') ?>?v=<?= time() ?>">

  <link rel="canonical" href="<?= current_url() ?>">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

  <?php
    $_desc = !empty($description) ? $description : site_setting('meta_description');
    $_ogimg = !empty($og_image) ? $og_image : base_url('dcncc.jpg');
  ?>
  <meta name="description" content="<?= esc($_desc) ?>">
  <meta property="og:title" content="<?= esc($_pt ?: $_st) ?>">
  <meta property="og:description" content="<?= esc($_desc) ?>">
  <meta property="og:image" content="<?= esc($_ogimg) ?>">
  <meta property="og:url" content="<?= current_url() ?>">

  <?= $this->renderSection('head_extra') ?>

  <?php $_ga = site_setting('ga_id'); if ($_ga): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($_ga) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= esc($_ga) ?>');</script>
  <?php endif; ?>
</head>

<body class="reader-body">
  <?= $this->renderSection('content') ?>
</body>
</html>
