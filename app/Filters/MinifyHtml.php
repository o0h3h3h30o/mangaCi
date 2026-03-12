<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MinifyHtml implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Skip admin pages (Tailwind CDN relies on unminified class scanning)
        $uri = $request->getUri()->getPath();
        if (str_starts_with($uri, '/admin')) {
            return;
        }

        $contentType = $response->getHeaderLine('Content-Type');
        if (str_contains($contentType, 'application/json') || str_contains($contentType, 'text/plain')) {
            return;
        }

        $body = $response->getBody();
        if (empty($body)) {
            return;
        }

        // Preserve <pre> và <textarea> nguyên vẹn
        $preserved = [];
        $index     = 0;

        $body = preg_replace_callback(
            '#(<(?:pre|textarea)[^>]*>)(.*?)(</(?:pre|textarea)>)#is',
            static function ($m) use (&$preserved, &$index) {
                $placeholder         = "\x02PRE_{$index}\x03";
                $preserved[$index++] = $m[0];
                return $placeholder;
            },
            $body
        );

        // Minify inline <style>
        $body = preg_replace_callback(
            '#(<style[^>]*>)(.*?)(</style>)#is',
            static fn($m) => $m[1] . self::minifyCss($m[2]) . $m[3],
            $body
        );

        // Minify inline <script>
        $body = preg_replace_callback(
            '#(<script[^>]*>)(.*?)(</script>)#is',
            static fn($m) => $m[1] . self::minifyJs($m[2]) . $m[3],
            $body
        );

        // Minify HTML: collapse whitespace between tags
        $body = preg_replace('/>\s+</', '><', $body);
        // Collapse multiple spaces/tabs/newlines into one space
        $body = preg_replace('/\s{2,}/', ' ', $body);

        // Restore preserved blocks
        foreach ($preserved as $i => $original) {
            $body = str_replace("\x02PRE_{$i}\x03", $original, $body);
        }

        $response->setBody(trim($body));
    }

    private static function minifyCss(string $css): string
    {
        // Xóa comments (trừ /*! license */)
        $css = preg_replace('#/\*(?!!)(.*?)\*/#s', '', $css);
        // Xóa whitespace thừa xung quanh các ký tự đặc biệt
        $css = preg_replace('/\s*([{}:;,>~+])\s*/', '$1', $css);
        // Xóa ; trước }
        $css = str_replace(';}', '}', $css);
        // Collapse whitespace
        $css = preg_replace('/\s{2,}/', ' ', $css);
        return trim($css);
    }

    private static function minifyJs(string $js): string
    {
        if (trim($js) === '') {
            return $js;
        }

        // Xóa multi-line comments (trừ /*! license */)
        $js = preg_replace('#/\*(?!!)(.*?)\*/#s', '', $js);
        // Xóa single-line comments (cẩn thận không xóa URL http://)
        $js = preg_replace('#(?<!:)//(?!/).*+#m', '', $js);
        // Collapse whitespace
        $js = preg_replace('/[ \t]+/', ' ', $js);
        // Xóa dòng trắng
        $js = preg_replace('/\n\s*\n/', "\n", $js);
        // Xóa space xung quanh operators
        $js = preg_replace('/\s*([=+\-*\/%&|^!<>?:,;{}()\[\]])\s*/', '$1', $js);
        // Xóa newline thừa
        $js = preg_replace('/\n+/', '', $js);

        return trim($js);
    }
}
