<?php

namespace App\Controllers;

use App\Models\MangaModel;

class Search extends BaseController
{
    public function index(): string
    {
        $mangaModel = new MangaModel();

        $filterName   = trim($this->request->getGet('filter[name]')   ?? '');
        $filterArtist = trim($this->request->getGet('filter[artist]') ?? '');
        $sort         = $this->request->getGet('sort') ?? '-updated_at';

        // Genres use slugs (comma-separated)
        $acceptRaw    = $this->request->getGet('filter[accept_genres]') ?? '';
        $rejectRaw    = $this->request->getGet('filter[reject_genres]') ?? '';
        $acceptGenres = array_values(array_filter(explode(',', $acceptRaw)));
        $rejectGenres = array_values(array_filter(explode(',', $rejectRaw)));

        // ?genre=slug from header nav → merge directly (no redirect)
        $genreSlug = trim($this->request->getGet('genre') ?? '');
        if ($genreSlug !== '' && !in_array($genreSlug, $acceptGenres)) {
            $acceptGenres[] = $genreSlug;
        }

        // ?author=slug from detail page → resolve to artist name for filtering
        $authorSlug = trim($this->request->getGet('author') ?? '');
        if ($authorSlug !== '' && $filterArtist === '') {
            $authorRow = db_connect()->table('author')->where('slug', $authorSlug)->get()->getRowArray();
            if ($authorRow) {
                $filterArtist = $authorRow['name'];
            }
        }

        $filterStatus  = $this->request->getGet('status') ?? '';
        $filterType    = trim($this->request->getGet('type') ?? '');
        $filterCaution = $this->request->getGet('caution');

        $results = $mangaModel->applySearchFilters([
            'filter_name'   => $filterName,
            'filter_artist' => $filterArtist,
            'sort'          => $sort,
            'accept_genres' => $acceptGenres,
            'reject_genres' => $rejectGenres,
            'status'        => $filterStatus,
            'type'          => $filterType,
            'caution'       => $filterCaution,
        ])->paginate(24);

        // Dynamic title / description
        if ($filterName !== '') {
            $pageTitle = lang('ComixxSearch.search_title', ['q' => $filterName]);
            $pageDesc  = lang('ComixxSearch.search_desc', ['q' => $filterName]);
        } elseif (!empty($acceptGenres)) {
            $genreRow  = db_connect()->table('category')->where('slug', $acceptGenres[0])->get()->getRowArray();
            $genreName = $genreRow['name'] ?? ucfirst(str_replace('-', ' ', $acceptGenres[0]));
            $pageTitle = $genreName . ' Manga';
            $pageDesc  = lang('ComixxSearch.genre_desc', ['genre' => $genreName]);
        } elseif ($filterArtist !== '') {
            $pageTitle = lang('ComixxSearch.artist_title', ['name' => $filterArtist]);
            $pageDesc  = lang('ComixxSearch.artist_desc', ['name' => $filterArtist]);
        } else {
            $pageTitle = lang('ComixxSearch.manga_list');
            $pageDesc  = lang('ComixxSearch.manga_list_desc');
        }

        $data = [
            'title'       => $pageTitle,
            'description' => $pageDesc,
            'categories'  => $this->categories,
            'currentUser' => $this->currentUser,
            'results'     => $results ?? [],
            'pager'       => $mangaModel->pager,
        ];
        return $this->themeView('search/index', $data);
    }

    public function liveSearch(): \CodeIgniter\HTTP\ResponseInterface
    {
        $q = trim($this->request->getGet('q') ?? '');

        if (strlen($q) < 2) {
            return $this->response->setJSON(['results' => []]);
        }

        $mangaModel = new MangaModel();
        $rows = $mangaModel
            ->where('is_public', 1)
            ->groupStart()
                ->like('name', $q)
                ->orLike('otherNames', $q)
            ->groupEnd()
            ->select('id, name, otherNames, slug, chapter_1, cover, image')
            ->orderBy('view_day', 'DESC')
            ->limit(10)
            ->find();

        $results = array_map(fn($m) => [
            'id'             => $m['id'],
            'name'           => $m['name'],
            'slug'           => $m['slug'],
            'cover_full_url' => manga_cover_url($m),
            'latest_chapter' => $m['chapter_1'] > 0
                ? ['name' => 'Chapter ' . rtrim(rtrim(number_format((float)$m['chapter_1'], 1), '0'), '.')]
                : null,
        ], $rows);

        return $this->response->setJSON(['results' => $results]);
    }
}
