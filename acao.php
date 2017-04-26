<?php
/**
 * arquivo:    acao.php
 * data:        04/03/2015
 * autor:        Guilherme Vieira
 * descrição:    Manipula requisiçoes da index.
 */

use LRX\Correio\Correio;
use LRX\Correio\MalaDireta;
use function LRX\desformatarCNPJ;
use LRX\Equipamentos\Equipamento;
use LRX\Equipamentos\EquipamentoDAO;
use LRX\Equipamentos\FendaDAO;
use LRX\Erro;
use LRX\Solicitacoes\Resultados\Resultado;
use LRX\Solicitacoes\Resultados\ResultadoDAO;
use LRX\Solicitacoes\SolicitacaoAcademica;
use LRX\Solicitacoes\SolicitacaoAcademicaDAO;
use LRX\Usuarios\Aluno;
use LRX\Usuarios\AlunoDAO;
use LRX\Usuarios\Empresa;
use LRX\Usuarios\EmpresaDAO;
use LRX\Usuarios\Pesquisador;
use LRX\Usuarios\PesquisadorDAO;
use LRX\Usuarios\Professor;
use LRX\Usuarios\ProfessorDAO;
use LRX\Usuarios\Usuario;
use LRX\Usuarios\UsuarioDAO;
use function LRX\desformatarCPF;
use function \LRX\obterExtensaoArquivo;

require_once "classes/LRX/autoload.php";
session_save_path("/tmp");
session_start();


/** @var $q string A operação a ser executada. Será o conteúdo de uma reqisição GET ou POST; NULL caso não haja
 * requisição */

$q = $_GET["q"] ?? $_POST["q"] ?? null;

$host = "http://csd.fisica.ufc.br/solicitacoes/";

/**
 *
 */
