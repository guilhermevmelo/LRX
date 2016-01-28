<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:35
 */

namespace LRX;

require_once "autoload.php";

class Grupo {
    protected $id;
    protected $criador;
    protected $responsaveis;
    protected $membros;
    protected $nome;
    protected $sigla;

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }
}