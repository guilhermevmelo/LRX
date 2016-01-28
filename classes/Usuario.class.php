<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:26
 */

namespace LRX;

require_once "autoload.php";

abstract class Usuario {
    protected $id;
    protected $documento;   // CPF ou CNPJ
    protected $nome;
    protected $email;
    protected $senha;       // Hash SHA1
    protected $telefone;
    protected $nivel_acesso;
    protected $confirmado;
    protected $uid;
    protected $mensagens;

    public function __set($atributo, $valor) {
        if ($atributo == 'senhaAberta') {
            $valor = sha1($valor);
            $atributo = 'senha';
        }

        $this->$atributo = $valor;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }

}