if (isset($q) && $q == "login") {

    $email = addslashes($_POST['email']);
    $senha = addslashes($_POST['senha']);

    $u = UsuarioDAO::login($email, $senha);
    
    if ($u === null) {
        Erro::lancarErro(array("codigo" => 1001, "mensagem" => "Usuário não encontrado."));
    } else if ($u === false) {
        Erro::lancarErro(array("codigo" => 1001, "mensagem" => "Senha incorreta."));
    } else if (!$u->emailConfirmado()) {
        Erro::lancarErro(array("codigo" => 1002, "mensagem" => "Email não confirmado."));
    } else if ($u->confirmado() != 2) {
        Erro::lancarErro(array("codigo" => 1002, "mensagem" => "Usuário ainda não teve cadastro liberado por um operador."));
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
    if ($nivel_acesso !== UsuarioDAO::nivelDeAcessoPorId($id)) {
        Erro::lancarErro(array("codigo" => 300, "mensagem" => "Você não tem permissão para executar essa ação"));
        return;
    }

    $aDAO = new AlunoDAO();
    $resposta = $aDAO->obterTodosPorProfessor($id, true);
    header('Content-Type: application/json');
    echo json_encode(array("codigo" => 200, "alunos" => $resposta));
}

/**
 *
 */
if (isset($q) && $q == "obterListaUsuarios") {
    $id = intval(addslashes($_GET["id"]));
    $nivel_acesso = intval($_GET['nivel_acesso']);
    $professores = boolval($_GET['professores']);
    $alunos = boolval($_GET['alunos']);
    $operadores = boolval($_GET['operadores']);
    $pesquisadores = boolval($_GET['pesquisadores']);
    $nao_confirmados = boolval($_GET['nao_confirmados']);

    if ($nivel_acesso !== UsuarioDAO::nivelDeAcessoPorId($id)) {
        Erro::lancarErro(array("codigo" => 303, "mensagem" => "Você não tem permissão para executar essa ação"));
        die();
    }

    $resposta = array();

    if ($professores) {
        $pDAO = new ProfessorDAO();
        $resposta = array_merge($resposta, $pDAO->obterTodos(true, $nao_confirmados));
    }

    if ($alunos) {
        $aDAO = new AlunoDAO();
        $resposta = array_merge($resposta, $aDAO->obterTodos(true, $nao_confirmados));
    }

    if ($pesquisadores) {
        $peDAO = new PesquisadorDAO();
        $resposta = array_merge($resposta, $peDAO->obterTodos(true, $nao_confirmados));
    }

    if ($operadores) {

    }

    header('Content-Type: application/json');
    echo json_encode(array("codigo" => 200, "usuarios" => $resposta));
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
                $resposta = $saDAO->obterTodasIncompletas(true);
                break;

            default:
                $resposta = $saDAO->obterTodosPorUsuario($id);
        }

        echo json_encode(array("codigo" => 200, "solicitacoes" => $resposta));

    } else if ($tipoSistema == 2) {
        // TODO: implementar comercial

        $resposta = array();

        echo json_encode(array("codigo" => 200, "solicitacoes" => $resposta));
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

            case 6:
                $resposta = $saDAO->obterTodasConcluidas();
                break;

            default:
                $resposta = $saDAO->obterTodasConcluidasPorUsuario($id);
        }

        echo json_encode(array("codigo" => 200, "solicitacoes" => $resposta));

    } else if ($tipoSistema == 2) {
                // TODO: implementar comercial

        $resposta = array();

        echo json_encode(array("codigo" => 200, "solicitacoes" => $resposta));
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

        // todo saDAO->obter retornar falso mandar codigo de erro

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

if (isset($q) && $q == "alterarStatusEquipamento") {
	header('Content-Type: application/json');
	$eDAO = new EquipamentoDAO();

	$id_equipamento = intval($_POST["id_equipamento"]);
	$e = $eDAO->obter($id_equipamento);
	if ($e === false) {
		Erro::lancarErro( array("codigo" => Erro::ERRO_EQUIPAMENTO_INEXISTENTE, "mensagem" => "Equipamento Inexistente"));
		return;
	}

	$e->setDisponivel(! $e->getDisponivel());
	$eDAO->atualizar($e);

	echo json_encode(array("codigo" => Erro::OK, "mensagem" => "Status do Equipamento alterado com sucesso."));
}

if (isset($q) && $q == "novoEquipamento") {
	header('Content-Type: application/json');

	//TODO: Verificar permissão de quem está adicionando

	$eDAO = new EquipamentoDAO();

	$nome = addslashes( $_POST["nome"]);
	$tipo = strtoupper(addslashes( $_POST["tipo"]));
	$tubo = strtoupper(addslashes( $_POST["tubo"]));
	$disponivel = $_POST["disponivel"] == "false" ? false : true;
	$obs = addslashes( $_POST["obs"]);

	$e = new Equipamento(null, $nome, $tipo, $tubo, $disponivel, null, $obs);

	if ($eDAO->criar($e)) {
		echo json_encode( array("codigo" => Erro::OK, "mensagem" => "Novo Equipamento adicionado com sucesso."));
	} else {
		Erro::lancarErro( array("codigo" => 300, "mensagem" => "Ocorreu um erro. Por favor informe um administrador."));
	}
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

    $solicitacao = new SolicitacaoAcademica($u, $equipamento);
    $fDAO = new FendaDAO();
    // Adicionando uma fenda padrão. Deverá ser modificada pelo operador.
    $solicitacao->setFenda($fDAO->obter(2));
    $solicitacao->setTipo(intval($_POST['tipo_amostra']));
    $solicitacao->setComposicao(addslashes($_POST['composicao']));

    $config = ($_POST['tipo_analise'] == 'drx') ?
        array(
            'tecnica' => 'drx',
            'dois_theta_inicial' => intval($_POST['dois_theta_inicial']),
            'dois_theta_final' => intval($_POST['dois_theta_final']),
            'delta_dois_theta' => floatval($_POST['delta_dois_theta']),
        ) :
        array(
            'tecnica' => 'frx',
            'resultado' => $_POST['tipo_resultado'],
            'medida' => $_POST['tipo_medida']
        );

    $solicitacao->setConfiguracao($config);

    $solicitacao->setInflamavel($_POST['inflamavel'] === "true" ? true : false);
    $solicitacao->setRadioativo($_POST['radioativo'] === "true" ? true : false);
    $solicitacao->setHigroscopico($_POST['higroscopico'] === "true" ? true : false);
    $solicitacao->setToxico($_POST['toxico'] === "true" ? true : false);
    $solicitacao->setCorrosivo($_POST['corrosivo'] === "true" ? true : false);

    $solicitacao->setObservacoes($_POST['observacoes']);

    $saDAO = new SolicitacaoAcademicaDAO();

    $saDAO->criar($solicitacao);

    $r = array(
        "codigo" => 200,
        "identificacao" => $solicitacao->getIdentificacaoDaAmostra()
    );

    if (intval($_POST['nivel_acesso']) === 1) {

        $aDAO = new AlunoDAO();
        $a = $aDAO->obter($u->getId());
        $p = $a->getProfessor();

        $assunto = 'Autorização de Análise da Amostra';
        $corpo_da_mensagem = '<p>Olá prof. '.$p->getNome().',<br> seu aluno ' . $a->getNome(). ' fez uma solicitação de análises de raios-x em nosso sistema. No entanto, é necessário que você
                autorize as análises das amostras para que possamos recebê-las.</p>
                <p>Para isso, acesse o <a href="http://csd.fisica.ufc.br/solicitacoes/" target="_blank">Sistema de Solicitação de Análises de Raios-X</a> e autorize as solicitações do seu aluno.</p>
                <p>Caso possua alguma dúvida quanto ao cadastro ou ao sistema em si, por favor entre em contato com o Laboratório 
                por meio do endereço de email lrxufc@gmail.com, ou pelo telefone 85 33669013.</p>
                <p style="text-align:right;">Atenciosamente,<br>Laboratório de Raios-X</p>';


        $correio = new Correio($p->getEmail(), $assunto, $corpo_da_mensagem);
        $correio->enviar();

    }

    echo json_encode($r);

}

/**
 * Autorizar Solicitação: O professor autoriza a solicitação que está sob responsabilidade dele.
 * Essa autorização verifica os limites tanto do aluno solicitante quanto do professor responsável.
 * Só após essa autorização é que a amostra vai para a tela dos operadores para ser aprovada pelo laboratório.
 */
if (isset($q) && $q == "autorizarSolicitacao") {
    $pDAO = new ProfessorDAO();
    $saDAO = new SolicitacaoAcademicaDAO();

    $id_professor = intval($_GET["id_professor"]);
    $nivel_acesso_professor = UsuarioDAO::nivelDeAcessoPorId($id_professor);

    if ($nivel_acesso_professor != 2 && $nivel_acesso_professor != 6) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_PERMISSAO_NEGADA, "mensagem" => "Você não tem permissão para executar essa ação"));
        return;
    }

    $p = $pDAO->obter($id_professor);

    if ($p === null) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_USUARIO_NAO_ENCONTRADO, "mensagem" => "Usuário não encontrado"));
    } else {
        $nSolicitacoesAndamento = $saDAO->obterNumeroSolicitacoesEmAndamento($id_professor)["aprovadas"];
        $limite = $p->getLimite();

        // Verifica se o professor está habilitado para fazer solicitações
        if (!$p->estaHabilitado()) {
	        Erro::lancarErro(array("codigo" => Erro::ERRO_SOLICITANTE_NAO_HABILITADO, "mensagem" => "Suas solicitações estão bloqueadas. No momento, apenas professores específicos estão habilitados a fazer solicitações."));
        }

        // Verifica se o professor ainda tem limite para aprovar solicitações
        if ($nSolicitacoesAndamento < $limite) {
            $solicitacao = $saDAO->obter(intval($_GET["id_solicitacao"]), false);

            // Verifica se o solicitante (que pode ser um aluno) tem limite para que sua solicitação seja aprovada
            $solicitante = $solicitacao->getSolicitante();
            $nSolicitacoesAndamentoSolicitante = $saDAO->obterNumeroSolicitacoesEmAndamento($solicitante);

            if ($nSolicitacoesAndamentoSolicitante["aprovadas"] == 0 ||
                $nSolicitacoesAndamentoSolicitante["aprovadas"] < $nSolicitacoesAndamentoSolicitante["limite"]
            ) {
                $solicitacao->setStatus(2);
                $saDAO->atualizar($solicitacao);
                header('Content-Type: application/json');
                echo json_encode(array("codigo" => 200));
            } else {
                Erro::lancarErro(array("codigo" => Erro::ERRO_LIMITE_SOLICITANTE_ANTIGIDO, "mensagem" => "Limite de solicitações do solicitante atingido"));
            }
        } else {
            Erro::lancarErro(array("codigo" => Erro::ERRO_LIMITE_PROFESSOR_ANTIGIDO, "mensagem" => "Limite de solicitações do professor atingido"));
        }
    }


}

