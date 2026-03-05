<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MangaCI — Install</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body { background: #0f1117; }
  input:focus { outline: none; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-lg">

  <!-- Header -->
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
           stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
      </svg>
    </div>
    <h1 class="text-2xl font-bold text-white">MangaCI</h1>
    <p class="text-gray-500 text-sm mt-1">Installation Wizard</p>
  </div>

  <?php if ($error): ?>
  <div class="mb-6 px-4 py-3 rounded-lg bg-red-900/40 border border-red-800 text-red-300 text-sm">
    <?= $error ?>
  </div>
  <?php endif; ?>

  <form method="post" action="/install" class="space-y-5">
    <?= csrf_field() ?>

    <!-- Database -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
      <h2 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <ellipse cx="12" cy="5" rx="9" ry="3"/>
          <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
          <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
        </svg>
        Database Connection
      </h2>

      <div class="grid grid-cols-3 gap-3 mb-3">
        <div class="col-span-2">
          <label class="block text-xs text-gray-400 mb-1">Host</label>
          <input type="text" name="db_host" value="<?= esc($old['db_host'] ?? '127.0.0.1') ?>"
                 placeholder="127.0.0.1"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-1">Port</label>
          <input type="number" name="db_port" value="<?= esc($old['db_port'] ?? '3306') ?>"
                 placeholder="3306"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
        </div>
      </div>

      <div class="mb-3">
        <label class="block text-xs text-gray-400 mb-1">Database Name</label>
        <input type="text" name="db_name" value="<?= esc($old['db_name'] ?? '') ?>"
               placeholder="manga_db" required
               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
        <p class="text-xs text-gray-600 mt-1">Sẽ được tạo tự động nếu chưa tồn tại.</p>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs text-gray-400 mb-1">DB Username</label>
          <input type="text" name="db_user" value="<?= esc($old['db_user'] ?? '') ?>"
                 placeholder="root" required
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
        </div>
        <div>
          <label class="block text-xs text-gray-400 mb-1">DB Password</label>
          <input type="password" name="db_pass" placeholder="(leave blank if none)"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
        </div>
      </div>
    </div>

    <!-- Admin Account -->
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
      <h2 class="text-sm font-semibold text-gray-200 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Admin Account
      </h2>

      <div class="space-y-3">
        <div>
          <label class="block text-xs text-gray-400 mb-1">Display Name <span class="text-gray-600">(optional)</span></label>
          <input type="text" name="admin_name" value="<?= esc($old['admin_name'] ?? '') ?>"
                 placeholder="Administrator"
                 class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs text-gray-400 mb-1">Username</label>
            <input type="text" name="admin_username" value="<?= esc($old['admin_username'] ?? '') ?>"
                   placeholder="admin" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Email</label>
            <input type="email" name="admin_email" value="<?= esc($old['admin_email'] ?? '') ?>"
                   placeholder="admin@example.com" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs text-gray-400 mb-1">Password</label>
            <input type="password" name="admin_pass" placeholder="Min. 6 characters" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Confirm Password</label>
            <input type="password" name="admin_confirm" placeholder="Repeat password" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:border-indigo-500 transition-colors">
          </div>
        </div>
      </div>
    </div>

    <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm py-3 rounded-xl transition-colors">
      Install MangaCI
    </button>
  </form>

  <p class="text-center text-xs text-gray-700 mt-6">
    Quá trình install sẽ tạo database, bảng, và tài khoản admin, sau đó ghi file <code class="text-gray-500">.env</code>.
  </p>

</div>
</body>
</html>
