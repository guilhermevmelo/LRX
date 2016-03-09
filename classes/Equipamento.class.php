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
    private $tipo;      // [FRX, DRX]
    private $tubo;      // [Cu, Co, Pd]
    private $status;    // [0 => indisponível, 1 => disponível]
    private $servicos;

    public function __construct($id = null, $nome = null, $tipo = null, $tubo = null, $status = null, $servicos =
    null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->tubo = $tubo;
        $this->status = $status;
        $this->servicos = $servicos;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }
}