/**
 * Alterar Status da Solicitação: executa semanticamente uma das ações abaixo:
 *
 * Aprovar Solicitação: A solicitação já foi autorizada pelo professor e agora está na lista do Laboratório
 * para ser aprovada. Após aprovação, a solicitação fica aguardando a entrega das amostras fisicamente no
 * Laboratório.
 * status = 3
 *
 * Confirmar Entrega: Uma vez aprovada a solicitação pelo laboratório, a amostra precisa ser entregue fisicamente
 * para que a análise seja feita. Adiciona a data de entrega. Somente após entregue é que a análise vai para
 * a fila do equipamento.
 * status = 4
 *
 * Análise em Andamento: A solicitação saiu da fila e está com análise em andamento no momento.
 * status = 5
 */
if (isset($q) && $q == "alterarStatusSolicitacao") {
    $saDAO = new SolicitacaoAcademicaDAO();

    $id_solicitacao = intval($_GET["id_solicitacao"]);
    $id_operador = intval($_GET["id_operador"]);
    $status = intval($_GET["status"]);

    $nivel_acesso_operador = UsuarioDAO::nivelDeAcessoPorId($id_operador);

    if ($nivel_acesso_operador < 5) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_PERMISSAO_NEGADA, "mensagem" => "Você não tem permissão para executar essa ação"));
        return;
    }

    $solicitacao = $saDAO->obter($id_solicitacao, false);
    if ($solicitacao === null) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_SOLICITACAO_INEXISTENTE, "mensagem" => "A solicitação que você está tentando alterar não existe"));
        return;
    }

    if ($status != 3 && $status != 4 && $status != 5 && $status != 7) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_STATUS_INEXISTENTE, "mensagem" => "Novo status para a solicitação não reconhecido"));
        return;
    }

    if ($status === 4) {
        $solicitacao->setDataRecebimento(new DateTime());
    }
    $solicitacao->setStatus($status);
    $saDAO->atualizar($solicitacao);

    if ($status === 3) {

        $uDAO = new UsuarioDAO();
        $u = $uDAO->obter(intval($solicitacao->getSolicitante()));

        $assunto = 'Análise da Amostra ' . $solicitacao->getIdentificacaoDaAmostra() . ' Autorizada';
        $corpo_da_mensagem = '<p>Olá '. $u->getNome() . ',<br> sua solicitação de análise da amostra ' . $solicitacao->getIdentificacaoDaAmostra() . ' foi
                aprovada pelo seu orientador e pelo laboratório. Portanto, <strong>estamos aguardando o recebimento da amostra para iniciarmos a análise.</strong></p>
                <p>O horário de recibemento e entrega de amostras do Laboratório de Raios X é de <strong>segunda a quinta, de 9:00 a 11:30 e de 13:30 a 16:00 hrs</strong></p>.
                <p>Lembre-se de etiquetar suas amostra usando o código de identificação da mesma.</p>
                <p>Caso possua alguma dúvida quanto ao cadastro ou ao sistema em si, por favor entre em contato com o Laboratório 
                por meio do endereço de email lrxufc@gmail.com, ou pelo telefone 85 33669013.</p>
                <p style="text-align:right;">Atenciosamente,<br>Laboratório de Raios-X</p>';


        $correio = new Correio($u->getEmail(), $assunto, $corpo_da_mensagem);
        $correio->enviar();
    }

    header('Content-Type: application/json');
    echo json_encode(array("codigo" => 200));
}

