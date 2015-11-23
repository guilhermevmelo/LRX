<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:26
 */

namespace LRX;


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

    public function __set($name, $value) {
        if ($name == 'senhaNaoEncriptada') {
            $value = sha1($value);
            $name = 'senha';
        }

        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

}