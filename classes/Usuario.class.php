<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:26
 */

namespace LRX;

require_once "autoload.php";

class Usuario {
    private $id_usuario;
    private $cpf;
    private $nome;
    private $email;
    private $senha;         // Hash SHA1
    private $telefone;
    private $nivel_acesso;
    private $confirmado;
    private $uid;
    private $limite;
    private $grupo;         // \LRX\Grupo

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