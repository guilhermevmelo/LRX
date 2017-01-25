<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/2/16
 * Time: 15:51
 */

namespace LRX;

class Erro {
    const OK = 200;
    const ERRO_PERMISSAO_NEGADA = 300;
    const ERRO_ARQUIVO_INVALIDO = 301;
    const ERRO_USUARIO_NAO_ENCONTRADO = 1001;
    const ERRO_EMAIL_NAO_CONFIRMADO = 1002;
    const ERRO_CADASTRO_NAO_LIBERADO = 1003;
    const ERRO_DE_UPLOAD = 2001;
    const ERRO_LIMITE_SOLICITANTE_ANTIGIDO = 4001;
    const ERRO_LIMITE_PROFESSOR_ANTIGIDO = 4002;
    const ERRO_CONFIRMAR_USUARIO_NAO_PROFESSOR = 5000;
    const ERRO_SOLICITACAO_INEXISTENTE = 6000;
    const ERRO_STATUS_INEXISTENTE = 6001;

    private function __construct() {

    }

    public static function lancarErro(array $erro) {
        header('Content-type: application/json');
        array_merge($erro, array('tipo_resposta' => 'erro'));
        echo json_encode($erro);
    }
}