<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 24/01/17
 * Time: 17:52
 */

$arquivo_url = $_GET["arquivo"] ?? $_POST["arquivo"] ?? null;

if (!isset($arquivo_url)) {
    header("Location: index.html");
    die();
}

$arquivo_nome = basename($arquivo_url);
$arquivo_url = "resultados".DIRECTORY_SEPARATOR.rawurlencode($arquivo_nome);
$tamanho_arquivo = filesize($arquivo_url);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=\"$arquivo_nome\"");

$arquivo = fopen($arquivo_url, "r");

print fread($arquivo, $tamanho_arquivo);
