<?php
/**
 * arquivo: 	action.php
 * data:		04/03/2015
 * autor:		Guilherme Vieira
 * descrição:	Manipula requisiçoes da index.
 */

namespace LRX;

require_once "classes/autoload.php";

session_start();

/** @var  $q guarda a operação a ser executada. Será o conteúdo de uma reqisição GET ou POST; NULL caso não haja requisição */
//$q = isset($_GET["q"])? $_GET["q"] : (isset($_POST["q"]) ? $_POST["q"] : NULL);
$q = $_GET["q"] ?? $_POST["q"] ?? NULL;


if (isset($q) && $q == "login") {
    $documento = addslashes($_POST["frm_login_documento"]);
    $senha = sha1(addslashes($_POST["frm_login_senha"]));
    $tipo = $_POST["frm_login_tipo"];

    $uDAO = new UsuarioDAO();

    /** @var  $u Usuario caso dê certo; null caso não dê */
    $u = $uDAO->login();
}

if (isset($q) && $q == 'testarUtils') {
    $s = new Solicitacao();
    $u = new Usuario();
    $u->nome = "Guilherme Vieira Melo";
    $s->usuario = $u;
    echo $s->gerarIdentificacao(true);
    $u->nome = "Barbara Marques Alves";
    echo $s->gerarIdentificacao(true);

}

if (isset($q) && $q == 'testarCupom') {
    $c = Cupom::getCupom(0.3);
    echo $c->getCodigo();

}

if (isset($q) && $q == "pdo") {
    print_r(\PDO::getAvailableDrivers());
}
