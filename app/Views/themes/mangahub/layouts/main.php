<!DOCTYPE html>
<html lang="vi">

<head itemscope itemtype="http://schema.org/WebPage">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php $_pt = trim($title ?? ''); $_st = site_setting('site_title', 'MangaCI'); ?>
  <title><?= $_pt ? esc($_pt) . ' - ' . esc($_st) : esc($_st) ?></title>

  <?php $_cdn = rtrim(env('CDN_COVER_URL', ''), '/'); ?>
  <?php if ($_cdn): ?>
  <?php $_cdnOrigin = parse_url($_cdn, PHP_URL_SCHEME) . '://' . parse_url($_cdn, PHP_URL_HOST); ?>
  <link rel="preconnect" href="<?= esc($_cdnOrigin) ?>">
  <link rel="dns-prefetch" href="<?= esc($_cdnOrigin) ?>">
  <?php endif; ?>

  <!-- Mangahub stylesheet (preload + load) -->
  <link rel="preload" href="<?= base_url('css/mangahub.css') ?>?v=<?= time() ?>" as="style">
  <link rel="stylesheet" href="<?= base_url('css/mangahub.css') ?>?v=<?= time() ?>">

  <!-- SEO meta -->
  <meta name="robots" content="index, follow">
  <meta name="Author" content="<?= esc($_st) ?>">
  <meta name="copyright" content="Copyright &copy; <?= date('Y') ?> <?= esc($_st) ?>">
  <link rel="canonical" href="<?= current_url() ?>">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

  <?php
    $_desc = !empty($description) ? $description : site_setting('meta_description');
    $_kw   = site_setting('meta_keywords');
    $_ogimg = !empty($og_image) ? $og_image : base_url('dcncc.jpg');
  ?>
  <meta name="description" content="<?= esc($_desc) ?>">
  <?php if ($_kw): ?>
  <meta name="keywords" content="<?= esc($_kw) ?>">
  <?php endif; ?>

  <!-- Open Graph -->
  <meta property="og:title" content="<?= esc($_pt ?: $_st) ?>">
  <meta property="og:description" content="<?= esc($_desc) ?>">
  <meta property="og:image" content="<?= esc($_ogimg) ?>">
  <meta property="og:url" content="<?= current_url() ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= esc($_st) ?>">

  <?= $this->renderSection('head_extra') ?>

  <?php $_ga = site_setting('ga_id'); if ($_ga): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($_ga) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= esc($_ga) ?>');</script>
  <?php endif; ?>
</head>

<?= $this->include('themes/mangahub/components/header') ?>

  <main class="wrap main-content">
    <?= $this->renderSection('content') ?>

<?= $this->include('themes/mangahub/components/footer') ?>
