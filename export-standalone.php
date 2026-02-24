#!/usr/bin/env php
<?php

// Script to create a standalone HTML export with inline assets

$baseUrl = 'http://consilium.test';
$publicPath = __DIR__ . '/public';
$buildPath = $publicPath . '/build';

// Get the HTML
$html = file_get_contents($baseUrl);

// Get manifest to find compiled assets
$manifest = json_decode(file_get_contents($buildPath . '/manifest.json'), true);

// Get CSS content
$cssFile = $manifest['resources/css/app.css']['file'] ?? null;
if ($cssFile) {
    $cssContent = file_get_contents($buildPath . '/assets/' . basename($cssFile));
    // Inline CSS
    $html = preg_replace(
        '/<link[^>]*href="[^"]*' . preg_quote(basename($cssFile), '/') . '"[^>]*>/',
        '<style>' . $cssContent . '</style>',
        $html
    );
}

// Get JS content
$jsFile = $manifest['resources/js/app.js']['file'] ?? null;
if ($jsFile) {
    $jsContent = file_get_contents($buildPath . '/assets/' . basename($jsFile));
    // Inline JS
    $html = preg_replace(
        '/<script[^>]*src="[^"]*' . preg_quote(basename($jsFile), '/') . '"[^>]*><\/script>/',
        '<script>' . $jsContent . '</script>',
        $html
    );
}

// Convert images to base64
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

// Remove vite preload links
$html = preg_replace('/<link[^>]*rel="modulepreload"[^>]*>/', '', $html);
$html = preg_replace('/<link[^>]*rel="preload"[^>]*>/', '', $html);

// Save the standalone HTML
file_put_contents($publicPath . '/consilium-standalone.html', $html);

echo "Standalone HTML created successfully at: public/consilium-standalone.html\n";
echo "File size: " . round(filesize($publicPath . '/consilium-standalone.html') / 1024, 2) . " KB\n";