/**
 *
 */
if (isset($q) && $q == "enviarResultado") {
    if (!is_uploaded_file($_FILES['arquivoUploadResultado']['tmp_name'])) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_ARQUIVO_INVALIDO, "mensagem" => "O correu um erro com o arquivo. Por favor tente novamente."));
        return;
    }
    $saDAO = new SolicitacaoAcademicaDAO();

    $id_operador = intval($_POST["id_operador"]);
    $id_solicitacao = intval($_POST["id_solicitacao"]);

    $nivel_acesso_operador = UsuarioDAO::nivelDeAcessoPorId($id_operador);

    if ($nivel_acesso_operador < 5) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_PERMISSAO_NEGADA, "mensagem" => "Você não tem permissão para executar essa ação"));
        return;
    }

    switch ($nivel_acesso_operador) {
        case 5:
            $uDAO = new AlunoDAO();
            break;

        case 6:
            $uDAO = new ProfessorDAO();
            break;
    }

    $operador = $uDAO->obter($id_operador);

    $solicitacao = $saDAO->obter($id_solicitacao, false);
    if ($solicitacao === null) {
        Erro::lancarErro(array("codigo" => Erro::ERRO_SOLICITACAO_INEXISTENTE, "mensagem" => "A solicitação que você está tentando alterar não existe"));
        return;
    }

    $diretorio = "resultados/";
    $arquivo_temporario = $_FILES["arquivoUploadResultado"]["tmp_name"];
    $extensao = obterExtensaoArquivo($_FILES["arquivoUploadResultado"]["name"]);
    $novo_arquivo = $diretorio . $solicitacao->getIdentificacaoDaAmostra() . "_" . uniqid() . "." . $extensao;

    header("Content-Type: application/json");
    if (move_uploaded_file($arquivo_temporario, $novo_arquivo)) {
        /*
         * Se o arquivo foi upado com sucesso e movido à página correta, insira-o no banco e associe-o à solicitação
         */
        $resultado = new Resultado($novo_arquivo, $solicitacao->getId());
        $resultado->setOperador($operador);
        $resultado->setDataEnvio(date('Y-m-d H:i:s'));

        $rDAO = new ResultadoDAO();
        $rDAO->criar($resultado);

        $solicitacao->setStatus(6);
        $saDAO->atualizar($solicitacao);

        $uDAO = new UsuarioDAO();
        $u = $uDAO->obter(intval($solicitacao->getSolicitante()));

        $assunto = 'Análise de Amostra Concluída';
        $corpo_da_mensagem = '<p>Olá . '.$u->getNome().',<br> a análise da amostra ' . $solicitacao->getIdentificacaoDaAmostra(). ' foi concluída.</p>
                <p>Para que os resultados sejam liberados para download em nosso sistema, é necessário que você faça o recolhimento da sua amostra.</p>
                <p>O horário de recibemento e entrega de amostras do Laboratório de Raios X é de <strong>segunda a quinta, de 9:00 a 11:30 e de 13:30 a 16:00 hrs</strong></p>.
                <p>Caso possua alguma dúvida quanto ao cadastro ou ao sistema em si, por favor entre em contato com o Laboratório 
                por meio do endereço de email lrxufc@gmail.com, ou pelo telefone 85 33669013.</p>
                <p style="text-align:right;">Atenciosamente,<br>Laboratório de Raios-X</p>';


        $correio = new Correio($u->getEmail(), $assunto, $corpo_da_mensagem);
        $correio->enviar();

        echo json_encode(array(
            "codigo" => 200
        ));
    } else {
        Erro::lancarErro(array("codigo" => Erro::ERRO_DE_UPLOAD, "mensagem" => "Ocorreu um erro com o upload"));
    }



}

/**
 *
 */
