<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:56
 */

namespace LRX\Solicitacoes;



abstract class Resultado {
    protected $id;
    protected $operador;
    protected $data_envio;
    protected $url_arquivo;
    protected $arquivo;

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }

    public abstract function carregarArquivo($arquivo = null);

    public abstract function analisarArquivo();
}