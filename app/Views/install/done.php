<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MangaCI — Installed</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>body { background: #0f1117; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md text-center">

  <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-900/40 border border-green-700 mb-6">
    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
         stroke="#4ade80" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
  </div>

  <h1 class="text-2xl font-bold text-white mb-2">Installation Complete!</h1>
  <p class="text-gray-400 text-sm mb-8">MangaCI đã được cài đặt thành công. Trang install đã bị khóa.</p>

  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 text-left mb-5 space-y-2">
    <div class="flex items-center gap-3 text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#4ade80" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <span class="text-gray-300">Database tables created</span>
    </div>
    <div class="flex items-center gap-3 text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#4ade80" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <span class="text-gray-300">Admin account created</span>
    </div>
    <div class="flex items-center gap-3 text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#4ade80" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <span class="text-gray-300">Configuration file (.env) written</span>
    </div>
    <?php $cl = $composerLog ?? ['ok' => null, 'output' => '']; ?>
    <?php if ($cl['ok'] === true): ?>
    <div class="flex items-center gap-3 text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#4ade80" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      <span class="text-gray-300">Composer dependencies installed</span>
    </div>
    <?php elseif ($cl['ok'] === false): ?>
    <div class="flex items-center gap-3 text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
           stroke="#f87171" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      <span class="text-yellow-400">Composer: cài thủ công (xem log bên dưới)</span>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($cl['output'])): ?>
  <div class="mb-5 bg-gray-950 border border-gray-800 rounded-xl p-4 text-left">
    <p class="text-xs text-gray-500 mb-2 font-medium">Composer output</p>
    <pre class="text-xs text-gray-400 overflow-x-auto whitespace-pre-wrap max-h-48 overflow-y-auto"><?= esc($cl['output']) ?></pre>
  </div>
  <?php endif; ?>

  <div class="flex gap-3 justify-center">
    <a href="/login"
       class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
      Go to Login
    </a>
    <a href="/"
       class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
      Go to Site
    </a>
  </div>

</div>
</body>
</html>
