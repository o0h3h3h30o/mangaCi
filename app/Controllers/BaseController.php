<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected array   $categories  = [];
    protected ?array  $currentUser = null;
    protected string  $activeTheme = 'default';
    protected $helpers = ['site_settings', 'lang_helper'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $categoryModel    = new CategoryModel();
        $this->categories = $categoryModel->getAllCategories();

        $this->activeTheme = site_setting('active_theme', 'default') ?: 'default';

        // Set locale from admin setting
        $locale = site_setting('site_language', 'en');
        if (in_array($locale, config('App')->supportedLocales)) {
            $request->setLocale($locale);
            service('language')->setLocale($locale);
        }

        $session = session();
        if ($session->get('isLoggedIn')) {
            $this->currentUser = [
                'id'       => (int) $session->get('user_id'),
                'name'     => $session->get('user_name'),
                'username' => $session->get('user_username'),
            ];
        } elseif ($cookie = $request->getCookie('_rm')) {
            // Auto-login từ remember-me cookie
            $decoded = base64_decode($cookie, true);
            if ($decoded && substr_count($decoded, '|') >= 1) {
                [$userId, $mac] = explode('|', $decoded, 2);
                $userModel = new \App\Models\UserModel();
                $user = $userModel->where('active', 1)->where('site_id', site_id())->find((int) $userId);
                if ($user) {
                    $expected = hash_hmac('sha256', $user['id'] . '|' . $user['password'], env('encryption.key', 'fallback-secret'));
                    if (hash_equals($expected, $mac)) {
                        $session->set([
                            'user_id'       => $user['id'],
                            'user_name'     => $user['name'] ?: $user['username'],
                            'user_username' => $user['username'],
                            'isLoggedIn'    => true,
                        ]);
                        $this->currentUser = [
                            'id'       => (int) $user['id'],
                            'name'     => $user['name'] ?: $user['username'],
                            'username' => $user['username'],
                        ];
                    }
                }
            }
        }
    }

    /**
     * Render view từ theme đang active, fallback về default nếu file không tồn tại.
     */
    protected function themeView(string $path, array $data = []): string
    {
        $try = 'themes/' . $this->activeTheme . '/' . $path;
        if (is_file(APPPATH . 'Views/' . $try . '.php')) {
            return view($try, $data);
        }
        return view('themes/default/' . $path, $data);
    }
}
