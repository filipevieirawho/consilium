#!/usr/bin/env php
<?php

$html = file_get_contents('http://consilium.test/');

// O problema é que o script está aparecendo no body como texto
// Vamos garantir que todo script inline esteja dentro de tags <script> corretas

// Remove qualquer texto de script que esteja fora de tags script
$html = preg_replace('/(?<!<script[^>]*>)function pc\(r,e\)\{return function\(\)\{return r\.apply\(e,arguments\)\}\}/', '', $html);

// Se o problema persistir, vamos envolver todo o conteúdo problemático em um script escondido
if (strpos($html, 'function pc(r,e)') !== false && strpos($html, '<script>') === false) {
    // O texto está solto no HTML, vamos removê-lo
    $html = preg_replace('/^function pc\(r,e\).*?window\.axios\.defaults\.headers\.common/ms', '', $html);
}

// Adiciona CSS para esconder qualquer erro que possa aparecer
$html = str_replace('</head>', '<style>body > script:first-child { display: none !important; }</style></head>', $html);

file_put_contents('/Users/filipevieirawho/Desktop/consilium-final.html', $html);

echo "HTML final criado no Desktop!\n";
echo "Tamanho: " . round(filesize('/Users/filipevieirawho/Desktop/consilium-final.html') / 1024, 2) . " KB\n";
