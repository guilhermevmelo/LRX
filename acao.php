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

/**
 *
 */
if (isset($q) && $q == "login") {
//    $email = "guilhermevmelo@gmail.com";
//    $senha = sha1("iqh5riv9");
    $email = addslashes($_POST['email']);
    $senha = addslashes($_POST['senha']);

    $u = UsuarioDAO::login($email, $senha);

    if ($u === null) {
        Erro::lancarErro(array("codigo" => 1001, "mensagem" => "Usuário não Encontrado"));
    } else {
        header('Content-Type: application/json');
        $resposta = array(
            "codigo" => 200,
            "id_usuario" => $u->getId(),
            "documento" => $u->getDocumento(),
            "nome" => $u->getNome(),
            "email" => $u->getEmail(),
            "email_alternativo" => $u->getEmailAlternativo(),
            "titulo" => $u->getTitulo(),
            "genero" => $u->getGenero(),
            "telefone" => $u->getTelefone(),
            "nivel_acesso" => $u->getNivelAcesso(),
            "confirmado" => $u->confirmado(),
            "email_confirmado" => $u->emailConfirmado(),
            "uid" => $u->getUid(),
            "mensagens" => $u->getMensagens(),
            "estado" => $u->getEstado(),
            "cidade" => $u->getCidade()
        );

        echo json_encode($resposta);
    }

    //print_p($u);

    //$tipo = $_POST["frm_login_tipo"];

   // $uDAO = new UsuarioAcademicoDAO();

    /** @var  $u Usuario caso dê certo; null caso não dê */
    //$u = $uDAO->login();
}

/**
 *
 */
if (isset($q) && $q == "loginDireto") {
//    $email = "guilhermevmelo@gmail.com";
//    $senha = sha1("iqh5riv9");
    $uid = addslashes($_POST['uid']);

    $u = UsuarioDAO::obterPorUid($uid);

    if ($u === null) {
        Erro::lancarErro(array("codigo" => 1001, "mensagem" => "Usuário não Encontrado"));
    } else {
        header('Content-Type: application/json');
        $resposta = array(
            "codigo" => 200,
            "id_usuario" => $u->getId(),
            "documento" => $u->getDocumento(),
            "nome" => $u->getNome(),
            "email" => $u->getEmail(),
            "email_alternativo" => $u->getEmailAlternativo(),
            "titulo" => $u->getTitulo(),
            "genero" => $u->getGenero(),
            "telefone" => $u->getTelefone(),
            "nivel_acesso" => $u->getNivelAcesso(),
            "confirmado" => $u->confirmado(),
            "email_confirmado" => $u->emailConfirmado(),
            "uid" => $u->getUid(),
            "mensagens" => $u->getMensagens(),
            "estado" => $u->getEstado(),
            "cidade" => $u->getCidade()
        );

        echo json_encode($resposta);
    }

    //print_p($u);

    //$tipo = $_POST["frm_login_tipo"];

    // $uDAO = new UsuarioAcademicoDAO();

    /** @var  $u Usuario caso dê certo; null caso não dê */
    //$u = $uDAO->login();
}

/**
 *
 */
if (isset($q) && $q == "obterListaSolicitacoes") {
    $id = addslashes($_GET["id"]);
    $nivel_acesso = intval($_GET['nivel_acesso']);
    $tipoSistema = $_GET['tipoSistema'];

    header('Content-Type: application/json');

    if ($tipoSistema == 1) {
        $saDAO = new SolicitacaoAcademicaDAO();

        // TODO implemntar demais niveis
        switch ($nivel_acesso) {
            case 5:
                $resposta = $saDAO->obterTodasIncompletas();
                break;

            default:
                $resposta = $saDAO->obterTodosPorUsuario($id);
        }

        echo json_encode(array("codigo" => 200, "solicitacoes" => $resposta));

    } else if ($tipoSistema == 2) {
        // TODO: implementar comercial
    }
}

if (isset($q) && $q == "obterListaSolicitacoesConcluidas") {
    $id = addslashes($_GET["id"]);
    $nivel_acesso = intval($_GET['nivel_acesso']);
    $tipoSistema = $_GET['tipoSistema'];

    header('Content-Type: application/json');

    if ($tipoSistema == 1) {
        $saDAO = new SolicitacaoAcademicaDAO();


        // TODO implementar demais niveis
        switch ($nivel_acesso) {
            case 5:
                $resposta = $saDAO->obterTodasConcluidas();
                break;

            default:
                $resposta = $saDAO->obterTodasConcluidasPorUsuario($id);
        }

        echo json_encode(array("codigo" => 200, "solicitacoes" => $resposta));

    } else if ($tipoSistema == 2) {
        // TODO: implementar comercial
    }
}

