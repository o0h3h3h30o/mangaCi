<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Installer (blocked after install.lock exists — checked inside controller)
$routes->get ('install', 'Install::index');
$routes->post('install', 'Install::run');

$routes->get('/', 'Home::index');
$routes->get('manga/(:segment)', 'Manga::detail/$1');
$routes->get('manga/(:segment)/(:segment)', 'Manga::chapter/$1/$2');
$routes->get('search', 'Search::index');
$routes->get('api/me', 'ApiMeController::index');
$routes->get('api/search', 'Search::liveSearch');
$routes->get('history', 'History::index');
$routes->get('bookmarks', 'Bookmarks::index');
$routes->get('profile', 'Profile::index');
$routes->get('profile/change-password', 'Profile::changePassword');
$routes->post('profile/change-password', 'Profile::changePasswordPost');

// Auth
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginPost');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerPost');
$routes->get('logout', 'Auth::logout');

// Bookmark API
$routes->post('api/bookmark/toggle', 'BookmarkController::toggle');

// Rating API
$routes->post('api/rating',            'RatingController::rate');
$routes->get ('api/rating/(:num)',     'RatingController::stats/$1');

// Admin
$routes->group('admin', function ($routes) {
    $routes->get('/',                       'Admin::dashboard');

    // Manga
    $routes->get('manga',                   'Admin::mangaList');
    $routes->get('manga/new',               'Admin::newManga');
    $routes->post('manga/new',              'Admin::createManga');
    $routes->get('manga/(:num)/edit',       'Admin::editManga/$1');
    $routes->post('manga/(:num)/edit',      'Admin::updateManga/$1');
    $routes->post('manga/(:num)/push-s3',   'Admin::pushToS3/$1');

    // Chapters
    $routes->get ('manga/(:num)/chapters',          'Admin::chapterList/$1');
    $routes->get ('manga/(:num)/chapters/new',      'Admin::newChapter/$1');
    $routes->post('manga/(:num)/chapters/new',      'Admin::createChapter/$1');
    $routes->get ('chapters/(:num)/edit',           'Admin::editChapter/$1');
    $routes->post('chapters/(:num)/edit',           'Admin::updateChapter/$1');
    $routes->post('chapters/(:num)/delete',         'Admin::deleteChapter/$1');
    $routes->post('chapters/(:num)/toggle-show',    'Admin::toggleChapterShow/$1');

    // Pages
    $routes->post('chapters/(:num)/pages/add',         'Admin::addPages/$1');
    $routes->post('chapters/(:num)/push-pages-s3',     'Admin::pushPagesToS3/$1');
    $routes->post('chapters/(:num)/pages/bulk-delete', 'Admin::bulkDeletePages/$1');
    $routes->post('chapters/(:num)/pages/delete-all',  'Admin::deleteAllPages/$1');
    $routes->post('pages/(:num)/edit',                 'Admin::updatePage/$1');
    $routes->post('pages/(:num)/delete',               'Admin::deletePage/$1');

    // AJAX search (typeahead)
    $routes->get('api/authors',             'Admin::apiSearchAuthors');
    $routes->get('api/artists',             'Admin::apiSearchArtists');
    $routes->get('api/tags',                'Admin::apiSearchTags');
    $routes->get('users',               'Admin::users');
    $routes->get('users/(:num)/edit',   'Admin::editUser/$1');
    $routes->post('users/(:num)/edit',  'Admin::updateUser/$1');
    $routes->get('groups',              'Admin::groups');
    $routes->get('groups/new',          'Admin::newGroup');
    $routes->post('groups/new',         'Admin::createGroup');
    $routes->get('groups/(:num)/edit',  'Admin::editGroup/$1');
    $routes->post('groups/(:num)/edit', 'Admin::updateGroup/$1');
    $routes->post('groups/(:num)/delete','Admin::deleteGroup/$1');

    // Settings
    $routes->get ('settings',                   'Admin::settings');
    $routes->post('settings',                   'Admin::updateSettings');

    // Reports
    $routes->get ('reports',                    'Admin::reports');
    $routes->post('reports/(:num)/resolve',     'Admin::resolveReport/$1');

    // Categories
    $routes->get('categories',                  'Admin::categories');
    $routes->get('categories/new',              'Admin::newCategory');
    $routes->post('categories/new',             'Admin::createCategory');
    $routes->get('categories/(:num)/edit',      'Admin::editCategory/$1');
    $routes->post('categories/(:num)/edit',     'Admin::updateCategory/$1');
    $routes->post('categories/(:num)/delete',   'Admin::deleteCategory/$1');

    // Comic Types
    $routes->get('comictypes',                  'Admin::comicTypes');
    $routes->get('comictypes/new',              'Admin::newComicType');
    $routes->post('comictypes/new',             'Admin::createComicType');
    $routes->get('comictypes/(:num)/edit',      'Admin::editComicType/$1');
    $routes->post('comictypes/(:num)/edit',     'Admin::updateComicType/$1');
    $routes->post('comictypes/(:num)/delete',   'Admin::deleteComicType/$1');

    // Tags
    $routes->get('tags',                        'Admin::tags');
    $routes->get('tags/new',                    'Admin::newTag');
    $routes->post('tags/new',                   'Admin::createTag');
    $routes->get('tags/(:num)/edit',            'Admin::editTag/$1');
    $routes->post('tags/(:num)/edit',           'Admin::updateTag/$1');
    $routes->post('tags/(:num)/delete',         'Admin::deleteTag/$1');

    // Authors / Artists
    $routes->get('authors',                     'Admin::authors');
    $routes->get('authors/new',                 'Admin::newAuthor');
    $routes->post('authors/new',                'Admin::createAuthor');
    $routes->get('authors/(:num)/edit',         'Admin::editAuthor/$1');
    $routes->post('authors/(:num)/edit',        'Admin::updateAuthor/$1');
    $routes->post('authors/(:num)/delete',      'Admin::deleteAuthor/$1');
});

// Notifications page
$routes->get ('notifications',                   'NotificationController::listPage');

// Notification API
$routes->get ('api/notifications',               'NotificationController::index');
$routes->post('api/notifications/read-all',      'NotificationController::readAll');
$routes->post('api/notifications/(:num)/read',   'NotificationController::read/$1');

// Comment API
$routes->get ('api/comments/manga/(:num)',        'CommentController::byManga/$1');
$routes->get ('api/comments/manga/(:num)/all',   'CommentController::byMangaAll/$1');
$routes->get ('api/comments/chapter/(:num)',      'CommentController::byChapter/$1');
$routes->get ('api/comments/(:num)/replies',      'CommentController::replies/$1');
$routes->get ('api/captcha',                      'CommentController::captcha');
$routes->post('api/comments',                     'CommentController::create');
$routes->post('api/comments/(:num)/react',        'CommentController::react/$1');

// Chapter Report API
$routes->post('api/chapters/(:num)/report',       'ChapterReportController::report/$1');
