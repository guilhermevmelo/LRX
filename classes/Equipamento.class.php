<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:27
 */

namespace LRX;

require_once "autoload.php";

class Equipamento {
    private $id;
    private $nome;
    private $tipo;
    private $tubo;
    private $stauts;
    private $servicos;

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }
}