if (isset($q) && $q == "confirmarEmail") {
    //TODO refatorar isso aqui
    $uid = addslashes($_GET["uid"]);
    //$nivel_acesso = UsuarioDAO::nivelDeAcessoPorUid($uid);

    $u = UsuarioDAO::obterPorUid($uid);

    if ($u === null) {
        Erro::lancarErro(array("codigo" => 300, "mensagem" => "Usuário não encontrado."));
    } else {
        switch ($u->getNivelAcesso()) {
            case 1:
                $uDAO = new AlunoDAO();
                break;

            case 2:
                $uDAO = new ProfessorDAO();
                break;

            case 3:
                $uDAO = new PesquisadorDAO();
                break;

            case 4:
                // TODO: Implementar Financeiro
                break;

            case 5:
                $uDAO = new AlunoDAO();
                break;

            case 6:
                $uDAO = new ProfessorDAO();

                break;
        }
        $p = $uDAO->obter($u->getId());
        $p->confirmarEmail();
        $uDAO->atualizar($p);
        header('Content-Type: application/json');
        echo json_encode(array("codigo" => 200, "mensagem" => "Seu email foi confirmado, " . $u->getNome()));
    }

}

/**
 *
 */
if (isset($q) && $q == "novaSenha") {
    $uid = addslashes($_POST["uid"]);
    $senha = addslashes($_POST['novaSenha']);

    $u = UsuarioDAO::obterPorUid($uid);

    if ($u === null) {
        Erro::lancarErro(array("codigo" => 300, "mensagem" => "Usuário não encontrado."));
    } else {
        switch ($u->getNivelAcesso()) {
            case 1:
                $uDAO = new AlunoDAO();
                break;

            case 2:
                $uDAO = new ProfessorDAO();
                break;

            case 3:
                // TODO: Implementar Responsável por empresa
                break;

            case 4:
                // TODO: Implementar Financeiro
                break;

            case 5:
                $uDAO = new AlunoDAO();
                break;

            case 6:
                $uDAO = new ProfessorDAO();

                break;
        }
        $p = $uDAO->obter($u->getId());
        $p->setSenha($senha);
        $uDAO->atualizar($p);
        header('Content-Type: application/json');
        echo json_encode(array("codigo" => 200, "mensagem" => "Sua senha foi alterada com sucesso, " . $u->getNome()));
    }

}

/**
 *
 */
if (isset($q) && $q == "novaSenhaEnviarEmail") {
    $cpf = addslashes($_GET["cpf"]);
    $cpf = desformatarCPF($cpf);
    $nivel_acesso = UsuarioDAO::nivelDeAcessoPorDocumento($cpf);

    switch ($nivel_acesso) {
        case 1:
            $uDAO = new AlunoDAO();
            break;
        case 2:
            $uDAO = new ProfessorDAO();
            break;
        case 3:
            //TODO Empresa
            break;
        case 4:
            //TODO Financeiro
            break;
        case 5:
            $uDAO = new AlunoDAO();
            break;
        case 6:
            $uDAO = new ProfessorDAO();
            break;

        default:
            Erro::lancarErro(array("codigo" => 300, "mensagem" => "Usuário não encontrado."));
            exit();
    }

    $u = $uDAO->obterPorDocumento($cpf);

    $link = $host . "#/RecuperarConta/NovaSenha/" . strrev($u->getUid());

    $assunto = '[LRX] Recuperação de conta ' . $u->getNome();
    $corpo_da_mensagem = '
                <p>Olá ' . $u->getNome() . ',<br>foi solicitada redefinição de senha para sua conta no LRX. Utilize o link abaixo para continuar o processo.
                Caso essa solicitação não tenha sido feita por você, basta ignorar este email. </p>
                <p>Utilize o seguinte link a qualquer momento para redefinir sua senha: <a href="' . $link . '" target="_blank">' . $link . '</a></p>
            ';
    $correio = new Correio($u->getEmail(), $assunto, $corpo_da_mensagem);
    $correio->enviar();

    header('Content-Type: application/json');
    echo json_encode(array("codigo" => 200, "mensagem" => "Foi enviado um link de recuperação de senha para o seu e-mail. Por favor, verifique sua caixa de entrada."));

}

/**
 *
 */
