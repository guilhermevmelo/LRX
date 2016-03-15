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

    $uDAO = new UsuarioAcademicoDAO();

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

if (isset($q) && $q == "usuarios") {
    //$g = new Grupo();
    //$g->setId(1);

    $p = new Professor("José Marcos Sasaki", "sasaki@fisica.ufc.br", "98765432100");
    $p->setAreaDePesquisa("Difração de Raios-X");
    $p->setCidade("Fortaleza");
    $p->setEstado("CE");
    $p->setConfirmado(true);
    $p->setGenero("M");
    $p->setDepartamento("Física");
    $p->setSenhaAberta("sasakilrx");
    $p->setTitulo(2);
    $p->setTelefone("85 999999999");
    $p->setLaboratorio("LRX");
    $p->setEmailAlternativo("josemarcossasaki@gmail.com");
    //$p->setGrupo($g);
//
    $pDAO = new ProfessorDAO();

    //$pDAO->criar($p);


    $profs = $pDAO->obterTodos();

    print_p($profs);

    //$profs[0]->setNome("Guilherme Vieira Melo");

//    $pDAO->atualizar($profs[0]);

//    //$pDAO->criar($p, false);
//
    $p2 = $pDAO->obter(1);

//    $a =  new Aluno("Bárbara", "barbaramalves1@gmail.com", "12345678900", $p2);
//    $a->setAreaDePesquisa("Fisioterapia Respiratória");
//    $a->setCidade("Fortaleza");
//    $a->setEstado("CE");
//    $a->setConfirmado(true);
//    $a->setGenero("F");
//    $a->setDepartamento("Fisioterapia");
//    $a->setSenhaAberta("beautiful18");
//    $a->setTitulo(0);
//    $a->setTelefone("85 989233281");
//    $a->setLaboratorio("HMCASG");
//    $a->setEmailAlternativo("oproprio@guilhermevieira.com.br");

    $aDAO = new AlunoDAO();
    $a = $aDAO->obter(21);
//
//    //print_p($p);
    print_p($a);

}