/**
 *
 */
if (isset($q) && $q == "obterDetalhesSolicitacao") {
    $id = addslashes($_GET["id_solicitacao"]);
    $tipoSistema = $_GET['tipoSistema'];

    header('Content-Type: application/json');

    if ($tipoSistema == 1) {
        $saDAO = new SolicitacaoAcademicaDAO();
        $resposta = $saDAO->obter($id, true);

        echo json_encode(array("codigo" => 200, "solicitacao" => $resposta));

    } else if ($tipoSistema == 2) {
        // TODO: implementar comercial
    }
}

/**
 *
 */
if (isset($q) && $q == "obterListaEquipamentos") {
    header('Content-Type: application/json');

    $eDAO = new EquipamentoDAO();
    $resposta = $eDAO->obterTodos(true, true);

    echo json_encode(array("codigo" => 200, "equipamentos" => $resposta));
}

/**
 *
 */
if (isset($q) && $q == "novaSolicitacaoAcademica") {
    header('Content-Type: application/json');

    $eDAO = new EquipamentoDAO();
    $equipamento = $eDAO->obter(intval($_POST['id_equipamento']));

    //TODO separar caso seja aluno
    $uDAO = new ProfessorDAO();
    $u = $uDAO->obter(intval($_POST['id_usuario']));

    $s = new SolicitacaoAcademica($u, $equipamento);
    $fDAO = new FendaDAO();
    $s->setFenda($fDAO->obter(2));

    $s->setComposicao(addslashes($_POST['composicao']));
    $config = array(
        '2theta_inicial' => intval($_POST['dois_theta_inicial']),
        '2theta_final' => intval($_POST['dois_theta_final']),
    );

    $s->setConfiguracao($config);

    $s->setInflamavel(boolval($_POST['inflamavel']));
    $s->setRadioativo(boolval($_POST['radioativo']));
    $s->setHigroscopico(boolval($_POST['higroscopico']));
    $s->setToxico(boolval($_POST['toxico']));
    $s->setCorrosivo(boolval($_POST['corrosivo']));

    $saDAO = new SolicitacaoAcademicaDAO();

    //print_p($s);

    $saDAO->criar($s);

    $r = array(
        "codigo" => 200,
        "identificacao" => $s->getIdentificacaoDaAmostra()
    );

    echo json_encode($r);

}


if (isset($q) && $q == "cancelarSolicitacao") {
    //header('Content-Type: application/json');

    // TODO adicionar verificacao de uid

    $saDAO = new SolicitacaoAcademicaDAO();
    //$sDAO = new SolicitacaoDAO();

    $s = $saDAO->obter(intval($_GET['id']), false);


    $s->setDataConclusao(new \DateTime());
    $s->setDataRecebimento(null);

    if (intval($_GET['nivel_acesso']) == 5)
        $s->setStatus(-2);
    else
        $s->setStatus(-1);
    $saDAO->atualizar($s);

    $r = array(
        "codigo" => 200
    );

    echo json_encode($r);

}

/********* PREAMBULO ********/
//echo date_default_timezone_get();

/********** TESTES **********/
if (isset($q) && $q == 'testarSoliDAO') {
    $saDAO = new SolicitacaoAcademicaDAO();
    $s = $saDAO->obter(5);

    print_p($s);

}

if (isset($q) && $q == 'testarSoli') {
    $eDAO = new EquipamentoDAO();
    $panalytical = $eDAO->obter(2);
    $uDAO = new ProfessorDAO();
    $u = $uDAO->obter(1);
    $s = new SolicitacaoAcademica($u, $panalytical);
    $fDAO = new FendaDAO();
    $s->setFenda($fDAO->obter(2));

    $saDAO = new SolicitacaoAcademicaDAO();

    print_p($s);

    $saDAO->criar($s);

    print_p($s);
}

if (isset($q) && $q == 'testarFenda') {
//    $f = new Fenda('1/32', true);
    $fDAO = new FendaDAO();
//    $fDAO->criar($f);

    print_p($fDAO->obterTodos());
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

    //print_p($profs);

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
    $a = $aDAO->obterTodos();
//
//    //print_p($p);
    print_p($a);

}

if (isset($q) && $q == 'equipamentos') {
    $eDAO =  new EquipamentoDAO();

    $e = new Equipamento(null, 'Não Existente', 'FRX', 'Co', false);

    //$eDAO->criar($e);
    print_p($eDAO->obterTodos());

    //$eDAO->deletar(5);
    print_p($eDAO->obterTodos());
}

//for ($i = 3; $i <= 119; $i++)
//    echo "&lt;option value=\"$i\"&gt;$i&amp;deg;&lt;/option&gt;<br>";