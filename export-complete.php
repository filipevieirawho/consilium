#!/usr/bin/env php
<?php

$baseUrl = 'http://consilium.test';
$publicPath = __DIR__ . '/public';
$buildPath = $publicPath . '/build';

// Pega o HTML original
$html = file_get_contents($baseUrl);

// Pega o manifest
$manifest = json_decode(file_get_contents($buildPath . '/manifest.json'), true);

// Pega e inline o CSS
$cssFile = $manifest['resources/css/app.css']['file'] ?? null;
if ($cssFile && file_exists($buildPath . '/assets/' . basename($cssFile))) {
    $cssContent = file_get_contents($buildPath . '/assets/' . basename($cssFile));
    $html = preg_replace(
        '/<link[^>]*href="[^"]*' . preg_quote(basename($cssFile), '/') . '"[^>]*>/',
        '<style>' . $cssContent . '</style>',
        $html
    );
}

// Pega e inline o JS
$jsFile = $manifest['resources/js/app.js']['file'] ?? null;
if ($jsFile && file_exists($buildPath . '/assets/' . basename($jsFile))) {
    $jsContent = file_get_contents($buildPath . '/assets/' . basename($jsFile));
    $html = preg_replace(
        '/<script[^>]*src="[^"]*' . preg_quote(basename($jsFile), '/') . '"[^>]*><\/script>/',
        '<script type="module">' . $jsContent . '</script>',
        $html
    );
}

// Remove preload links
$html = preg_replace('/<link[^>]*rel="(modulepreload|preload)"[^>]*>/', '', $html);

// Converte imagens para base64
$imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
foreach ($imageExtensions as $ext) {
    preg_match_all('/src="([^"]*\.' . $ext . ')"/i', $html, $matches);
    foreach ($matches[1] as $imagePath) {
        $localPath = $publicPath . parse_url($imagePath, PHP_URL_PATH);
        if (file_exists($localPath)) {
            $imageData = base64_encode(file_get_contents($localPath));
            $mimeType = mime_content_type($localPath);
            $base64Image = 'data:' . $mimeType . ';base64,' . $imageData;
            $html = str_replace($imagePath, $base64Image, $html);
        }
    }
}

// Salva
file_put_contents('/Users/filipevieirawho/Desktop/consilium-complete.html', $html);

echo "HTML completo criado no Desktop!\n";
echo "Tamanho: " . round(filesize('/Users/filipevieirawho/Desktop/consilium-complete.html') / 1024, 2) . " KB\n";
