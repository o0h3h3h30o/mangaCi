<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 – Page Not Found</title>
    <link href="<?= base_url('css/app.css?id=d0cecc47a2557912eaae75ef632a75da') ?>" rel="stylesheet">
    <style>
        body {
            background-color: #0e1726;
            background-image: url(https://i2.mgcdnxyz.cfd/storage/images/default/body-bg.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-18px); }
        }
        .float { animation: floatY 3.5s ease-in-out infinite; }
    </style>
</head>
<body>
    <div class="text-center max-w-md w-full">

        <!-- Illustration -->
        <div class="float mb-4">
            <img src="<?= base_url('images/404.png') ?>"
                 alt="404"
                 class="mx-auto w-64 sm:w-80 select-none pointer-events-none drop-shadow-2xl">
        </div>

        <!-- Text -->
        <h1 class="text-5xl sm:text-6xl font-black text-white mb-2 tracking-tight">404</h1>
        <h2 class="text-lg sm:text-xl font-semibold text-indigo-300 mb-2">Page not found!</h2>
        <p class="text-gray-400 text-sm mb-8">
            <?php if (ENVIRONMENT !== 'production'): ?>
                <?= nl2br(esc($message)) ?>
            <?php else: ?>
                The page you're looking for doesn't exist or has been removed.
            <?php endif; ?>
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Back to Home
            </a>
            <a href="/search"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-gray-600 text-gray-300 text-sm font-semibold hover:bg-white/10 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Browse Manga
            </a>
        </div>

    </div>
</body>
</html>
