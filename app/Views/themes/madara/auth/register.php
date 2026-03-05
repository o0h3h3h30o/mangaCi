<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - MangaCI</title>
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
                                Create new account
                            </div>

                            <?php if (session()->getFlashdata('error')): ?>
                            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded px-4 py-3 text-sm">
                                <?= esc(session()->getFlashdata('error')) ?>
                            </div>
                            <?php endif; ?>

                            <form method="post" action="/register">
                                <?= csrf_field() ?>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-xs font-bold mb-2">Full Name</label>
                                    <input type="text" name="name" value="<?= esc(old('name')) ?>"
                                           class="px-3 py-3 bg-white rounded text-sm shadow focus:outline-none focus:ring dark:text-black w-full border-0"
                                           placeholder="Your name">
                                </div>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-xs font-bold mb-2">Username</label>
                                    <input type="text" name="username" value="<?= esc(old('username')) ?>"
                                           class="px-3 py-3 bg-white rounded text-sm shadow focus:outline-none focus:ring dark:text-black w-full border-0"
                                           placeholder="e.g. cool_user123">
                                    <p class="text-xs text-gray-400 mt-1">3-30 characters, letters, numbers, underscore</p>
                                </div>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-xs font-bold mb-2">Email</label>
                                    <input type="email" name="email" value="<?= esc(old('email')) ?>"
                                           class="px-3 py-3 bg-white rounded text-sm shadow focus:outline-none focus:ring dark:text-black w-full border-0"
                                           placeholder="Email">
                                </div>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-xs font-bold mb-2">Password</label>
                                    <input type="password" name="password"
                                           class="border-0 px-3 py-3 bg-white rounded text-sm shadow focus:outline-none dark:text-black focus:ring w-full border-0"
                                           placeholder="Min 6 characters">
                                </div>
                                <div class="relative w-full mb-3">
                                    <label class="block uppercase text-xs font-bold mb-2">Confirm Password</label>
                                    <input type="password" name="confirm_password"
                                           class="border-0 px-3 py-3 bg-white rounded text-sm shadow focus:outline-none dark:text-black focus:ring w-full border-0"
                                           placeholder="Repeat password">
                                </div>
                                <div class="text-center mt-6">
                                    <button class="text-black bg-blue-200 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full"
                                            type="submit">Register
                                    </button>
                                </div>
                            </form>

                            <div class="mt-6 py-3 border-t-2 border-gray-200">
                                <a href="/login" class="text-sm text-blue-500 hover:text-blue-700">Already have an account? Login</a>
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
