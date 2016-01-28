<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 19:07
 */

namespace LRX;

require_once "autoload.php";

class Projeto {
    protected $id;
    protected $responsavel; //LRX\Usuario
    protected $nome;
    protected $descricao;
    protected $data_criacao;
    protected $instituicao;
    protected $status;
    protected $membros;

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }
}