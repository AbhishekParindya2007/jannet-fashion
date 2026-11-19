<?php
require_once __DIR__ . '/includes/bootstrap.php';

function render_html_with_asset_rewrites(string $htmlFile): void {
    $path = __DIR__ . DIRECTORY_SEPARATOR . $htmlFile;
    if (!is_file($path)) {
        http_response_code(404);
        echo "File not found: " . htmlspecialchars($htmlFile);
        return;
    }
    ob_start();
    include $path;
    $html = ob_get_clean();

    // Rewrite common asset paths to Images/ and Videos/
    $replacements = [
        'href="Icon.png"' => 'href="Images/Icon.png"',
        "src=\"logo.png\"" => 'src="Images/logo.png"',
        "url('bg.jpg')" => "url('Images/bg.jpg')",
        "src=\"jannet home.mp4\"" => 'src="Videos/jannet home.mp4"',
    ];

    // Replace for typical inline image names found across pages
    $extensions = ['png','jpg','jpeg','webp','gif','avif','svg'];
    foreach ($extensions as $ext) {
        // naive root path -> Images/<file>
        $html = preg_replace("#src=\"([^/][^\"]*\.$ext)\"#i", 'src="Images/$1"', $html);
        $html = preg_replace("#url\('([^/][^']*\.$ext)'\)#i", "url('Images/$1')", $html);
    }

    // video common types
    $videoExt = ['mp4','webm','ogg'];
    foreach ($videoExt as $ext) {
        $html = preg_replace("#src=\"([^/][^\"]*\.$ext)\"#i", 'src="Videos/$1"', $html);
    }

    // Apply specific overrides last
    $html = strtr($html, $replacements);

    echo $html;
}
