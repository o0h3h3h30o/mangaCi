<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    private function db(): BaseConnection
    {
        return \Config\Database::connect();
    }

    /** Returns null if user is admin, or redirect/403 response if not */
    private function guard(): ?ResponseInterface
    {
        if (!$this->currentUser) {
            return redirect()->to('/login');
        }

        $ok = $this->db()
            ->table('users_groups ug')
            ->join('groups g', 'g.id = ug.group_id')
            ->where('ug.user_id', $this->currentUser['id'])
            ->where('g.name', 'admin')
            ->countAllResults() > 0;

        if (!$ok) {
            return $this->response->setStatusCode(403)
                ->setBody('<p style="font:18px system-ui;text-align:center;padding:80px 0">403 — You do not have permission to access this area.</p>');
        }
        return null;
    }

    private function render(string $view, array $data = []): string
    {
        $data['currentUser'] = $this->currentUser;
        $data['content']     = view($view, $data);
        return view('admin/layout', $data);
    }

    // ── Site Settings ─────────────────────────────────────────

    public function settings(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;

        $settings = [];
        $tableError = false;
        try {
            $rows     = $this->db()->table('site_settings')->get()->getResultArray();
            $settings = array_column($rows, 'value', 'key');
        } catch (\Throwable $e) {
            $tableError = true;
        }

        // Liệt kê các theme có sẵn
        $themeDir = APPPATH . 'Views/themes/';
        $themes   = [];
        if (is_dir($themeDir)) {
            foreach (scandir($themeDir) as $item) {
                if ($item[0] !== '.' && is_dir($themeDir . $item)) {
                    $themes[] = $item;
                }
            }
        }

        return $this->response->setBody(
            $this->render('admin/settings', [
                'title'      => 'Site Settings',
                'activePage' => 'settings',
                'settings'   => $settings,
                'tableError' => $tableError,
                'themes'     => $themes,
            ])
        );
    }

    public function updateSettings(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db = $this->db();

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

        // Xử lý upload site logo
        $logoUrl = trim($this->request->getPost('site_logo') ?? '');
        $logoFile = $this->request->getFile('site_logo_file');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            if (in_array($logoFile->getMimeType(), $allowed)) {
                $newName = 'logo_' . time() . '.' . $logoFile->getExtension();
                $logoFile->move(FCPATH . 'images/', $newName);
                $logoUrl = base_url('images/' . $newName);
            }
        }

        // Xử lý upload footer logo
        $footerLogoUrl = trim($this->request->getPost('footer_logo') ?? '');
        $footerLogoFile = $this->request->getFile('footer_logo_file');
        if ($footerLogoFile && $footerLogoFile->isValid() && !$footerLogoFile->hasMoved()) {
            if (in_array($footerLogoFile->getMimeType(), $allowed)) {
                $newName = 'footer_logo_' . time() . '.' . $footerLogoFile->getExtension();
                $footerLogoFile->move(FCPATH . 'images/', $newName);
                $footerLogoUrl = base_url('images/' . $newName);
            }
        }

        $fields = ['site_title', 'meta_description', 'meta_keywords', 'footer_copyright', 'footer_url', 'active_theme', 'ga_id'];
        $values = ['site_logo' => $logoUrl, 'footer_logo' => $footerLogoUrl];
        foreach ($fields as $key) {
            $values[$key] = trim($this->request->getPost($key) ?? '');
        }

        foreach ($values as $key => $value) {
            $exists = $db->table('site_settings')->where('key', $key)->countAllResults() > 0;
            if ($exists) {
                $db->table('site_settings')->where('key', $key)->update(['value' => $value]);
            } else {
                $db->table('site_settings')->insert(['key' => $key, 'value' => $value]);
            }
        }

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Settings saved successfully.']);
        return redirect()->to('/admin/settings');
    }

    // ── Dashboard ─────────────────────────────────────────────

    public function dashboard(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db = $this->db();

        $stats = [
            'users'    => (int) $db->table('users')->countAllResults(),
            'manga'    => (int) $db->table('manga')->countAllResults(),
            'comments' => (int) $db->table('comments')->countAllResults(),
            'groups'   => (int) $db->table('groups')->countAllResults(),
        ];

        $recentUsers = $db->table('users')
            ->select('id, name, username, email, active, created_at, last_login')
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return $this->response->setBody(
            $this->render('admin/dashboard', [
                'title'       => 'Dashboard',
                'activePage'  => 'dashboard',
                'stats'       => $stats,
                'recentUsers' => $recentUsers,
            ])
        );
    }

    // ── Users ──────────────────────────────────────────────────

    public function users(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $q     = trim($this->request->getGet('q') ?? '');
        $gf    = (int) ($this->request->getGet('group') ?? 0);
        $page  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = 20;

        $conditions = [];
        $params     = [];

        if ($q !== '') {
            $conditions[] = '(u.name LIKE ? OR u.username LIKE ? OR u.email LIKE ?)';
            $like         = "%{$q}%";
            $params       = [...$params, $like, $like, $like];
        }
        if ($gf > 0) {
            $conditions[] = 'ug2.group_id = ?';
            $params[]     = $gf;
        }

        $where   = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $gfJoin  = $gf > 0 ? 'INNER JOIN users_groups ug2 ON ug2.user_id = u.id' : '';

        $countSql = "SELECT COUNT(DISTINCT u.id) AS cnt FROM users u {$gfJoin} {$where}";
        $total    = (int) $db->query($countSql, $params)->getRow()->cnt;

        $offset  = ($page - 1) * $limit;
        $dataSql = "
            SELECT u.id, u.name, u.username, u.email, u.active, u.last_login, u.created_at,
                   IFNULL(GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', '), '—') AS user_groups
            FROM users u
            {$gfJoin}
            LEFT JOIN users_groups ug ON ug.user_id = u.id
            LEFT JOIN groups g ON g.id = ug.group_id
            {$where}
            GROUP BY u.id ORDER BY u.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";
        $users = $db->query($dataSql, $params)->getResultArray();

        $groups = $db->table('groups')->orderBy('name')->get()->getResultArray();

        return $this->response->setBody(
            $this->render('admin/users_list', [
                'title'      => 'Users',
                'activePage' => 'users',
                'users'      => $users,
                'total'      => $total,
                'page'       => $page,
                'totalPages' => max(1, (int) ceil($total / $limit)),
                'limit'      => $limit,
                'q'          => $q,
                'gf'         => $gf,
                'groups'     => $groups,
            ])
        );
    }

    public function editUser(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db   = $this->db();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        if (!$user) return $this->response->setStatusCode(404)->setBody('User not found');

        $allGroups  = $db->table('groups')->orderBy('name')->get()->getResultArray();
        $userGroups = array_map('intval', array_column(
            $db->table('users_groups')->select('group_id')->where('user_id', $id)->get()->getResultArray(),
            'group_id'
        ));

        return $this->response->setBody(
            $this->render('admin/user_edit', [
                'title'      => 'Edit User #' . $id,
                'activePage' => 'users',
                'user'       => $user,
                'allGroups'  => $allGroups,
                'userGroups' => $userGroups,
                'flash'      => session()->getFlashdata('flash'),
            ])
        );
    }

    public function updateUser(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db   = $this->db();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        if (!$user) return $this->response->setStatusCode(404)->setBody('User not found');

        $name     = trim($this->request->getPost('name')     ?? '');
        $username = trim($this->request->getPost('username') ?? '');
        $email    = trim($this->request->getPost('email')    ?? '');
        $active   = (int) ($this->request->getPost('active') ?? 0);
        $newPass  = trim($this->request->getPost('password') ?? '');
        $groupIds = array_map('intval', (array) ($this->request->getPost('groups') ?? []));

        if ($name === '' || $username === '' || $email === '') {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Name, username, and email are required.']);
            return redirect()->to("/admin/users/{$id}/edit");
        }

        $update = ['name' => $name, 'username' => $username, 'email' => $email, 'active' => $active];
        if ($newPass !== '') {
            if (strlen($newPass) < 6) {
                session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Password must be at least 6 characters.']);
                return redirect()->to("/admin/users/{$id}/edit");
            }
            $update['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $db->table('users')->where('id', $id)->update($update);

        // Sync groups
        $db->table('users_groups')->where('user_id', $id)->delete();
        foreach ($groupIds as $gid) {
            if ($gid > 0) {
                $db->table('users_groups')->insert(['user_id' => $id, 'group_id' => $gid]);
            }
        }

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'User updated successfully.']);
        return redirect()->to("/admin/users/{$id}/edit");
    }

    // ── Groups ─────────────────────────────────────────────────

    public function groups(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;

        $groups = $this->db()->query(
            'SELECT g.id, g.name, COUNT(ug.user_id) AS member_count
             FROM groups g
             LEFT JOIN users_groups ug ON ug.group_id = g.id
             GROUP BY g.id ORDER BY g.name'
        )->getResultArray();

        return $this->response->setBody(
            $this->render('admin/groups_list', [
                'title'      => 'Groups',
                'activePage' => 'groups',
                'groups'     => $groups,
                'flash'      => session()->getFlashdata('flash'),
            ])
        );
    }

    public function newGroup(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        return $this->response->setBody(
            $this->render('admin/group_form', [
                'title'      => 'New Group',
                'activePage' => 'groups',
                'group'      => null,
                'flash'      => session()->getFlashdata('flash'),
            ])
        );
    }

    public function createGroup(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Group name is required.']);
            return redirect()->to('/admin/groups/new');
        }

        $this->db()->table('groups')->insert(['name' => $name]);
        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "Group \"{$name}\" created."]);
        return redirect()->to('/admin/groups');
    }

    public function editGroup(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $group = $this->db()->table('groups')->where('id', $id)->get()->getRowArray();
        if (!$group) return $this->response->setStatusCode(404)->setBody('Group not found');

        return $this->response->setBody(
            $this->render('admin/group_form', [
                'title'      => 'Edit Group',
                'activePage' => 'groups',
                'group'      => $group,
                'flash'      => session()->getFlashdata('flash'),
            ])
        );
    }

    public function updateGroup(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $group = $this->db()->table('groups')->where('id', $id)->get()->getRowArray();
        if (!$group) return redirect()->to('/admin/groups');

        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Group name is required.']);
            return redirect()->to("/admin/groups/{$id}/edit");
        }

        $this->db()->table('groups')->where('id', $id)->update(['name' => $name]);
        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Group updated.']);
        return redirect()->to('/admin/groups');
    }

    public function deleteGroup(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $group = $db->table('groups')->where('id', $id)->get()->getRowArray();
        if (!$group) return redirect()->to('/admin/groups');

        $db->table('users_groups')->where('group_id', $id)->delete();
        $db->table('groups')->where('id', $id)->delete();
        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "Group \"{$group['name']}\" deleted."]);
        return redirect()->to('/admin/groups');
    }

    // ── Shared helpers ─────────────────────────────────────────

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
        $text = preg_replace('/[\s]+/', '-', $text);
        return trim($text, '-');
    }

    private function taxonomyList(string $table, string $title, string $activePage, string $baseUrl, bool $hasSlug, ?string $countSql = null, string $nameCol = 'name'): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $q     = trim($this->request->getGet('q') ?? '');
        $page  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = 30;

        $where  = $q !== '' ? "WHERE `{$nameCol}` LIKE ?" : '';
        $params = $q !== '' ? ["%{$q}%"] : [];

        if ($countSql) {
            $slugCol = $hasSlug ? 't.slug,' : '';
            $sql = "SELECT t.id, t.`{$nameCol}` AS name, {$slugCol} ({$countSql}) AS manga_count
                    FROM `{$table}` t {$where} ORDER BY t.`{$nameCol}`
                    LIMIT {$limit} OFFSET " . (($page - 1) * $limit);
        } else {
            $slugCol = $hasSlug ? 'slug,' : '';
            $sql = "SELECT id, `{$nameCol}` AS name, {$slugCol} 0 AS manga_count FROM `{$table}` {$where} ORDER BY `{$nameCol}`
                    LIMIT {$limit} OFFSET " . (($page - 1) * $limit);
        }

        $items  = $this->db()->query($sql, $params)->getResultArray();
        $countQ = "SELECT COUNT(*) AS cnt FROM `{$table}` {$where}";
        $total  = (int) $this->db()->query($countQ, $params)->getRow()->cnt;

        return $this->response->setBody($this->render('admin/taxonomy_list', [
            'title'        => $title,
            'activePage'   => $activePage,
            'items'        => $items,
            'baseUrl'      => $baseUrl,
            'hasSlug'      => $hasSlug,
            'hasMangaCount'=> $countSql !== null,
            'q'            => $q,
            'page'         => $page,
            'totalPages'   => max(1, (int) ceil($total / $limit)),
            'total'        => $total,
            'flash'        => session()->getFlashdata('flash'),
        ]));
    }

    private function taxonomyForm(string $table, ?int $id, string $title, string $activePage, string $baseUrl, bool $hasSlug, string $nameCol = 'name'): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $item = null;
        if ($id) {
            $row = $this->db()->table($table)->where('id', $id)->get()->getRowArray();
            if (!$row) return $this->response->setStatusCode(404)->setBody('Not found');
            // Normalize: always expose 'name' key to view
            if ($nameCol !== 'name') $row['name'] = $row[$nameCol] ?? '';
            $item = $row;
        }
        return $this->response->setBody($this->render('admin/taxonomy_form', [
            'title'      => $title,
            'activePage' => $activePage,
            'item'       => $item,
            'baseUrl'    => $baseUrl,
            'hasSlug'    => $hasSlug,
            'flash'      => session()->getFlashdata('flash'),
        ]));
    }

    private function taxonomyCreate(string $table, string $baseUrl, bool $hasSlug, string $nameCol = 'name'): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $name = trim($this->request->getPost('name') ?? '');
        if (!$name) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Name is required.']);
            return redirect()->to("{$baseUrl}/new");
        }
        $row = [$nameCol => $name];
        if ($hasSlug) {
            $slug = trim($this->request->getPost('slug') ?? '') ?: $this->slugify($name);
            $row['slug'] = $slug;
        }
        $this->db()->table($table)->insert($row);
        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "\"{$name}\" created."]);
        return redirect()->to($baseUrl);
    }

    private function taxonomyUpdate(int $id, string $table, string $baseUrl, bool $hasSlug, string $nameCol = 'name'): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $name = trim($this->request->getPost('name') ?? '');
        if (!$name) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Name is required.']);
            return redirect()->to("{$baseUrl}/{$id}/edit");
        }
        $row = [$nameCol => $name];
        if ($hasSlug) {
            $slug = trim($this->request->getPost('slug') ?? '') ?: $this->slugify($name);
            $row['slug'] = $slug;
        }
        $this->db()->table($table)->where('id', $id)->update($row);
        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Updated successfully.']);
        return redirect()->to($baseUrl);
    }

    private function taxonomyDelete(int $id, string $table, string $baseUrl, array $cleanJunctions = []): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db = $this->db();
        foreach ($cleanJunctions as [$jTable, $col]) {
            $db->table($jTable)->where($col, $id)->delete();
        }
        $db->table($table)->where('id', $id)->delete();
        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Deleted.']);
        return redirect()->to($baseUrl);
    }

    // ── Categories (Genres) ────────────────────────────────────

    public function categories(): ResponseInterface
    {
        return $this->taxonomyList(
            'category', 'Categories', 'categories', '/admin/categories', true,
            'SELECT COUNT(*) FROM category_manga cm WHERE cm.category_id = t.id'
        );
    }
    public function newCategory(): ResponseInterface    { return $this->taxonomyForm('category', null,  'New Category',  'categories', '/admin/categories', true); }
    public function createCategory(): ResponseInterface  { return $this->taxonomyCreate('category', '/admin/categories', true); }
    public function editCategory(int $id): ResponseInterface   { return $this->taxonomyForm('category', $id, 'Edit Category', 'categories', '/admin/categories', true); }
    public function updateCategory(int $id): ResponseInterface { return $this->taxonomyUpdate($id, 'category', '/admin/categories', true); }
    public function deleteCategory(int $id): ResponseInterface { return $this->taxonomyDelete($id, 'category', '/admin/categories', [['category_manga', 'category_id']]); }

    // ── Comic Types ────────────────────────────────────────────

    public function comicTypes(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        try {
            return $this->taxonomyList(
                'comictype', 'Comic Types', 'comictypes', '/admin/comictypes', false,
                'SELECT COUNT(*) FROM manga m WHERE m.type_id = t.id',
                'label'
            );
        } catch (\Exception $e) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Error loading Comic Types: ' . $e->getMessage()]);
            return redirect()->to('/admin');
        }
    }
    public function newComicType(): ResponseInterface    { return $this->taxonomyForm('comictype', null,  'New Comic Type',  'comictypes', '/admin/comictypes', false, 'label'); }
    public function createComicType(): ResponseInterface  { return $this->taxonomyCreate('comictype', '/admin/comictypes', false, 'label'); }
    public function editComicType(int $id): ResponseInterface   { return $this->taxonomyForm('comictype', $id, 'Edit Comic Type', 'comictypes', '/admin/comictypes', false, 'label'); }
    public function updateComicType(int $id): ResponseInterface { return $this->taxonomyUpdate($id, 'comictype', '/admin/comictypes', false, 'label'); }
    public function deleteComicType(int $id): ResponseInterface { return $this->taxonomyDelete($id, 'comictype', '/admin/comictypes'); }

    // ── Tags ───────────────────────────────────────────────────

    public function tags(): ResponseInterface
    {
        return $this->taxonomyList('tag', 'Tags', 'tags', '/admin/tags', true);
    }
    public function newTag(): ResponseInterface    { return $this->taxonomyForm('tag', null,  'New Tag',  'tags', '/admin/tags', true); }
    public function createTag(): ResponseInterface  { return $this->taxonomyCreate('tag', '/admin/tags', true); }
    public function editTag(int $id): ResponseInterface   { return $this->taxonomyForm('tag', $id, 'Edit Tag', 'tags', '/admin/tags', true); }
    public function updateTag(int $id): ResponseInterface { return $this->taxonomyUpdate($id, 'tag', '/admin/tags', true); }
    public function deleteTag(int $id): ResponseInterface { return $this->taxonomyDelete($id, 'tag', '/admin/tags'); }

    // ── Authors / Artists ──────────────────────────────────────

    public function authors(): ResponseInterface
    {
        return $this->taxonomyList(
            'author', 'Authors / Artists', 'authors', '/admin/authors', true,
            'SELECT COUNT(*) FROM author_manga am WHERE am.author_id = t.id'
        );
    }
    public function newAuthor(): ResponseInterface    { return $this->taxonomyForm('author', null,  'New Author',  'authors', '/admin/authors', true); }
    public function createAuthor(): ResponseInterface  { return $this->taxonomyCreate('author', '/admin/authors', true); }
    public function editAuthor(int $id): ResponseInterface   { return $this->taxonomyForm('author', $id, 'Edit Author', 'authors', '/admin/authors', true); }
    public function updateAuthor(int $id): ResponseInterface { return $this->taxonomyUpdate($id, 'author', '/admin/authors', true); }
    public function deleteAuthor(int $id): ResponseInterface { return $this->taxonomyDelete($id, 'author', '/admin/authors', [['author_manga', 'author_id']]); }

    // ── AJAX search APIs ───────────────────────────────────────

    public function apiSearchAuthors(): ResponseInterface
    {
        if (!$this->currentUser) return $this->response->setStatusCode(401)->setJSON([]);
        $q   = trim($this->request->getGet('q') ?? '');
        $sql = $q !== ''
            ? "SELECT id, name FROM author WHERE name LIKE ? ORDER BY name LIMIT 15"
            : "SELECT id, name FROM author ORDER BY name LIMIT 15";
        $rows = $this->db()->query($sql, $q !== '' ? ["%{$q}%"] : [])->getResultArray();
        return $this->response->setJSON($rows);
    }

    public function apiSearchArtists(): ResponseInterface
    {
        if (!$this->currentUser) return $this->response->setStatusCode(401)->setJSON([]);
        $q   = trim($this->request->getGet('q') ?? '');
        $sql = $q !== ''
            ? "SELECT id, name FROM author WHERE name LIKE ? ORDER BY name LIMIT 15"
            : "SELECT id, name FROM author ORDER BY name LIMIT 15";
        $rows = $this->db()->query($sql, $q !== '' ? ["%{$q}%"] : [])->getResultArray();
        return $this->response->setJSON($rows);
    }

    public function apiSearchTags(): ResponseInterface
    {
        if (!$this->currentUser) return $this->response->setStatusCode(401)->setJSON([]);
        $q   = trim($this->request->getGet('q') ?? '');
        $sql = $q !== ''
            ? "SELECT id, name FROM tag WHERE name LIKE ? ORDER BY name LIMIT 15"
            : "SELECT id, name FROM tag ORDER BY name LIMIT 15";
        $rows = $this->db()->query($sql, $q !== '' ? ["%{$q}%"] : [])->getResultArray();
        return $this->response->setJSON($rows);
    }

    // ── Manga List ─────────────────────────────────────────────

    public function mangaList(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $q     = trim($this->request->getGet('q') ?? '');
        $sf    = (int) ($this->request->getGet('status') ?? 0);
        $pf    = $this->request->getGet('pub') ?? '';
        $page  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = 20;

        $conds  = [];
        $params = [];

        if ($q !== '') {
            $conds[]  = '(m.name LIKE ? OR m.otherNames LIKE ? OR m.slug LIKE ?)';
            $like     = "%{$q}%";
            $params   = [...$params, $like, $like, $like];
        }
        if ($sf > 0)     { $conds[] = 'm.status_id = ?';  $params[] = $sf; }
        if ($pf === '1') { $conds[] = 'm.is_public = 1'; }
        if ($pf === '0') { $conds[] = 'm.is_public = 0'; }

        $where  = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $offset = ($page - 1) * $limit;

        $countSql = "SELECT COUNT(DISTINCT m.id) AS cnt FROM manga m {$where}";
        $total    = (int) $db->query($countSql, $params)->getRow()->cnt;

        $dataSql = "
            SELECT m.id, m.name, m.slug, m.status_id, m.is_public, m.views, m.update_at,
                   IFNULL(GROUP_CONCAT(DISTINCT c.name  ORDER BY c.name  SEPARATOR ', '), '') AS categories,
                   IFNULL(GROUP_CONCAT(DISTINCT IF(am.type=1, a.name, NULL) ORDER BY a.name SEPARATOR ', '), '') AS authors,
                   IFNULL(GROUP_CONCAT(DISTINCT IF(am.type=2, a.name, NULL) ORDER BY a.name SEPARATOR ', '), '') AS artists
            FROM manga m
            LEFT JOIN category_manga cm ON cm.manga_id = m.id
            LEFT JOIN category c ON c.id = cm.category_id
            LEFT JOIN author_manga am ON am.manga_id = m.id
            LEFT JOIN author a ON a.id = am.author_id
            {$where}
            GROUP BY m.id ORDER BY m.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";
        $items = $db->query($dataSql, $params)->getResultArray();

        // Statuses
        try { $statuses = $db->table('status')->orderBy('id')->get()->getResultArray(); }
        catch (\Exception $e) { $statuses = [['id'=>1,'name'=>'Ongoing'],['id'=>2,'name'=>'Completed']]; }

        return $this->response->setBody($this->render('admin/manga_list', [
            'title'      => 'Manga',
            'activePage' => 'manga',
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => max(1, (int) ceil($total / $limit)),
            'q'          => $q,
            'sf'         => $sf,
            'pf'         => $pf,
            'statuses'   => $statuses,
            'cdnBase'    => rtrim(env('CDN_COVER_URL', ''), '/'),
            'flash'      => session()->getFlashdata('flash'),
        ]));
    }

    // ── Manga Edit / Create ────────────────────────────────────

    private function loadMangaFormData(): array
    {
        $db = $this->db();

        // Statuses
        try { $statuses = $db->table('status')->orderBy('id')->get()->getResultArray(); }
        catch (\Exception $e) { $statuses = [['id'=>1,'name'=>'Ongoing'],['id'=>2,'name'=>'Completed']]; }

        $categories = $db->table('category')->orderBy('name')->get()->getResultArray();

        try { $comictypes = $db->table('comictype')->orderBy('id')->get()->getResultArray(); }
        catch (\Exception $e) { $comictypes = []; }

        return compact('statuses', 'categories', 'comictypes');
    }

    public function newManga(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $fd = $this->loadMangaFormData();
        return $this->response->setBody($this->render('admin/manga_form', array_merge($fd, [
            'title'      => 'New Manga',
            'activePage' => 'manga',
            'manga'        => null,
            'mangaCats'    => [],
            'mangaAuthors' => [],
            'mangaArtists' => [],
            'mangaTags'    => [],
            'flash'      => session()->getFlashdata('flash'),
        ])));
    }

    public function createManga(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db   = $this->db();
        $name = trim($this->request->getPost('name') ?? '');
        if (!$name) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Name is required.']);
            return redirect()->to('/admin/manga/new');
        }

        $slug = trim($this->request->getPost('slug') ?? '') ?: $this->slugify($name);

        // Handle image upload — file will be saved after insert (need ID for filename)
        $coverCdn  = (int) ($this->request->getPost('cover_cdn') ?? 0);
        $imageUrl  = trim($this->request->getPost('image_url') ?? '');
        $imageFile = $this->request->getFile('image_file');
        $pendingImageFile = null;
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imgTypes = ['image/jpeg','image/png','image/gif','image/webp'];
            if (in_array($imageFile->getMimeType(), $imgTypes)) {
                $pendingImageFile = $imageFile;
                $coverCdn = 0;
            }
        }

        $row = [
            'name'           => $name,
            'slug'           => $slug,
            'otherNames'     => trim($this->request->getPost('otherNames') ?? ''),
            'summary'        => trim($this->request->getPost('summary') ?? ''),
            'status_id'      => (int) ($this->request->getPost('status_id') ?? 1),
            'is_public'      => (int) ($this->request->getPost('is_public') ?? 0),
            'caution'        => (int) ($this->request->getPost('caution') ?? 0),
            'from_manga18fx' => trim($this->request->getPost('from_manga18fx') ?? ''),
            'cover'          => $coverCdn,
            'image'          => ($coverCdn || $pendingImageFile) ? '' : $imageUrl,
            'type_id'        => ($t = $this->request->getPost('type_id')) ? (int) $t : null,
            'views'          => 0, 'view_day' => 0, 'view_month' => 0,
            'update_at'      => date('Y-m-d H:i:s'),
        ];

        $db->table('manga')->insert($row);
        $mangaId = $db->insertID();

        // Save uploaded image with ID-based filename
        if ($pendingImageFile) {
            $newName = $mangaId . '-thumb.' . $pendingImageFile->getExtension();
            $pendingImageFile->move(FCPATH . 'images/', $newName, true);
            $imageUrl = base_url('images/' . $newName);
            $db->table('manga')->where('id', $mangaId)->update(['image' => $imageUrl]);
        }

        $this->syncMangaRelations($db, $mangaId);

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "Manga \"{$name}\" created."]);
        return redirect()->to("/admin/manga/{$mangaId}/edit");
    }

    public function editManga(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $id)->get()->getRowArray();
        if (!$manga) return $this->response->setStatusCode(404)->setBody('Manga not found');

        $fd = $this->loadMangaFormData();

        $mangaCats = array_column(
            $db->query('SELECT c.id, c.name FROM category c JOIN category_manga cm ON c.id=cm.category_id WHERE cm.manga_id=?', [$id])->getResultArray(),
            'name', 'id'
        );
        try {
            // type=1 → author; type=0/NULL → treat as author (migration fallback); type=2 → artist
            $mangaAuthors = $db->query(
                'SELECT a.id, a.name FROM author a JOIN author_manga am ON a.id=am.author_id
                 WHERE am.manga_id=? AND (am.type IS NULL OR am.type IN (0,1)) ORDER BY a.name',
                [$id]
            )->getResultArray();
            $mangaArtists = $db->query(
                'SELECT a.id, a.name FROM author a JOIN author_manga am ON a.id=am.author_id
                 WHERE am.manga_id=? AND am.type=2 ORDER BY a.name',
                [$id]
            )->getResultArray();
        } catch (\Exception $e) {
            // type column doesn't exist yet — show all as authors
            $mangaAuthors = $db->query(
                'SELECT a.id, a.name FROM author a JOIN author_manga am ON a.id=am.author_id
                 WHERE am.manga_id=? ORDER BY a.name',
                [$id]
            )->getResultArray();
            $mangaArtists = [];
        }

        $mangaTags = [];
        try {
            $mangaTags = $db->query('SELECT t.id, t.name FROM tag t JOIN manga_tag mt ON t.id=mt.tag_id WHERE mt.manga_id=?', [$id])->getResultArray();
        } catch (\Exception $e) {}

        return $this->response->setBody($this->render('admin/manga_form', array_merge($fd, [
            'title'        => 'Edit: ' . $manga['name'],
            'activePage'   => 'manga',
            'manga'        => $manga,
            'mangaCats'    => array_keys($mangaCats),
            'mangaAuthors' => $mangaAuthors,
            'mangaArtists' => $mangaArtists,
            'mangaTags'    => $mangaTags,
            'flash'        => session()->getFlashdata('flash'),
        ])));
    }

    public function updateManga(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $id)->get()->getRowArray();
        if (!$manga) return $this->response->setStatusCode(404)->setBody('Manga not found');

        $name = trim($this->request->getPost('name') ?? '');
        if (!$name) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Name is required.']);
            return redirect()->to("/admin/manga/{$id}/edit");
        }
        $slug = trim($this->request->getPost('slug') ?? '') ?: $this->slugify($name);

        // Handle image upload
        $coverCdn  = (int) ($this->request->getPost('cover_cdn') ?? 0);
        $imageUrl  = trim($this->request->getPost('image_url') ?? '');
        $imageFile = $this->request->getFile('image_file');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imgTypes = ['image/jpeg','image/png','image/gif','image/webp'];
            if (in_array($imageFile->getMimeType(), $imgTypes)) {
                $newName = $id . '-thumb.' . $imageFile->getExtension();
                $imageFile->move(FCPATH . 'images/', $newName, true);
                $imageUrl = base_url('images/' . $newName);
                $coverCdn = 0;
            }
        }

        $row = [
            'name'           => $name,
            'slug'           => $slug,
            'otherNames'     => trim($this->request->getPost('otherNames') ?? ''),
            'summary'        => trim($this->request->getPost('summary') ?? ''),
            'status_id'      => (int) ($this->request->getPost('status_id') ?? 1),
            'is_public'      => (int) ($this->request->getPost('is_public') ?? 0),
            'caution'        => (int) ($this->request->getPost('caution') ?? 0),
            'from_manga18fx' => trim($this->request->getPost('from_manga18fx') ?? ''),
            'cover'          => $coverCdn,
            'image'          => $coverCdn ? '' : $imageUrl,
            'type_id'        => ($t = $this->request->getPost('type_id')) ? (int) $t : null,
            'update_at'      => date('Y-m-d H:i:s'),
        ];

        try {
            $db->table('manga')->where('id', $id)->update($row);
        } catch (\Exception $e) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'DB Error: ' . $e->getMessage()]);
            return redirect()->to("/admin/manga/{$id}/edit");
        }

        try {
            $this->syncMangaRelations($db, $id);
        } catch (\Exception $e) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Manga saved but relations error: ' . $e->getMessage()]);
            return redirect()->to("/admin/manga/{$id}/edit");
        }

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Manga updated.']);
        return redirect()->to("/admin/manga/{$id}/edit");
    }

    public function deleteManga(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $id)->get()->getRowArray();
        if (!$manga) return $this->response->setStatusCode(404)->setBody('Manga not found');

        $name = $manga['name'];

        // 1. Delete cover images (local files)
        foreach (['', '-thumb'] as $suffix) {
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
                $path = FCPATH . "images/{$id}{$suffix}.{$ext}";
                if (is_file($path)) @unlink($path);
            }
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
                $path = FCPATH . "cover/{$id}{$suffix}.{$ext}";
                if (is_file($path)) @unlink($path);
            }
        }

        // 2. Delete all pages of all chapters
        $chapterIds = array_column(
            $db->query('SELECT id FROM chapter WHERE manga_id = ?', [$id])->getResultArray(),
            'id'
        );
        if ($chapterIds) {
            $in = implode(',', $chapterIds);
            $db->query("DELETE FROM page WHERE chapter_id IN ({$in})");
        }

        // 3. Delete chapters
        $db->table('chapter')->where('manga_id', $id)->delete();

        // 4. Delete relations
        try { $db->table('category_manga')->where('manga_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('author_manga')->where('manga_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('manga_tag')->where('manga_id', $id)->delete(); } catch (\Throwable $e) {}

        // 5. Delete bookmarks, comments, ratings, content_likes, notifications
        try { $db->table('bookmarks')->where('manga_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('comments')->where('manga_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('item_ratings')->where('item_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('content_likes')->where('content_type', 'manga')->where('content_id', $id)->delete(); } catch (\Throwable $e) {}
        try { $db->table('notifications')->where('manga_id', $id)->delete(); } catch (\Throwable $e) {}

        // 6. Delete content_likes for chapters
        if ($chapterIds) {
            try { $db->table('content_likes')->where('content_type', 'chapter')->whereIn('content_id', $chapterIds)->delete(); } catch (\Throwable $e) {}
            try { $db->table('chapter_reports')->whereIn('chapter_id', $chapterIds)->delete(); } catch (\Throwable $e) {}
        }

        // 7. Delete manga
        $db->table('manga')->where('id', $id)->delete();

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "Manga \"{$name}\" and all related data deleted."]);
        return redirect()->to('/admin/manga');
    }

    /** Find or create an author by name, return ID */
    private function findOrCreateAuthor(BaseConnection $db, array $a): int
    {
        $aId = isset($a['id']) ? (int) $a['id'] : 0;
        if (!$aId && !empty($a['name'])) {
            $aName    = trim($a['name']);
            $existing = $db->table('author')->where('name', $aName)->get()->getRowArray();
            if ($existing) {
                $aId = (int) $existing['id'];
            } else {
                $db->table('author')->insert(['name' => $aName, 'slug' => $this->slugify($aName)]);
                $aId = (int) $db->insertID();
            }
        }
        return $aId;
    }

    private function insertAuthorManga(BaseConnection $db, int $mangaId, int $authorId, int $type): void
    {
        try {
            $db->table('author_manga')->insert(['manga_id' => $mangaId, 'author_id' => $authorId, 'type' => $type]);
        } catch (\Exception $e) {
            // type column doesn't exist — insert without it
            $db->table('author_manga')->insert(['manga_id' => $mangaId, 'author_id' => $authorId]);
        }
    }

    private function syncMangaRelations(BaseConnection $db, int $mangaId): void
    {
        // Authors (type=1) + Artists (type=2)
        $db->table('author_manga')->where('manga_id', $mangaId)->delete();

        $authorsJson = $this->request->getPost('authors_data') ?? '[]';
        foreach ((json_decode($authorsJson, true) ?? []) as $a) {
            $aId = $this->findOrCreateAuthor($db, $a);
            if ($aId > 0) $this->insertAuthorManga($db, $mangaId, $aId, 1);
        }

        $artistsJson = $this->request->getPost('artists_data') ?? '[]';
        foreach ((json_decode($artistsJson, true) ?? []) as $a) {
            $aId = $this->findOrCreateAuthor($db, $a);
            if ($aId > 0) $this->insertAuthorManga($db, $mangaId, $aId, 2);
        }

        // Categories
        $db->table('category_manga')->where('manga_id', $mangaId)->delete();
        foreach (array_map('intval', (array) ($this->request->getPost('categories') ?? [])) as $cId) {
            if ($cId > 0) $db->table('category_manga')->insert(['manga_id' => $mangaId, 'category_id' => $cId]);
        }

        // Tags
        $tagsJson = $this->request->getPost('tags_data') ?? '[]';
        $tags = json_decode($tagsJson, true) ?? [];
        try {
            $db->table('manga_tag')->where('manga_id', $mangaId)->delete();
            foreach ($tags as $t) {
                $tId = isset($t['id']) ? (int) $t['id'] : 0;
                if (!$tId && !empty($t['name'])) {
                    $tName = trim($t['name']);
                    $existing = $db->table('tag')->where('name', $tName)->get()->getRowArray();
                    if ($existing) {
                        $tId = (int) $existing['id'];
                    } else {
                        try {
                            $db->table('tag')->insert(['name' => $tName, 'slug' => $this->slugify($tName)]);
                        } catch (\Exception $e) {
                            $db->table('tag')->insert(['name' => $tName]);
                        }
                        $tId = (int) $db->insertID();
                    }
                }
                if ($tId > 0) $db->table('manga_tag')->insert(['manga_id' => $mangaId, 'tag_id' => $tId]);
            }
        } catch (\Exception $e) {
            log_message('error', 'syncMangaRelations tags: ' . $e->getMessage());
            throw $e;
        }
    }

    // ── Chapter CRUD ──────────────────────────────────────────────────────────

    public function chapterList(int $mangaId): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $mangaId)->get()->getRowArray();
        if (!$manga) return $this->response->setStatusCode(404)->setBody('Manga not found');

        $q     = trim($this->request->getGet('q') ?? '');
        $page  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = 50;

        $builder = $db->table('chapter c')
            ->select('c.*, (SELECT COUNT(*) FROM page p WHERE p.chapter_id = c.id) AS page_count')
            ->where('c.manga_id', $mangaId);

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('c.number', $q)
                ->groupEnd();
        }

        $total      = $builder->countAllResults(false);
        $totalPages = (int) ceil($total / $limit);
        $items      = $builder->orderBy('c.number', 'DESC')->limit($limit, ($page - 1) * $limit)->get()->getResultArray();

        return $this->response->setBody($this->render('admin/chapter_list', [
            'title'      => 'Chapters: ' . $manga['name'],
            'activePage' => 'manga',
            'manga'      => $manga,
            'items'      => $items,
            'total'      => $total,
            'totalPages' => $totalPages,
            'page'       => $page,
            'q'          => $q,
            'flash'      => session()->getFlashdata('flash'),
        ]));
    }

    public function newChapter(int $mangaId): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $mangaId)->get()->getRowArray();
        if (!$manga) return $this->response->setStatusCode(404)->setBody('Manga not found');

        return $this->response->setBody($this->render('admin/chapter_form', [
            'title'      => 'New Chapter — ' . $manga['name'],
            'activePage' => 'manga',
            'manga'      => $manga,
            'chapter'    => null,
            'flash'      => session()->getFlashdata('flash'),
        ]));
    }

    public function createChapter(int $mangaId): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $mangaId)->get()->getRowArray();
        if (!$manga) return $this->response->setStatusCode(404)->setBody('Manga not found');

        $number = $this->request->getPost('number');
        if ($number === null || $number === '') {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Chapter number is required.']);
            return redirect()->to("/admin/manga/{$mangaId}/chapters/new");
        }

        $number = (float) $number;
        $name   = trim($this->request->getPost('name') ?? '');
        $slug   = trim($this->request->getPost('slug') ?? '')
            ?: ($name ? $this->slugify($name) : ('chapter-' . rtrim(rtrim(number_format($number, 1), '0'), '.')));

        $row = [
            'manga_id'    => $mangaId,
            'number'      => $number,
            'slug'        => $slug,
            'name'        => $name,
            'is_show'     => (int) ($this->request->getPost('is_show') ?? 0),
            'is_crawling' => (int) ($this->request->getPost('is_crawling') ?? 0),
            'source_url'  => trim($this->request->getPost('source_url') ?? ''),
            'view'        => 0,
        ];

        try {
            $db->table('chapter')->insert($row);
        } catch (\Exception $e) {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Error: ' . $e->getMessage()]);
            return redirect()->to("/admin/manga/{$mangaId}/chapters/new");
        }

        // Update manga update_at
        $db->table('manga')->where('id', $mangaId)->update(['update_at' => date('Y-m-d H:i:s')]);

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "Chapter {$number} created."]);
        return redirect()->to("/admin/manga/{$mangaId}/chapters");
    }

    public function editChapter(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db      = $this->db();
        $chapter = $db->query(
            'SELECT c.*, (SELECT COUNT(*) FROM page p WHERE p.chapter_id = c.id) AS page_count FROM chapter c WHERE c.id = ?',
            [$id]
        )->getRowArray();
        if (!$chapter) return $this->response->setStatusCode(404)->setBody('Chapter not found');

        $manga = $db->table('manga')->where('id', $chapter['manga_id'])->get()->getRowArray();
        $pages = $db->table('page')->where('chapter_id', $id)->orderBy('slug', 'ASC')->get()->getResultArray();

        return $this->response->setBody($this->render('admin/chapter_form', [
            'title'      => 'Edit Chapter ' . $chapter['number'],
            'activePage' => 'manga',
            'manga'      => $manga,
            'chapter'    => $chapter,
            'pages'      => $pages,
            'flash'      => session()->getFlashdata('flash'),
        ]));
    }

    public function updateChapter(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db      = $this->db();
        $chapter = $db->table('chapter')->where('id', $id)->get()->getRowArray();
        if (!$chapter) return $this->response->setStatusCode(404)->setBody('Chapter not found');

        $number = $this->request->getPost('number');
        if ($number === null || $number === '') {
            session()->setFlashdata('flash', ['type' => 'error', 'msg' => 'Chapter number is required.']);
            return redirect()->to("/admin/chapters/{$id}/edit");
        }

        $number = (float) $number;
        $name   = trim($this->request->getPost('name') ?? '');
        $slug   = trim($this->request->getPost('slug') ?? '')
            ?: ($name ? $this->slugify($name) : ('chapter-' . rtrim(rtrim(number_format($number, 1), '0'), '.')));

        $db->table('chapter')->where('id', $id)->update([
            'number'      => $number,
            'slug'        => $slug,
            'name'        => $name,
            'is_show'     => (int) ($this->request->getPost('is_show') ?? 0),
            'is_crawling' => (int) ($this->request->getPost('is_crawling') ?? 0),
            'source_url'  => trim($this->request->getPost('source_url') ?? ''),
        ]);

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Chapter updated.']);
        return redirect()->to("/admin/chapters/{$id}/edit");
    }

    public function toggleChapterShow(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db      = $this->db();
        $chapter = $db->table('chapter')->where('id', $id)->get()->getRowArray();
        if (!$chapter) return $this->response->setJSON(['ok' => false]);

        $isShow = (int) ($this->request->getPost('is_show') ?? 0);
        $db->table('chapter')->where('id', $id)->update(['is_show' => $isShow]);
        return $this->response->setJSON(['ok' => true, 'is_show' => $isShow]);
    }

    public function deleteChapter(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db      = $this->db();
        $chapter = $db->table('chapter')->where('id', $id)->get()->getRowArray();
        if (!$chapter) return $this->response->setStatusCode(404)->setBody('Chapter not found');

        $mangaId = $chapter['manga_id'];
        $db->table('chapter')->where('id', $id)->delete();

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "Chapter {$chapter['number']} deleted."]);
        return redirect()->to("/admin/manga/{$mangaId}/chapters");
    }

    // ── Page CRUD ─────────────────────────────────────────────────────────────

    public function addPages(int $chapterId): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db      = $this->db();
        $chapter = $db->table('chapter')->where('id', $chapterId)->get()->getRowArray();
        if (!$chapter) return $this->response->setStatusCode(404)->setBody('Chapter not found');

        $sourceType = $this->request->getPost('source_type') ?? 'cdn';
        $startSlug  = max(1, (int) ($this->request->getPost('start_slug') ?? 1));
        $added      = 0;

        // ── Local file upload ───────────────────────────────────────
        if ($sourceType === 'local') {
            $files = $this->request->getFiles()['page_files'] ?? [];
            if (!is_array($files)) $files = [$files];

            // Sắp xếp theo tên file gốc để giữ thứ tự đúng
            usort($files, fn($a, $b) => strcmp($a->getClientName(), $b->getClientName()));

            $uploadDir = FCPATH . 'uploads/chapters/' . $chapterId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($files as $i => $file) {
                if (!$file || !$file->isValid() || $file->hasMoved()) continue;

                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedMimes)) continue;

                $ext      = strtolower($file->getClientExtension() ?: 'jpg');
                $slug     = $startSlug + $i;
                $filename = str_pad($slug, 3, '0', STR_PAD_LEFT) . '.' . $ext;

                $file->move($uploadDir, $filename, true);

                $storedPath = '/uploads/chapters/' . $chapterId . '/' . $filename;
                $row = [
                    'chapter_id'  => $chapterId,
                    'slug'        => $slug,
                    'image'       => $storedPath,
                    'image_local' => '',
                    'external'    => 0,
                ];
                try { $db->table('page')->insert($row); $added++; } catch (\Exception $e) {}
            }

            session()->setFlashdata('flash', ['type' => 'success', 'msg' => "{$added} file(s) uploaded and added."]);
            return redirect()->to("/admin/chapters/{$chapterId}/edit");
        }

        // ── CDN S3 / External URL / paths (text-based) ─────────────
        $raw      = trim($this->request->getPost('urls') ?? '');
        $isCdn    = (bool) $this->request->getPost('is_cdn');
        $external = (int)  $this->request->getPost('external');

        $urls = array_filter(array_map('trim', explode("\n", $raw)));
        foreach ($urls as $i => $url) {
            if (!$url) continue;
            $slug = $startSlug + $i;
            $row  = ['chapter_id' => $chapterId, 'slug' => $slug];
            if ($isCdn) {
                $row['image_local'] = ltrim(basename($url), '/');
                $row['image']       = '';
                $row['external']    = 0;
            } else {
                $row['image']       = $url;
                $row['image_local'] = '';
                $row['external']    = $external;
            }
            try { $db->table('page')->insert($row); $added++; } catch (\Exception $e) {}
        }

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => "{$added} page(s) added."]);
        return redirect()->to("/admin/chapters/{$chapterId}/edit");
    }

    /**
     * AJAX: upload 1 page local lên S3, cập nhật DB, xoá file local.
     * POST /admin/chapters/{id}/push-pages-s3  body: page_id
     */
    public function pushPagesToS3(int $chapterId): ResponseInterface
    {
        if ($r = $this->guard()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(401);
        }

        $db      = $this->db();
        $chapter = $db->table('chapter')->where('id', $chapterId)->get()->getRowArray();
        if (!$chapter) {
            return $this->response->setJSON(['success' => false, 'error' => 'Chapter not found'])->setStatusCode(404);
        }

        $pageId = (int) $this->request->getPost('page_id');
        $page   = $db->table('page')->where('id', $pageId)->where('chapter_id', $chapterId)->get()->getRowArray();
        if (!$page) {
            return $this->response->setJSON(['success' => false, 'error' => 'Page not found']);
        }

        $imageUrl  = $page['image']    ?? '';
        $external  = (int)($page['external'] ?? 0);
        $imageData = null;
        $mimeType  = 'image/jpeg';
        $basename  = null;
        $localPath = null;

        $mimeMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
        $extMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

        if ($external === 0 && str_starts_with($imageUrl, '/uploads/')) {
            // ── Local file ──────────────────────────────────────────
            $localPath = FCPATH . ltrim($imageUrl, '/');
            if (!file_exists($localPath)) {
                return $this->response->setJSON(['success' => false, 'error' => 'File missing: ' . $imageUrl]);
            }
            $imageData = file_get_contents($localPath);
            $ext       = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
            $mimeType  = $mimeMap[$ext] ?? 'image/jpeg';

        } elseif ($external === 1 && !empty($imageUrl)) {
            // ── External URL: download tạm rồi push ─────────────────
            $ch = curl_init($imageUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; MangaBot/1.0)',
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $body     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $ct       = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($httpCode !== 200 || !$body) {
                return $this->response->setJSON(['success' => false, 'error' => "Download failed (HTTP {$httpCode})"]);
            }

            $imageData = $body;
            $mimeType  = explode(';', trim($ct))[0] ?: 'image/jpeg';
            // Lấy ext từ URL, fallback từ MIME
            $urlExt = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
            $ext    = $urlExt ?: ($extMap[$mimeType] ?? 'jpg');

        } else {
            return $this->response->setJSON(['success' => false, 'error' => 'Not a pushable page']);
        }

        // Tên file = slug.ext (e.g. 1.jpg, 10.jpg)
        $basename = $page['slug'] . '.' . ($ext ?: 'jpg');

        // Upload lên S3: chapter/{chapterId}/{slug}.ext
        $s3Key = 'chapter/' . $chapterId . '/' . $basename;
        if (!$this->s3PutObject($imageData, $s3Key, $mimeType)) {
            return $this->response->setJSON(['success' => false, 'error' => 'S3 upload failed']);
        }

        // Cập nhật DB sang CDN pattern
        $db->table('page')->where('id', $pageId)->update([
            'image_local' => $basename,
            'image'       => '',
            'external'    => 0,
        ]);

        // Xoá file local (chỉ áp dụng local upload, external URL không có file local)
        if ($localPath) {
            @unlink($localPath);
            $dir   = dirname($localPath);
            $files = array_diff(scandir($dir) ?: [], ['.', '..']);
            if (empty($files)) {
                @rmdir($dir);
            }
        }

        return $this->response->setJSON(['success' => true, 'basename' => $basename]);
    }

    /** AJAX: xoá nhiều pages theo danh sách ID */
    public function bulkDeletePages(int $chapterId): ResponseInterface
    {
        if ($r = $this->guard()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(401);
        }
        $db = $this->db();
        if (!$db->table('chapter')->where('id', $chapterId)->get()->getRowArray()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Chapter not found'])->setStatusCode(404);
        }

        $ids = array_filter(array_map('intval', (array) ($this->request->getPost('page_ids') ?? [])));
        if (empty($ids)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No pages selected']);
        }

        $db->table('page')->where('chapter_id', $chapterId)->whereIn('id', $ids)->delete();

        return $this->response->setJSON(['success' => true, 'deleted' => count($ids)]);
    }

    /** AJAX: xoá toàn bộ pages của chapter */
    public function deleteAllPages(int $chapterId): ResponseInterface
    {
        if ($r = $this->guard()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(401);
        }
        $db = $this->db();
        if (!$db->table('chapter')->where('id', $chapterId)->get()->getRowArray()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Chapter not found'])->setStatusCode(404);
        }

        $count = (int) $db->table('page')->where('chapter_id', $chapterId)->countAllResults();
        $db->table('page')->where('chapter_id', $chapterId)->delete();

        return $this->response->setJSON(['success' => true, 'deleted' => $count]);
    }

    public function updatePage(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db   = $this->db();
        $page = $db->table('page')->where('id', $id)->get()->getRowArray();
        if (!$page) return $this->response->setStatusCode(404)->setBody('Page not found');

        $srcType    = $this->request->getPost('edit_source_type') ?? 'cdn';
        $imageLocal = trim($this->request->getPost('image_local') ?? '');
        $image      = trim($this->request->getPost('image') ?? '');
        $external   = (int) $this->request->getPost('external');

        if ($srcType === 'cdn') {
            $row = ['image_local' => $imageLocal, 'image' => '', 'external' => 0];
        } else {
            $row = ['image_local' => '', 'image' => $image, 'external' => $external];
        }
        $row['slug'] = max(1, (int) ($this->request->getPost('slug') ?? $page['slug']));

        $db->table('page')->where('id', $id)->update($row);

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Page updated.']);
        return redirect()->to("/admin/chapters/{$page['chapter_id']}/edit");
    }

    public function deletePage(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db   = $this->db();
        $page = $db->table('page')->where('id', $id)->get()->getRowArray();
        if (!$page) return $this->response->setStatusCode(404)->setBody('Page not found');

        $chapterId = $page['chapter_id'];
        $db->table('page')->where('id', $id)->delete();

        session()->setFlashdata('flash', ['type' => 'success', 'msg' => 'Page deleted.']);
        return redirect()->to("/admin/chapters/{$chapterId}/edit");
    }

    // ── Chapter Reports ──────────────────────────────────────────

    public function reports(): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db     = $this->db();
        $status = $this->request->getGet('status') ?? 'pending';
        $page   = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit  = 30;
        $offset = ($page - 1) * $limit;

        $builder = $db->table('chapter_reports cr')
            ->select('cr.id, cr.reason, cr.note, cr.ip_address, cr.created_at, cr.status,
                      ch.id AS chapter_id, ch.name AS chapter_name, ch.slug AS chapter_slug,
                      m.name AS manga_name, m.slug AS manga_slug,
                      u.username AS reporter_username')
            ->join('chapter ch', 'cr.chapter_id = ch.id', 'left')
            ->join('manga m', 'ch.manga_id = m.id', 'left')
            ->join('users u', 'cr.user_id = u.id', 'left')
            ->orderBy('cr.created_at', 'DESC');

        if ($status === 'pending') {
            $builder->where('cr.status', 'pending');
        }

        $total      = $builder->countAllResults(false);
        $totalPages = max(1, (int) ceil($total / $limit));
        $rows       = $builder->limit($limit, $offset)->get()->getResultArray();

        $pendingCount = (int) $db->table('chapter_reports')->where('status', 'pending')->countAllResults();

        return $this->response->setBody(
            $this->render('admin/reports_list', [
                'title'        => 'Chapter Reports',
                'activePage'   => 'reports',
                'rows'         => $rows,
                'total'        => $total,
                'page'         => $page,
                'totalPages'   => $totalPages,
                'status'       => $status,
                'pendingCount' => $pendingCount,
            ])
        );
    }

    public function resolveReport(int $id): ResponseInterface
    {
        if ($r = $this->guard()) return $r;
        $db = $this->db();

        // Lấy thông tin report trước khi resolve
        $report = $db->table('chapter_reports cr')
            ->select('cr.user_id, cr.reason,
                      ch.name AS chapter_name, ch.slug AS chapter_slug,
                      m.id AS manga_id, m.name AS manga_name, m.slug AS manga_slug')
            ->join('chapter ch', 'cr.chapter_id = ch.id', 'left')
            ->join('manga m', 'ch.manga_id = m.id', 'left')
            ->where('cr.id', $id)
            ->get()->getRowArray();

        $db->table('chapter_reports')->where('id', $id)->update(['status' => 'resolved']);

        // Gửi notification nếu reporter đã đăng nhập
        if ($report && !empty($report['user_id'])) {
            $reasonLabels = [
                'wrong_images'  => 'Wrong images',
                'missing_pages' => 'Missing pages',
                'low_quality'   => 'Low quality',
                'cant_load'     => 'Images not loading',
                'wrong_order'   => 'Wrong page order',
                'other'         => 'Other',
            ];
            $reasonLabel = $reasonLabels[$report['reason']] ?? $report['reason'];

            $db->table('notifications')->insert([
                'user_id'      => (int) $report['user_id'],
                'actor_id'     => $this->currentUser['id'] ?? 0,
                'type'         => 'report_resolved',
                'manga_id'     => (int) $report['manga_id'],
                'manga_slug'   => $report['manga_slug'] ?? '',
                'manga_name'   => $report['manga_name'] ?? '',
                'chapter_slug' => $report['chapter_slug'] ?? '',
                'preview'      => $report['chapter_name'] . ' — ' . $reasonLabel,
            ]);
        }

        return redirect()->to('/admin/reports?' . http_build_query([
            'status' => $this->request->getPost('status') ?? 'pending',
            'page'   => $this->request->getPost('page') ?? 1,
        ]));
    }

    // ── Push cover image to S3 ─────────────────────────────────
    public function pushToS3(int $id): ResponseInterface
    {
        if ($r = $this->guard()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(401);
        }

        $db    = $this->db();
        $manga = $db->table('manga')->where('id', $id)->get()->getRowArray();
        if (!$manga) {
            return $this->response->setJSON(['success' => false, 'error' => 'Manga not found'])->setStatusCode(404);
        }

        // 1. Tìm nguồn ảnh: ưu tiên file local, sau đó image URL
        $imageData = null;
        $mimeType  = 'image/jpeg';
        $mangaId   = $manga['id'];

        // Thử đọc file local: images/{id}-thumb.{ext} (fallback: {slug}-thumb.{ext} cho file cũ)
        foreach ([$mangaId, $manga['slug']] as $prefix) {
            foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
                $localPath = FCPATH . 'images/' . $prefix . '-thumb.' . $ext;
                if (file_exists($localPath)) {
                    $imageData = file_get_contents($localPath);
                    $mimeMap   = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
                    $mimeType  = $mimeMap[$ext] ?? 'image/jpeg';
                    break 2;
                }
            }
        }

        // Nếu không có local, download từ image URL
        if ($imageData === null && !empty($manga['image'])) {
            $ch = curl_init($manga['image']);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_USERAGENT      => 'Mozilla/5.0',
            ]);
            $body     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $ct       = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($httpCode === 200 && $body) {
                $imageData = $body;
                $mimeType  = explode(';', $ct)[0] ?: 'image/jpeg';
            }
        }

        if ($imageData === null) {
            return $this->response->setJSON(['success' => false, 'error' => 'No image source found']);
        }

        // 2. Tạo thumbnail (resize 300px wide, JPEG)
        $thumbData = $this->resizeToJpeg($imageData, 300);

        // 3. Upload lên S3: ảnh gốc + ảnh thumb
        $errors = [];
        if (!$this->s3PutObject($imageData, 'cover/' . $mangaId . '.jpg', $mimeType)) {
            $errors[] = 'original';
        }
        if (!$this->s3PutObject($thumbData, 'cover/' . $mangaId . '-thumb.jpg', 'image/jpeg')) {
            $errors[] = 'thumb';
        }
        if ($errors) {
            return $this->response->setJSON(['success' => false, 'error' => 'S3 upload failed (' . implode(', ', $errors) . ') — check S3_* config in .env']);
        }

        // 4. Cập nhật DB
        $db->table('manga')->where('id', $id)->update(['cover' => 1, 'image' => '']);

        return $this->response->setJSON(['success' => true, 'cdn_url' => rtrim(env('CDN_COVER_URL', ''), '/') . '/' . $mangaId . '-thumb.jpg']);
    }

    /**
     * Resize ảnh về width cố định (giữ tỉ lệ), trả về JPEG binary.
     * Fallback: trả nguyên ảnh gốc nếu GD không hỗ trợ.
     */
    private function resizeToJpeg(string $imageData, int $maxWidth = 300, int $quality = 85): string
    {
        if (!function_exists('imagecreatefromstring')) return $imageData;

        $src = @imagecreatefromstring($imageData);
        if (!$src) return $imageData;

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($origW <= $maxWidth) {
            // Chỉ cần convert sang JPEG
            ob_start();
            imagejpeg($src, null, $quality);
            $result = ob_get_clean();
            imagedestroy($src);
            return $result ?: $imageData;
        }

        $newW = $maxWidth;
        $newH = (int) round($origH * $maxWidth / $origW);

        $dst = imagecreatetruecolor($newW, $newH);
        // Xử lý transparency (PNG, WebP)
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        ob_start();
        imagejpeg($dst, null, $quality);
        $result = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $result ?: $imageData;
    }

    private function s3PutObject(string $data, string $key, string $contentType = 'image/jpeg'): bool
    {
        $endpoint  = rtrim(env('S3_ENDPOINT', ''), '/');
        $bucket    = env('S3_BUCKET', '');
        $region    = env('S3_REGION', 'us-east-1');
        $accessKey = env('S3_ACCESS_KEY', '');
        $secretKey = env('S3_SECRET_KEY', '');

        if (!$endpoint || !$bucket || !$accessKey || !$secretKey) return false;

        $datetime    = gmdate('Ymd\THis\Z');
        $date        = gmdate('Ymd');
        $payloadHash = hash('sha256', $data);
        $objPath     = '/' . $bucket . '/' . ltrim($key, '/');
        $url         = $endpoint . $objPath;
        $host        = parse_url($endpoint, PHP_URL_HOST);

        $headers = [
            'content-type'         => $contentType,
            'host'                 => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date'           => $datetime,
        ];
        ksort($headers);

        $canonicalHeaders = '';
        $signedList       = [];
        foreach ($headers as $k => $v) {
            $canonicalHeaders .= $k . ':' . $v . "\n";
            $signedList[]      = $k;
        }
        $signedHeaders = implode(';', $signedList);

        $canonicalRequest = implode("\n", [
            'PUT', $objPath, '',
            $canonicalHeaders, $signedHeaders, $payloadHash,
        ]);

        $credentialScope = "$date/$region/s3/aws4_request";
        $stringToSign    = implode("\n", [
            'AWS4-HMAC-SHA256', $datetime, $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        $kDate     = hash_hmac('sha256', $date,           'AWS4' . $secretKey, true);
        $kRegion   = hash_hmac('sha256', $region,         $kDate,              true);
        $kService  = hash_hmac('sha256', 's3',            $kRegion,            true);
        $kSigning  = hash_hmac('sha256', 'aws4_request',  $kService,           true);
        $signature = hash_hmac('sha256', $stringToSign,   $kSigning);

        $auth = "AWS4-HMAC-SHA256 Credential=$accessKey/$credentialScope, SignedHeaders=$signedHeaders, Signature=$signature";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: $auth",
                "Content-Type: $contentType",
                "Host: $host",
                "x-amz-content-sha256: $payloadHash",
                "x-amz-date: $datetime",
                "Content-Length: " . strlen($data),
            ],
        ]);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200 || $httpCode === 204;
    }
}
