<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/2/16
 * Time: 15:51
 */

namespace LRX;

class Erro {
    private function __construct() {

    }

    public static function lancarErro(array $erro) {
        header('Content-type: application/json');
        array_merge($erro, array('tipo_resposta' => 'erro'));
        echo json_encode($erro);
    }
}