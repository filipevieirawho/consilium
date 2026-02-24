#!/usr/bin/env php
<?php

$html = file_get_contents('http://consilium.test/');

// Remove o script inline gigante que está causando o erro visual
$html = preg_replace('/<script>function pc\(r,e\).*?<\/script>/s', '<script>/* Script removido para versão standalone */</script>', $html);

// Remove qualquer texto de erro visível no body
$html = preg_replace('/function pc\(r,e\).*?window\.axios\.defaults/s', '', $html);

// Salva o HTML limpo
file_put_contents('/Users/filipevieirawho/Desktop/consilium-clean.html', $html);

echo "HTML limpo criado no Desktop!\n";
echo "Tamanho: " . round(filesize('/Users/filipevieirawho/Desktop/consilium-clean.html') / 1024, 2) . " KB\n";
