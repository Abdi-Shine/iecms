<?php

namespace App\Support;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class CourtDocumentPdf
{
    /**
     * Render a court document Blade view (the same view used for the on-screen
     * "Dukuumintiga" page) as a real, standalone PDF. Relies on the view's
     * existing print stylesheet (`.no-print` / `@media print` rules already
     * used for browser printing) to hide the toolbar, sign modal, and back
     * button, by rendering with the "print" media type.
     */
    public static function stream(string $view, array $data, string $filename): Response
    {
        $html = view($view, $data)->render();
        $html = self::inlineLocalStylesheets($html);
        $html = self::stripTailwindBundle($html);
        $html = self::inlineLocalImages($html);
        $html = self::stripPageAtRules($html);

        $options = new Options();
        // Local images are already inlined as data URIs above; only genuinely
        // external URLs (if any) would need a real fetch, and even then we
        // never want a request to loop back into this same PHP process.
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultMediaType', 'print');
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Strip the Vite-bundled app.css <style> block (recognizable by the
     * Tailwind reset it always starts with). These court documents are
     * styled entirely by hand-written CSS/inline styles, not Tailwind
     * utility classes, so the bundle is dead weight for the PDF — and worse
     * than dead weight: its sheer size (thousands of rules) causes Dompdf's
     * CSS engine to silently stop applying rules that come after it in the
     * document, such as the print-only `position: fixed` footer rule in
     * court-document.css, which is loaded later via @push('styles').
     */
    private static function stripTailwindBundle(string $html): string
    {
        return preg_replace('/<style>\*,:before,:after\{--tw-.*?<\/style>/s', '', $html, 1);
    }

    /**
     * Replace <img src="http://this-app/..."> URLs with base64 data URIs read
     * directly from local disk. Avoids Dompdf making an HTTP request back into
     * this same server for its own assets — with a single-worker dev server
     * (php artisan serve) that self-request deadlocks the process.
     */
    private static function inlineLocalImages(string $html): string
    {
        return preg_replace_callback(
            '/src="(https?:\/\/[^"]+)"/i',
            function (array $m) {
                $path = self::urlToLocalPath($m[1]);
                if (!$path) {
                    return $m[0];
                }

                $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                    'png'         => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif'         => 'image/gif',
                    'webp'        => 'image/webp',
                    'svg'         => 'image/svg+xml',
                    default       => null,
                };
                if (!$mime) {
                    return $m[0];
                }

                $data = base64_encode(file_get_contents($path));
                return 'src="data:' . $mime . ';base64,' . $data . '"';
            },
            $html
        );
    }

    /**
     * Replace <link rel="stylesheet" href="http://this-app/css/x.css"> with an
     * inline <style> block read from local disk — same self-request-deadlock
     * reason as inlineLocalImages().
     */
    private static function inlineLocalStylesheets(string $html): string
    {
        return preg_replace_callback(
            '/<link[^>]+rel="stylesheet"[^>]+href="(https?:\/\/[^"]+)"[^>]*>/i',
            function (array $m) {
                $path = self::urlToLocalPath($m[1]);
                if (!$path) {
                    return $m[0];
                }

                $css = file_get_contents($path);
                // Strip remote @import rules (e.g. Google Fonts) — unreachable
                // with isRemoteEnabled disabled, and left dangling they can
                // throw off Dompdf's page layout.
                $css = preg_replace('/@import\s+url\([^)]*https?:\/\/[^)]*\)\s*;?/i', '', $css);
                // `100vh` is meant for the browser print dialog; Dompdf
                // miscalculates it and produces a blank leading page. The
                // base (non-print) rule already sets a fixed A4 pixel height.
                $css = preg_replace('/height:\s*100vh\s*!important;?/i', '', $css);
                // Paper size/margin is already set via Dompdf's setPaper();
                // a duplicate @page rule in the stylesheet can produce a
                // second, blank page context.
                $css = preg_replace('/@page\s*\{[^}]*\}/i', '', $css);
                // A fixed 1123px A4-height box (height OR min-height) makes
                // Dompdf's layout engine emit a blank leading page — let it
                // size to content instead. This also neuters the "flex-grow
                // footer to page bottom" trick, but that trick itself causes
                // the same blank-leading-page bug in Dompdf, so it can't be
                // used for the PDF anyway; the footer instead sits directly
                // after the content, which is the reliable, bug-free option.
                $css = preg_replace('/(?<![\w-])(min-)?height:\s*1123px\s*;/i', '${1}height: auto;', $css);

                return '<style>' . $css . '</style>';
            },
            $html
        );
    }

    /**
     * Strip every @page {...} at-rule from the fully-assembled HTML,
     * wherever it came from — including a blade view's own inline
     * <style> block (inlineLocalStylesheets() only catches this pattern
     * in *externally linked* stylesheets, not markup the view already
     * had inline). A leftover @page rule — even one containing nested
     * margin-box at-rules like @bottom-center {...} — produces Dompdf's
     * blank-leading-page bug, since paper size/margins are already set
     * via setPaper(). Brace-balanced (not a naive regex) because these
     * rules commonly nest further at-rules inside them.
     */
    private static function stripPageAtRules(string $html): string
    {
        $result = '';
        $offset = 0;
        $len    = strlen($html);

        while (($pos = stripos($html, '@page', $offset)) !== false) {
            $result .= substr($html, $offset, $pos - $offset);

            $bracePos = strpos($html, '{', $pos);
            if ($bracePos === false) {
                $offset = $len;
                break;
            }

            $depth = 1;
            $i     = $bracePos + 1;
            while ($i < $len && $depth > 0) {
                if ($html[$i] === '{') {
                    $depth++;
                } elseif ($html[$i] === '}') {
                    $depth--;
                }
                $i++;
            }

            $offset = $i;
        }

        $result .= substr($html, $offset);

        return $result;
    }

    private static function urlToLocalPath(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        $path = ltrim($path, '/');
        $full = str_starts_with($path, 'storage/')
            ? storage_path('app/public/' . substr($path, strlen('storage/')))
            : public_path($path);

        return is_file($full) ? $full : null;
    }
}
