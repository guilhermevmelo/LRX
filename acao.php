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

/** @var  $q string A operação a ser executada. Será o conteúdo de uma reqisição GET ou POST; NULL caso não haja
 * requisição */
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
    $u = new Aluno();
    $u->setNome("Guilherme Vieira Melo");
    $s->setUsuario($u);
    echo $s->gerarIdentificacao(true);
    $u->setNome("Barbara Marques Alves");
    echo $s->gerarIdentificacao(true);

}

if (isset($q) && $q == 'testarCupom') {
    $cDAO = new CupomDAO();

    $c = $cDAO->obterPorCodigo("100056E04A42768D5");

    //$c = Cupom::gerarCupom(.5);
    $c->usar();

    $cDAO->atualizar($c);

    //$cDAO->criar($c);
    print_p($cDAO->obterTodos());
}

if (isset($q) && $q == "pdo") {
    print_p(\PDO::getAvailableDrivers());
}
