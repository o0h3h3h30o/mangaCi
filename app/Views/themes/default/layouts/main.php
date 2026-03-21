<!DOCTYPE html>
<html lang="vi">

<head itemscope itemtype="http://schema.org/WebPage">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <?php $_pt = trim($title ?? ''); $_st = site_setting('site_title', 'MangaCI'); $_hh = site_setting('home_heading', ''); ?>
  <title><?= $_pt ? esc($_pt) . ' - ' . esc($_st) : ($_hh ? esc($_hh) : esc($_st)) ?></title>

  <style>
    /* Critical: Self-hosted Font */
    @font-face {
      font-family: 'Afacad Flux';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: url('<?= base_url('fonts/afacad-flux-400.ttf') ?>') format('truetype')
    }

    /* Critical: Body & Background */
    body {
      background-repeat: repeat;
      min-width: 375px;
      background: #f1f2ff
    }

    body.dark {
      background-color: #0e1726
    }

    body.dark>span.bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      display: block;
      bottom: 0;
      background-image: url(https://i2.mgcdnxyz.cfd/storage/images/default/body-bg.jpg);
      background-repeat: no-repeat;
      background-size: cover;
      z-index: -1;
      opacity: .5
    }

    /* Critical: Navigation */
    #main-nav {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 49;
      will-change: transform;
      transition: transform 200ms linear
    }

    .nav-pinned {
      transform: translateY(0)
    }

    /* Critical: Layout container */
    .max-w-7xl { max-width: 80rem }
    .mx-auto { margin-left: auto; margin-right: auto }
    .px-3 { padding-left: .75rem; padding-right: .75rem }
    .w-full { width: 100% }
    .mt-\[80px\] { margin-top: 80px }

    @media(min-width:768px) {
      .md\:mt-\[140px\] { margin-top: 140px }
    }

    /* Critical: Dark mode text */
    .dark { --tw-text-opacity: 1 }
    .text-white { color: rgb(255 255 255/var(--tw-text-opacity)) }
    .bg-stone-800 { background-color: rgb(41 37 36) }

    /* Critical: Font */
    html { font-family: 'Afacad Flux', Arial, Helvetica, sans-serif }

    /* Critical: SVG Icon sizes */
    .h-3 { height: .75rem } .h-4 { height: 1rem } .h-5 { height: 1.25rem }
    .h-6 { height: 1.5rem } .h-9 { height: 2.25rem } .h-10 { height: 2.5rem }
    .w-3 { width: .75rem } .w-4 { width: 1rem } .w-5 { width: 1.25rem }
    .w-6 { width: 1.5rem } .w-8 { width: 2rem } .w-10 { width: 2.5rem }
    .w-auto { width: auto }

    /* Critical: Display utilities */
    .hidden { display: none } .block { display: block }
    .flex { display: flex } .inline-flex { display: inline-flex }

    /* Critical: Flexbox alignment */
    .items-center { align-items: center }
    .justify-center { justify-content: center }
    .justify-between { justify-content: space-between }

    /* Critical: Navigation background */
    .bg-light-blue { background-color: #111827 }

    /* Critical: Rounded */
    .rounded-full { border-radius: 9999px }
    .rounded-md { border-radius: .375rem }

    /* Critical: Position */
    .relative { position: relative } .absolute { position: absolute } .fixed { position: fixed }

    /* Critical: Sizing for nav */
    .h-16 { height: 4rem }

    /* Critical: Padding cho buttons */
    .p-1 { padding: .25rem }
    .mr-2 { margin-right: .5rem }

    /* Critical: Text color cho icons */
    .text-gray-400 { color: rgb(156 163 175) }

    /* Critical: Hide mobile menu */
    @media(min-width:640px) {
      .sm\:hidden { display: none }
    }

    /* Critical: Swiper layout - prevent FOUC */
    .swiper { overflow: hidden; position: relative; }
    .swiper-wrapper { display: flex; }
    .swiper-slide { flex-shrink: 0; }


    /* Swiper custom */
    .newest-swiper .swiper-button-next, .newest-swiper .swiper-button-prev,
    .hot-today-swiper .swiper-button-next, .hot-today-swiper .swiper-button-prev {
      color: #fff;
      background: rgba(0,0,0,0.5);
      width: 36px;
      height: 36px;
      border-radius: 50%;
      opacity: 0;
      transition: opacity .3s;
    }
    .newest-swiper .swiper-button-next::after, .newest-swiper .swiper-button-prev::after,
    .hot-today-swiper .swiper-button-next::after, .hot-today-swiper .swiper-button-prev::after { font-size: 16px; }
    .newest-swiper:hover .swiper-button-next, .newest-swiper:hover .swiper-button-prev,
    .hot-today-swiper:hover .swiper-button-next, .hot-today-swiper:hover .swiper-button-prev { opacity: 1; }
  </style>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
    /* Pre-size swiper slides to match JS config → prevent CLS
       Mobile (<640): 3 slides, gap 12px
       SM (640+):     3 slides, gap 16px
       LG (1024+):    6 slides, gap 20px */
    .newest-swiper .swiper-slide,
    .hot-today-swiper .swiper-slide { width: calc((100% - 24px) / 3) }
    @media(min-width:640px) {
      .newest-swiper .swiper-slide,
      .hot-today-swiper .swiper-slide { width: calc((100% - 32px) / 3) }
    }
    @media(min-width:1024px) {
      .newest-swiper .swiper-slide,
      .hot-today-swiper .swiper-slide { width: calc((100% - 100px) / 6) }
    }
  </style>
  <link rel="preload" href="<?= base_url('fonts/afacad-flux-400.ttf') ?>" as="font" type="font/ttf" crossorigin>
  <link rel="preload" href="<?= base_url('css/app.css?id=d0cecc47a2557912eaae75ef632a75da') ?>" as="style">
  <link href="<?= base_url('css/app.css?id=d0cecc47a2557912eaae75ef632a75da') ?>" rel="stylesheet">
  
  <meta name="robots" content="index, follow">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="Author" content="MangaCI" />
  <meta name="copyright" content="Copyright &copy; 2026 MangaCI" />
  <link rel="canonical" href="<?= current_url() ?>">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

  <?php
    $_desc = !empty($description) ? $description : site_setting('meta_description');
    $_kw   = site_setting('meta_keywords');
  ?>
  <meta name="description" content="<?= esc($_desc) ?>">
  <?php if ($_kw): ?><meta name="keywords" content="<?= esc($_kw) ?>"><?php endif; ?>
  <meta property="og:title" content="<?= esc($title ?? '') ?>">
  <meta property="og:description" content="<?= esc($_desc) ?>">
  <?php $_ogimg = !empty($og_image) ? $og_image : base_url('dcncc.jpg'); ?>
  <meta property="og:image" content="<?= esc($_ogimg) ?>">
  <meta property="og:url" content="<?= current_url() ?>">
  <meta property="og:type" content="website">

  <!-- Livewire Styles -->
  <style>
    [wire\:loading],[wire\:loading\.delay],[wire\:loading\.inline-block],[wire\:loading\.inline],[wire\:loading\.block],[wire\:loading\.flex],[wire\:loading\.table],[wire\:loading\.grid],[wire\:loading\.inline-flex] { display: none; }
    [wire\:loading\.delay\.shortest],[wire\:loading\.delay\.shorter],[wire\:loading\.delay\.short],[wire\:loading\.delay\.long],[wire\:loading\.delay\.longer],[wire\:loading\.delay\.longest] { display: none; }
    [wire\:offline] { display: none; }
    [wire\:dirty]:not(textarea):not(input):not(select) { display: none; }
    input:-webkit-autofill,select:-webkit-autofill,textarea:-webkit-autofill { animation-duration: 50000s; animation-name: livewireautofill; }
    @keyframes livewireautofill { from {} }
  </style>
    <style>
    @media(min-width:768px){#nav-spacer{height:60px}}
    @media(min-width:768px){main{margin-top:60px}}
  </style>
  <?= $this->renderSection('head_extra') ?>
  <?php $_ga = site_setting('ga_id'); if ($_ga): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($_ga) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= esc($_ga) ?>');</script>
  <?php endif; ?>
</head>

<?= $this->include('themes/default/components/header') ?>


  <div id="nav-spacer" style="height:80px"></div>
  <style>@media(min-width:768px){#nav-spacer{height:140px}}</style>
<?= $this->renderSection('content') ?>

<?= $this->include('themes/default/components/footer') ?>
