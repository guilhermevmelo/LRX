<?php
/**
 * arquivo: 	acao.php
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
$q = $_GET["q"] ?? $_POST["q"] ?? null;

/**
 *
 */
if (isset($q) && $q == "login") {
    $email = addslashes($_POST['email']);
    $senha = addslashes($_POST['senha']);

    $u = UsuarioDAO::login($email, $senha);
    //TODO: Mensagens diferentes para email e para senha não encontrados.
    if ($u === null) {
        Erro::lancarErro(array("codigo" => 1001, "mensagem" => "Usuário não encontrado."));
    } else if (!$u->emailConfirmado()) {
        Erro::lancarErro(array("codigo" => 1002, "mensagem" => "Email não confirmado."));
    } else if ($u->confirmado() != 2) {
        Erro::lancarErro(array("codigo" => 1002, "mensagem" => "Usuário ainda não teve cadastro liberado por um operador."));
    }
    else {
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
            "limite" => $u->getLimite(),
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
            "limite" => $u->getLimite(),
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
if (isset($q) && $q == "obterListaAlunos") {
    $id = intval(addslashes($_GET["id"]));
    $nivel_acesso = intval($_GET['nivel_acesso']);
    // TODO: Verificar se o usuário solicitante é realment eum professor

    header('Content-Type: application/json');
    $aDAO = new AlunoDAO();
    $resposta = $aDAO->obterTodosPorProfessor($id, true);
    echo json_encode(array("codigo" => 200, "alunos" => $resposta));
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

        // TODO implementar demais niveis
        switch ($nivel_acesso) {
            case 5:
                $resposta = $saDAO->obterTodasIncompletas(true);
                break;

            case 6:
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

/**
 *
 */
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
if (isset($q) && $q == "obterDetalhesAluno") {
    $id = intval(addslashes($_GET["id_aluno"]));
    $id_requisitante = intval(addslashes($_GET["id_requisitante"]));

    header('Content-Type: application/json');

    $aDAO = new AlunoDAO();
    $resposta = $aDAO->obter($id, true);
    echo json_encode(array("codigo" => 200, "aluno" => $resposta));

}

/**
 *
 */
if (isset($q) && $q == "obterDetalhesSolicitacao") {
    $id = addslashes($_GET["id_solicitacao"]);
    $tipoSistema = intval($_GET['tipoSistema']);

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
    /**
     * Se não for explicitamente requisitado que não sejam retornados apenas
     * os equipamentos disponíveis, serão.
     */
    $apenasDisponiveis = boolval($_GET['apenasDisponiveis'] ?? true);
    $eDAO = new EquipamentoDAO();
    $resposta = $eDAO->obterTodos($apenasDisponiveis, true);

    echo json_encode(array("codigo" => 200, "equipamentos" => $resposta));
}

/**
 *
 */
if (isset($q) && $q == "novaSolicitacaoAcademica") {
    header('Content-Type: application/json');

    $eDAO = new EquipamentoDAO();
    $equipamento = $eDAO->obter(intval($_POST['id_equipamento']));

    $u = null;
    switch (intval($_POST['nivel_acesso'])) {
        case 1:
            $uDAO = new AlunoDAO();
            $u = $uDAO->obter(intval($_POST['id_usuario']));
            break;

        case 2:
            $uDAO = new ProfessorDAO();
            $u = $uDAO->obter(intval($_POST['id_usuario']));
            break;

        default:
            $uDAO = new ProfessorDAO();
            $u = $uDAO->obter(intval($_POST['id_usuario']));
    }

    $s = new SolicitacaoAcademica($u, $equipamento);
    $fDAO = new FendaDAO();
    // Adicionando uma fenda padrão. Deverá ser modifiada pelo operador.
    $s->setFenda($fDAO->obter(2));
    $s->setTipo(intval($_POST['tipo_amostra']));
    $s->setComposicao(addslashes($_POST['composicao']));

    $config = ($_POST['tipo_analise'] == 'drx') ?
        array(
            'tecnica' => 'drx',
            'dois_theta_inicial' => intval($_POST['dois_theta_inicial']),
            'dois_theta_final' => intval($_POST['dois_theta_final']),
            'delta_dois_theta' => intval($_POST['delta_dois_theta']),
        ) :
        array(
            'tecnica' => 'frx',
            'resultado' => $_POST['tipo_resultado'],
            'medida' => $_POST['tipo_medida']
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

/**
 *
 */
if (isset($q) && $q == "alterarSolicitacaoAcademica") {

}

/**
 *
 */
if (isset($q) && $q == "aprovarSolicitacao") {

    $pDAO = new ProfessorDAO();
    $saDAO = new SolicitacaoAcademicaDAO();

    $id_professor = intval($_GET["id_professor"]);

    $p = $pDAO->obter($id_professor);

    if ($p === null) {
        Erro::lancarErro(array("codigo" => 3999, "mensagem" => "Usuário não encontrado"));
    } else {
        $nSolicitacoesAndamento = $saDAO->obterNumeroSolicitacoesEmAndamento($id_professor)["aprovadas"];
        $limite = $p->getLimite();

        //echo $nSolicitacoesAndamento . " < " . $limite;

        if ($nSolicitacoesAndamento < $limite) {
            $s = $saDAO->obter(intval($_GET["id_solicitacao"]), false);
            $s->setStatus(2);
            $saDAO->atualizar($s);

            header('Content-Type: application/json');
            echo json_encode(array("codigo" => 200));
        } else {
            Erro::lancarErro(array("codigo" => 4000, "mensagem" => "Limite de solicitações atingido"));
        }
    }


}

/**
 *
 */
if (isset($q) && $q == "confirmarEmail") {
    //TODO refatorar isso aqui
    $uid = addslashes($_GET["uid"]);
    $u = UsuarioDAO::obterPorUid($uid);



    if ($u === null) {
        Erro::lancarErro(array("codigo" => 300, "mensagem" => "Usuário não encontrado."));
    } else {
        $pDAO = new ProfessorDAO();
        $p = $pDAO->obter($u->getId());
        $p->confirmarEmail();
        $pDAO->atualizar($p);
        header('Content-Type: application/json');
        //print_r($u);
        echo json_encode(array("codigo" => 200, "mensagem" => "Seu email foi confirmado, ".$u->getNome()));
    }

}


/**
 *
 */
if (isset($q) && $q == "cancelarSolicitacao") {
    header('Content-Type: application/json');

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

/**
 *
 */
if (isset($q) && $q == "verificarDocumento") {
    header('Content-Type: application/json');

    $email = addslashes($_GET['email']);
    $cpf = addslashes($_GET['documento']);
    $cpf = desformatarCPF($cpf);

    $documentoExiste = UsuarioDAO::existeDocumento($cpf);
    $emailExiste = UsuarioDAO::existeEmail($email);

    $r = array(
        "codigo" => 200,
        "existeDocumento" => $documentoExiste,
        "existeEmail" => $emailExiste
    );
    echo json_encode($r);
}

/**
 *
 */
if (isset($q) && $q == "vincularAluno") {
    header('Content-Type: application/json');

    $email = addslashes($_POST['email']);
    $cpf = addslashes($_POST['documento']);
    $cpf = desformatarCPF($cpf);
    $nome = addslashes($_POST['nome']);
    $id_professor = intval($_POST['id_professor']);

    $podeSerVinculado = false;
    $existeVinculo = null;
    $emailExiste = null;
    $mensagem = null;
    $documentoExiste = UsuarioDAO::nivelDeAcessoPorDocumento($cpf);

    if ($documentoExiste >= 2) {
        // documento já cadastrado com um nível de no mínimo professor
        // TODO: Avisar quando usuario tentar vincular como aluno um usuário já cadastrado como professor
        $podeSerVinculado = false;
        $mensagem = "Existe um professor cadastrado com o CPF informado. Por favor, solicite que este entre em contato com a equipe técnica do laboratório para que o cadastro seja modificado.";
    } else if ($documentoExiste == 1){
        // documento cadastrado como aluno: verifica se está desvinculado
        $aDAO = new AlunoDAO();
        $aluno = $aDAO->obterPorDocumento($cpf);
        $existeVinculo = AlunoDAO::existeVinculo($aluno->getId());

        if ($existeVinculo) {
            $mensagem = "Existe um aluno cadastrado com o CPF informado. No entanto, está vinculado a um outro professor. Por favor, solicite que seu aluno se desvincule do outro professor no painel de configurações.";
        } else {
            // vincula e envia email

            $podeSerVinculado = true;
            $mensagem = "Existe um aluno cadastrado com o CPF informado. Foi vinculado com sucesso à sua conta.";
        }

    } else if (!$documentoExiste) {
        //documento nao cadastrado: verifica se email esta cadastrado
        $emailExiste = UsuarioDAO::existeEmail($email);

        if ($emailExiste) {
            // email ja esta cadastrado notifica o professor
            $mensagem = "O CPF informado não está cadastrado em nosso sistema, no entanto e email informado está no cadastro de outro CPF. Por favor, entre em contato com seu aluno para conferência dos dados.";
        } else {
            // novo usuario, prosseguir com o cadastro

            $aDAO = new AlunoDAO();
            $pDAO = new ProfessorDAO();
            $professor = $pDAO->obter($id_professor);

            $aluno = new Aluno($nome, $email, $cpf, $professor);
            $aluno->setSenhaAberta("12345678");

            $aDAO->criar($aluno);

            $link = "http://guilhermevieira.com.br/raiosx/#/NovoAluno/".$aluno->getUid();
            // subject
            $subject = '[Convite para Cadastro no LRX] '.$aluno->getNome();

            // message
            $message = '
                <html>
                <head>
                 <title>'.$subject.'</title>
                </head>
                <body>
                <p>Olá '.$aluno->getNome().',<br>você foi cadastrado no Sistema de Solicitações do Laboratório de Raios X da UFC sob
                 orientação de '.$professor->getNome().'. Antes de estar apto a solicitar análises, você precisa completar seu cadastro.
                  O que pode ser feito seguindo o link abaixo.</p>
                <p>Continuar cadastro: <a href="'.$link.'">'.$link.'</a></p>
                </body>
                </html>
                ';

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

            // Additional headers
            $headers .= 'From: LRX <naoresponda@raiosx.fisica.ufc.br>' . "\r\n";

            // Mail it
            mail($aluno->getEmail(), $subject, $message, $headers);

            $podeSerVinculado = true;
            $mensagem = "Um convite foi enviado ao email informado. Por favor, solicite que seu aluno conclua o cadastro.";
        }
    }

    $r = array(
        "codigo" => 200,
        "existeDocumento" => boolval($documentoExiste),
        "existeEmail" => $emailExiste,
        "existeVinculo" => $existeVinculo,
        "podeSerVinculado" => $podeSerVinculado,
        "mensagem" => $mensagem
    );
    echo json_encode($r);
}

/**
 *
 */
if (isset($q) && $q == "cadastrarUsuario") {
    header('Content-Type: application/json');

    $email = addslashes($_POST['email']);
    $cpf = addslashes($_POST['documento']);
    $cpf = desformatarCPF($cpf);
    $nome = addslashes($_POST['nome']);
    $genero = addslashes($_POST['genero']);
    $email_alternativo = addslashes($_POST['email_alternativo']);
    $cidade = addslashes($_POST['cidade']);
    $estado = addslashes($_POST['estado']);
    $telefone = addslashes($_POST['telefone']);
    $ies = addslashes($_POST['ies']);
    $departamento = addslashes($_POST['departamento']);
    $laboratorio = addslashes($_POST['laboratorio']);
    $area_de_pesquisa = addslashes($_POST['area_de_pesquisa']);
    $titulo = addslashes(intval($_POST['titulo']));
    $senha = addslashes($_POST['senha']);

    $p = new Professor($nome, $email, $cpf);
    $p->setAreaDePesquisa($area_de_pesquisa);
    $p->setCidade($cidade);
    $p->setEstado($estado);
    $p->setConfirmado(false);
    $p->setEmailConfirmado(false);
    $p->setGenero($genero);
    $p->setDepartamento($departamento);
    $p->setSenha($senha);
    $p->setTitulo(intval($titulo));
    $p->setTelefone($telefone);
    $p->setLaboratorio($laboratorio);
    $p->setEmailAlternativo($email_alternativo);
    $p->setIes($ies);

    $pDAO = new ProfessorDAO();
    try {
        $pDAO->criar($p);

        $link = "http://guilhermevieira.com.br/raiosx/#/NovoUsuario/Confirmar/".$p->getUid();
        // subject
        $subject = '[Confirmação de Cadastro LRX] '.$p->getNome();

        // message
        $message = '
	<html>
	<head>
	 <title>'.$subject.'</title>
	</head>
	<body>
	Confirmar: <a href="'.$link.'">'.$link.'</a>
	</body>
	</html>
	';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        // Additional headers
        $headers .= 'From: LRX <naoresponda@raiosx.fisica.ufc.br>' . "\r\n";

        // Mail it
        mail($p->getEmail(), $subject, $message, $headers);
        $r = array(
            "codigo" => 200
        );
    } catch (\Exception $ex) {
        Erro::lancarErro($ex->getMessage());
        $r = array(
            "codigo" => 3,
            "mensagem" => $ex->getMessage()
        );
    }


    echo json_encode($r);
}

/********* PREAMBULO ********/
//echo date_default_timezone_get();

/********** TESTES **********/
if (isset($q) && $q == "test") {
    $aDAO = new AlunoDAO();

}