if (isset($q) && $q == "cancelarSolicitacao") {
    header('Content-Type: application/json');

    // TODO adicionar verificacao de uid

    $saDAO = new SolicitacaoAcademicaDAO();
    //$sDAO = new SolicitacaoDAO();

    $solicitacao = $saDAO->obter(intval($_GET['id']), false);


    $solicitacao->setDataConclusao(new \DateTime());
    $solicitacao->setDataRecebimento(null);

    if (intval($_GET['nivel_acesso']) == 5 || intval($_GET['nivel_acesso']) == 6)
        $solicitacao->setStatus(-2);
    else
        $solicitacao->setStatus(-1);
    $saDAO->atualizar($solicitacao);

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
    $tipo_usuario = addslashes($_GET['tipoUsuario']);

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
    $vinculo = addslashes($_POST['vinculo']);
    $id_professor = intval($_POST['id_professor']);

    $podeSerVinculado = false;
    $existeVinculo = null;
    $emailExiste = null;
    $mensagem = null;
    $documentoExiste = UsuarioDAO::nivelDeAcessoPorDocumento($cpf);
    $r = null;

    if ($documentoExiste >= 2) {
        // documento já cadastrado com um nível de no mínimo professor
        // TODO: Avisar quando usuario tentar vincular como aluno um usuário já cadastrado como professor
        $podeSerVinculado = false;
        $mensagem = "Existe um professor cadastrado com o CPF informado. Por favor, solicite que este entre em contato com a equipe técnica do laboratório para que o cadastro seja modificado.";
    } else if ($documentoExiste == 1) {
        // documento cadastrado como aluno: verifica se está desvinculado
        $aDAO = new AlunoDAO();
        $aluno = $aDAO->obterPorDocumento($cpf);
        $existeVinculo = AlunoDAO::existeVinculo($aluno->getId());

        if ($existeVinculo) {
            $mensagem = "Existe um aluno cadastrado com o CPF informado. No entanto, está vinculado a um outro professor. Por favor, solicite que seu aluno se desvincule do outro professor no painel de configurações.";
        } else {
            // vincula e envia email
            //TODO: Vincular caso o aluno já esteja cadastrado e esteja apto a novo vínculo.
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

            $aluno = new Aluno($nome, $email, $cpf, $professor, $vinculo);
            $aluno->setSenhaAberta("12345678");

            $aDAO->criar($aluno);

            $link = $host . "#/NovoAluno/" . $aluno->getUid();

            $assunto = '[Convite para Cadastro no LRX] ' . $aluno->getNome();
            $corpo_da_mensagem = '
                <p>Olá ' . $aluno->getNome() . ',<br>você foi cadastrado no Sistema de Solicitações do Laboratório de Raios X da UFC sob
                 orientação de ' . $professor->getNome() . '. Antes de estar apto a solicitar análises, você precisa completar seu cadastro.
                  O que pode ser feito seguindo o link abaixo.</p>
                <p>Continuar cadastro: <a href="' . $link . '">' . $link . '</a></p>
            ';
            $correio = new Correio($email, $assunto, $corpo_da_mensagem);
            $correio->enviar();

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
if (isset($q) && $q == "completarCadastroAluno") {
    $uid = addslashes($_GET['uid']);
    $aDAO = new AlunoDAO();
    $a = $aDAO->obterPorUid($uid, true);

    if ($a === false) {
        Erro::lancarErro(array("codigo" => 1001, "mensagem" => "Usuário não Encontrado"));
    } else {
        header('Content-Type: application/json');
        $resposta = array(
            "codigo" => 200,
            "aluno" => $a
        );

        echo json_encode($resposta);
    }
}

/**
 *
 */
if (isset($q) && $q == "cadastrarUsuario") {
    header('Content-Type: application/json');

    $tipo = addslashes($_POST['tipo']);
    $email = addslashes($_POST['email']);
    $cpf = addslashes($_POST['documento']);
    $cpf = desformatarCPF($cpf);
    $nome = addslashes($_POST['nome']);
    $genero = addslashes($_POST['genero']);
    $email_alternativo = addslashes($_POST['email_alternativo']);
    $cidade = addslashes($_POST['cidade']);
    $estado = addslashes($_POST['estado']);
    $telefone = addslashes($_POST['telefone']);
    $senha = addslashes($_POST['senha']);

    $r = null;

    if ($tipo === "academico") {

        $ies = addslashes($_POST['ies']);
        $departamento = addslashes($_POST['departamento']);
        $laboratorio = addslashes($_POST['laboratorio']);
        $area_de_pesquisa = addslashes($_POST['area_de_pesquisa']);
        $titulo = addslashes(intval($_POST['titulo']));

        $p = new Professor($nome, $email, $cpf);
        $p->setAreaDePesquisa($area_de_pesquisa);
        $p->setCidade($cidade);
        $p->setEstado($estado);
        $p->setConfirmado(false);
        $p->setEmailConfirmado(false);
        $p->setGenero($genero == 'M' ? 1 : 2);
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

            $link = $host . "#/NovoUsuario/Confirmar/" . $p->getUid();
            $assunto = '[Confirmação de Cadastro LRX] ' . $p->getNome();
            $corpo_da_mensagem = '<p>Olá prof(a). '.$p->getNome().',<br> foi solicitado um cadastro em seu nome no Sistema de 
            Solicitações do Laboratório de Raios-X do departamento de Física da UFC. Para que possamos confirmar que este 
            endereço de email de fato pertence a você, precisamos que utilize o link de confirmação abaixo.</p>
            <p>Basta clicar no link abaixo que esta página será redirecionada para o sistema e a confirmação do email será 
            executada. Link: <a href="' . $link . '">' . $link . '</a></p>
            <p>Caso o(a) sr(a) não tenha requisitado uma conta no nosso sistema, fique tranquilo(a). Não utilizar o link acima 
            implicará na não liberação do cadastro e quem quer que tenha utilizado seu endereço de email para cadastrar-se 
            não terá acesso ao sistema.</p>
            <p>Caso possua alguma dúvida quanto ao cadastro ou ao sistema em si, por favor entre em contato com o Laboratório 
            por meio do endereço de email lrxufc@gmail.com, ou pelo telefone 85 33669013.</p>
            <p style="text-align:right;">Atenciosamente,<br>Laboratório de Raios-X</p>';


            $correio = new Correio($email, $assunto, $corpo_da_mensagem);
            $correio->enviar();

            $r = array(
                "codigo" => 200
            );
        } catch (\Exception $ex) {
            $r = array(
                "codigo" => 3,
                "mensagem" => $ex->getMessage()
            );
        }
    } else if ($tipo === "empresarial") {

        $cnpj = desformatarCNPJ($_POST['cnpj']);
        $razao_nome = addslashes($_POST['razao_nome']);
        $sigla = addslashes($_POST['sigla']);

        $endereco = array(
            'cep' => addslashes($_POST['cep']),
            'rua' => addslashes($_POST['rua']),
            'numero' => addslashes($_POST['rua_numero']),
            'complemento' => addslashes($_POST['rua_complemento']),
            'bairro' => addslashes($_POST['bairro']),
            'cidade' => addslashes($_POST['cidade_emp']),
            'estado' => addslashes($_POST['estado_emp'])

        );

        $inscricao_estadual = addslashes($_POST['inscricao_estadual']);
        $inscricao_municipal = addslashes($_POST['inscricao_municipal']);
        $site = addslashes($_POST['site']);

        $e = new Empresa($cnpj, $razao_nome, $sigla, $endereco, $inscricao_estadual, $inscricao_municipal, $site);
        $eDAO = new EmpresaDAO();
        $eDAO->criar($e);

        $e = $eDAO->obterPorCnpj($cnpj);

        $p = new Pesquisador($nome, $email, $cpf);
        $p->setCidade($cidade);
        $p->setEstado($estado);
        $p->setConfirmado(false);
        $p->setEmailConfirmado(false);
        $p->setGenero($genero == 'M' ? 1 : 2);
        $p->setSenha($senha);
        $p->setTelefone($telefone);
        $p->setEmailAlternativo($email_alternativo);
        $p->setEmpresa($e);

        $pDAO = new PesquisadorDAO();
        $pDAO->criar($p);

        $link = $host . "#/NovoUsuario/Confirmar/" . $p->getUid();
        $assunto = '[Confirmação de Cadastro LRX] ' . $p->getNome();
        $corpo_da_mensagem = '<p>Olá Sr(a). '.$p->getNome().',<br> foi solicitado um cadastro em seu nome no Sistema de 
            Solicitações do Laboratório de Raios-X do departamento de Física da UFC. Para que possamos confirmar que este 
            endereço de email de fato pertence a você, precisamos que utilize o link de confirmação abaixo.</p>
            <p>Basta clicar no link abaixo que esta página será redirecionada para o sistema e a confirmação do email será 
            executada. Link: <a href="' . $link . '">' . $link . '</a></p>
            <p>Caso o(a) sr(a) não tenha requisitado uma conta no nosso sistema, fique tranquilo(a). Não utilizar o link acima 
            implicará na não liberação do cadastro e quem quer que tenha utilizado seu endereço de email para cadastrar-se 
            não terá acesso ao sistema.</p>
            <p>Caso possua alguma dúvida quanto ao cadastro ou ao sistema em si, por favor entre em contato com o Laboratório 
            por meio do endereço de email lrxufc@gmail.com, ou pelo telefone 85 33669013.</p>
            <p style="text-align:right;">Atenciosamente,<br>Laboratório de Raios-X</p>';


        $correio = new Correio($email, $assunto, $corpo_da_mensagem);
        $correio->enviar();

        $r = array(
            "codigo" => 200
        );


    }

    echo json_encode($r);
}

/**
 *
 */
if (isset($q) && $q == "cadastrarAluno") {
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
    $uid = addslashes($_POST['uid']);

    $aDAO = new AlunoDAO();

    $a = $aDAO->obterPorUid($uid);
    $a->setNome($nome);
    $a->setDocumento($cpf);
    $a->setAreaDePesquisa($area_de_pesquisa);
    $a->setCidade($cidade);
    $a->setEstado($estado);
    $a->setConfirmado(true);
    $a->setEmailConfirmado(false);
    $a->setGenero($genero == 'M' ? 1 : 2);
    $a->setDepartamento($departamento);
    $a->setSenha($senha);
    $a->setTitulo(intval($titulo));
    $a->setTelefone($telefone);
    $a->setLaboratorio($laboratorio);
    $a->setEmailAlternativo($email_alternativo);
    $a->setIes($ies);
    $a->confirmarEmail();


    try {
        $aDAO->atualizar($a);

        //$link = $host . "#/NovoUsuario/Confirmar/" . $a->getUid();
        //$assunto = '[Confirmação de Cadastro LRX] ' . $a->getNome();

        //$corpo_da_mensagem = '<p>Confirmar: <a href="' . $link . '">' . $link . '</a></p>';

        //$correio = new Correio($email, $assunto, $corpo_da_mensagem);
        //$correio->enviar();

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

/**
 *
 */
if (isset($q) && $q == "confirmarUsuario") {
    $id = intval(addslashes($_GET["id"]));
    $id_operador = intval(addslashes($_GET["id_operador"]));

    $nivel_acesso_operador = UsuarioDAO::nivelDeAcessoPorId($id_operador);

    if ($nivel_acesso_operador < 5) {
        Erro::lancarErro(array("codigo" => 303, "mensagem" => "Você não tem permissão para executar essa ação"));
        die();
    }

    $nivel_acesso = UsuarioDAO::nivelDeAcessoPorId($id);
    if ($nivel_acesso == 1) {
        Erro::lancarErro(array("codigo" => 5000, "mensagem" => "Não é possível confirmar um usuário aluno"));
        die();
    }

    $uDAO = null;
    switch ($nivel_acesso) {
        case 2:
            $uDAO = new ProfessorDAO();
            break;
        case 3:
            $uDAO = new PesquisadorDAO();
            break;
    }

    $u = $uDAO->obter($id, false);

    if ($u === null) {
        Erro::lancarErro(array("codigo" => 300, "mensagem" => "Usuário não encontrado."));
    } else {
        $u->confirmar();
        $uDAO->atualizar($u);

        $assunto = "[LRX] Liberação de cadastro";
        $link = $host;
        $mensagem = "<p>Olá " . $u->getNome() . ",<br>confirmamos seu cargo e liberamos seu cadastro 
            para solicitações.</p>
            <p>Se você for professor, acesse o sistema em <a href='" . $link . "' target='_blank'>" . $link . "</a> e cadastre individualmente seus alunos
            na opção <strong>Vincular Aluno</strong> do menu <strong>Alunos</strong> para que também possam fazer solicitações.</p>
            <p>Observe que todas as solicitações, tanto suas quanto de seus alunos, <span style='color:red;'>devem ser aprovadas pelo senhor</span> 
            antes de serem enviadas ao laboratório. Observe também que cada professor possui um limite todal de vinte solicitações
            simultâneas em andamento, somadas as suas e a de seus alunos. Contamos, portanto, com sua colaboração para que não forneça
            seus dados de login arbitrariamente para seus alunos, deixe que tenham cada qual seu próprio cadastro.</p>
            <p>Agradecemos a compreensão e seja bem vindo!<br>Equipe LRX</p>";
        $correio = new Correio($u->getEmail(), $assunto, $mensagem);
        $correio->enviar();

        header('Content-Type: application/json');
        echo json_encode(array("codigo" => 200, "mensagem" => "O usuário " . $u->getNome() . " foi confirmado"));
    }
}

if (isset($q) && $q == "cancelarUsuario") {
    $id = intval(addslashes($_GET["id"]));
    $id_operador = intval(addslashes($_GET["id_operador"]));

    $nivel_acesso_operador = UsuarioDAO::nivelDeAcessoPorId($id_operador);

    if ($nivel_acesso_operador < 5) {
        Erro::lancarErro(array("codigo" => 303, "mensagem" => "Você não tem permissão para executar essa ação"));
        die();
    }

    $nivel_acesso = UsuarioDAO::nivelDeAcessoPorId($id);
    if ($nivel_acesso == 1) {
        Erro::lancarErro(array("codigo" => 5000, "mensagem" => "Não é possível cancelar um usuário aluno"));
        die();
    }

    $uDAO = null;

    if ($nivel_acesso == 2) {
        $uDAO = new ProfessorDAO();
    } else if ($nivel_acesso == 3) {
        $uDAO = new PesquisadorDAO();
    }

    $u = $uDAO->obter($id, false);

    if ($u === null) {
        Erro::lancarErro(array("codigo" => 300, "mensagem" => "Usuário não encontrado."));
    } else {
        $uDAO->deletar($u->getId(), true);

        // $assunto = "[LRX] Cadastro não autorizado";
        // $link = $host;
        // $mensagem = "<p>Olá professor " . $u->getNome() . ",<br>confirmamos seu cargo de professor e liberamos seu cadastro 
        //     para solicitações.</p>
        //     <p>Acesse o sistema em <a href='" . $link . "' target='_blank'>" . $link . "</a> e cadastre individualmente seus alunos
        //     na opção <strong>Vincular Aluno</strong> do menu <strong>Alunos</strong> para que também possam fazer solicitações.</p>
        //     <p>Observe que todas as solicitações, tanto suas quanto de seus alunos, <span style='color:red;'>devem ser aprovadas pelo senhor</span> 
        //     antes de serem enviadas ao laboratório. Observe também que cada professor possui um limite todal de vinte solicitações
        //     simultâneas em andamento, somadas as suas e a de seus alunos. Contamos, portanto, com sua colaboração para que não forneça
        //     seus dados de login arbitrariamente para seus alunos, deixe que tenham cada qual seu próprio cadastro.</p>
        //     <p>Agradecemos a compreensão e seja bem vindo!<br>Equipe LRX</p>";
        // $correio = new Correio($u->getEmail(), $assunto, $mensagem);
        // $correio->enviar();

        header('Content-Type: application/json');
        echo json_encode(array("codigo" => 200, "mensagem" => "O usuário " . $u->getNome() . " foi deletado."));
    }
}


/********* PREAMBULO ********/
//echo date_default_timezone_get();

/********** TESTES **********/
