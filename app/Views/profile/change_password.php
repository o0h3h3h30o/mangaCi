<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<main>
    <div class="max-w-7xl mx-auto px-3 w-full mt-6">
        <div class="border-2 border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md rounded dark:shadow-gray-900 overflow-hidden">

            <!-- Header -->
            <div class="p-4 border-b border-gray-100 dark:border-dark-blue">
                <div class="flex items-center gap-3">
                    <a href="/profile" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Change Password</h2>
                </div>
            </div>

            <!-- Form -->
            <div class="p-6 max-w-md">

                <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 text-sm">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
                <?php endif; ?>

                <form action="/profile/change-password" method="POST" class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                        <input type="password" name="current_password" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-dark-blue bg-white dark:bg-light-blue text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="new_password" required minlength="6"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-dark-blue bg-white dark:bg-light-blue text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="6"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-dark-blue bg-white dark:bg-light-blue text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Save
                        </button>
                        <a href="/profile" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">Cancel</a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</main>
<?= $this->endSection() ?>
