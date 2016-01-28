<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:51
 */

namespace LRX;

require_once "autoload.php";

class Mensagem {
    private $id;
    private $remetente;
    private $data_envio;
    private $corpo;
    private $lida;

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }
}