<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MangaCI</title>
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>
<body x-data class="dark bg-stone-800 text-white" :class="{ 'dark bg-stone-800 text-white': $store.darkMode.on }">
    <section class="absolute w-full h-full">
        <div class="container mx-auto px-4 h-full">
            <div class="flex content-center items-center justify-center h-full">
                <div class="w-full lg:w-4/12 px-4">
                    <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-white border-0 dark:border-2 dark:border-dark-blue dark:bg-fire-blue">
                        <div class="flex-auto p-6">
                            <div class="text-center mb-3 font-bold text-lg">
                                Login
                            </div>

                            <?php if (session()->getFlashdata('error')): ?>
                            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded px-4 py-3 text-sm">
                                <?= esc(session()->getFlashdata('error')) ?>
                            </div>
                            <?php endif; ?>

                            <form method="post" action="/login">
                                <?= csrf_field() ?>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Email or Username</label>
                                    <input type="text" name="login" value="<?= esc(old('login')) ?>"
                                           class="px-3 py-3 bg-white text-gray-800 rounded text-sm shadow focus:outline-none focus:ring w-full border-0"
                                           placeholder="Email or Username">
                                </div>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Password</label>
                                    <input type="password" name="password"
                                           class="border-0 px-3 py-3 bg-white text-gray-800 rounded text-sm shadow focus:outline-none focus:ring w-full border-0"
                                           placeholder="Password">
                                </div>
                                <div class="flex items-center mt-4">
                                    <input type="checkbox" name="remember" id="remember" value="1" class="w-4 h-4 text-blue-600 rounded cursor-pointer">
                                    <label for="remember" class="ml-2 text-sm text-gray-500 cursor-pointer select-none">Remember me for 7 days</label>
                                </div>
                                <div class="text-center mt-4">
                                    <button class="text-black bg-blue-200 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full"
                                            type="submit">Login
                                    </button>
                                </div>
                            </form>

                            <div class="flex flex-wrap mt-6 py-3 border-t-2 border-gray-200">
                                <div class="w-1/2">
                                    <a href="#" class="text-sm text-gray-500">Forgot password?</a>
                                </div>
                                <div class="w-1/2 text-right">
                                    <a href="/register" class="text-sm text-blue-500 hover:text-blue-700">Create new account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<script src="<?= base_url('js/app.js') ?>" defer></script>
</body>
</html>
