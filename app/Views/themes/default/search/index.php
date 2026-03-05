<?= $this->extend('themes/default/layouts/main') ?>

<?= $this->section('content') ?>
    <main>
        <div class="max-w-7xl mx-auto px-3 w-full mt-6" x-data="listFilers">

            <div x-data="{ open: false, genresLoaded: false }">
                <!-- Keyword search bar -->
                <div class="items-center flex w-full justify-center">
                    <div class="flex bg-white dark:bg-stone-200 w-full rounded shadow-md">
                        <input type="text" name="search" x-model="params.keyword" @keyup.enter="doQuery()"
                            class="block border-transparent text-black grow focus:outline-none focus:ring-0 border-0 bg-transparent px-3 py-2"
                            placeholder="Enter keyword">
                        <select id="search-type" name="search-type" x-model="params.type"
                            class="border-transparent my-0 text-gray-500 focus:outline-none focus:ring-0 border-0 bg-transparent pr-2">
                            <option value="name">Title</option>
                            <option value="artist">Author</option>
                        </select>
                    </div>
                </div>

                <!-- Genre filter toggle -->
                <div class="flex justify-end mt-1 select-none" :class="{ 'hidden': open }">
                    <svg class="h-6 w-6 dark:text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" />
                        <path d="M5.5 5h13a1 1 0 0 1 0.5 1.5L14 12L14 19L10 16L10 12L5 6.5a1 1 0 0 1 0.5 -1.5" />
                    </svg>
                    <p class="cursor-pointer" @click.stop="open = !open; genresLoaded = true">Filter by genre</p>
                </div>

                <!-- Genre panel (lazy rendered) -->
                <template x-if="genresLoaded">
                    <div x-show="open" x-transition
                        class="justify-between border-2 border-t-0 border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded-b dark:shadow-gray-900 pt-4"
                        style="max-height:500px;overflow-y:auto">
                        <div class="flex justify-end cursor-pointer select-none mb-3" @click.stop="open = false">
                            <svg class="h-6 w-6 text-red-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" />
                                <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            <p>Close</p>
                        </div>

                        <!-- Dynamic genre checkboxes -->
                        <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                            <?php foreach ($categories as $cat): ?>
                            <label class="ml-3 inline-flex items-center cursor-pointer select-none"
                                @click="toggleGenre('<?= esc($cat['slug']) ?>')">
                                <div>
                                    <!-- Excluded (red X) -->
                                    <svg class="h-6 w-6 text-red-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        :class="{'hidden': params.genres['<?= esc($cat['slug']) ?>'] != 2}"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                        <path d="M10 10l4 4m0 -4l-4 4" />
                                    </svg>
                                    <!-- Included (green check) -->
                                    <svg class="h-6 w-6 text-green-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        :class="{'hidden': params.genres['<?= esc($cat['slug']) ?>'] != 1}"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                    <!-- Unchecked (empty box) -->
                                    <svg class="h-6 w-6 dark:text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        :class="{'hidden': params.genres['<?= esc($cat['slug']) ?>']}"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" />
                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                    </svg>
                                </div>
                                <div class="truncate">
                                    <span class="ml-2 text-sm font-semibold text-blueGray-600 text-ellipsis"><?= esc($cat['name']) ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Results section -->
            <div class="justify-between border-2 border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded font-semibold dark:shadow-gray-900 mt-6">
                <div class="p-4 border-b border-gray-100 dark:border-dark-blue">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl">
                                <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manga List</h2>
                            <span class="text-sm text-gray-400 font-normal">(<?= count($results) ?> results)</span>
                        </div>
                        <div class="relative">
                            <select @change="doQuery()"
                                class="appearance-none bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                name="sort" x-model="params.sort">
                                <option value="-updated_at">Recently Updated</option>
                                <option value="-created_at">Newest</option>
                                <option value="created_at">Oldest</option>
                                <option value="-views">Most Viewed</option>
                                <option value="-views_day">Top Day</option>
                                <option value="-views_week">Top Week</option>
                                <option value="name">A-Z</option>
                                <option value="-name">Z-A</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    <?php if (empty($results)): ?>
                    <p class="text-center text-gray-400 py-12">No results found.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <?php
                        foreach ($results as $manga):
                            $coverUrl = manga_cover_url($manga);
                            $diff = time() - ($manga['update_at'] ?? 0);
                            if ($diff < 60)         $ago = $diff . 's ago';
                            elseif ($diff < 3600)   $ago = floor($diff/60) . 'm ago';
                            elseif ($diff < 86400)  $ago = floor($diff/3600) . 'h ago';
                            elseif ($diff < 604800) $ago = floor($diff/86400) . 'd ago';
                            else                    $ago = floor($diff/604800) . 'w ago';
                        ?>
                        <div class="w-full">
                            <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue
                                hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover
                                hover:shadow-lg transition-all duration-300 manga-vertical overflow-hidden">
                                <div class="relative">
                                    <a href="/manga/<?= esc($manga['slug']) ?>" title="<?= esc($manga['name']) ?>">
                                        <div class="cover-frame">
                                            <img src="<?= esc($coverUrl) ?>" alt="<?= esc($manga['name']) ?>"
                                                class="cover" width="300" height="405" loading="lazy" decoding="async">
                                        </div>
                                    </a>
                                </div>
                                <div class="p-3">
                                    <h3 class="text-base font-semibold mb-2 truncate">
                                        <a href="/manga/<?= esc($manga['slug']) ?>"
                                            class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                                            <?= esc($manga['name']) ?>
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <?php if (($manga['chapter_1'] ?? 0) > 0): ?>
                                        <h4 class="flex-shrink-0">
                                            <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug'] ?? '') ?>"
                                                class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-xs font-medium whitespace-nowrap">
                                                Chapter <?= rtrim(rtrim(number_format((float)$manga['chapter_1'], 1), '0'), '.') ?>
                                            </a>
                                        </h4>
                                        <?php endif; ?>
                                        <div class="flex items-center gap-1 text-gray-400 text-xs flex-shrink min-w-0">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="whitespace-nowrap overflow-hidden text-ellipsis"><?= $ago ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pager): ?>
                    <div class="mt-6">
                        <?= $pager->links() ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
<?= $this->endSection() ?>
