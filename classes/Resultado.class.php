<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:56
 */

namespace LRX;

require_once "autoload.php";

abstract class Resultado {
    protected $id;
    protected $operador;
    protected $data_envio;
    protected $url_arquivo;

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }

    public abstract function analisarArquivo();
}