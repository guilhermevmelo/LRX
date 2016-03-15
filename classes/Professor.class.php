<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:42
 */

namespace LRX;

require_once "autoload.php";

class Professor extends UsuarioAcademico {

    public function __construct($nome, $email, $documento, $id = null, $uid = null, $limite = 20) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->documento = $documento;
        $this->limite = $limite;
        $this->uid = $uid ?? $this->gerarUid();
        $this->nivel_acesso = 2;
    }

}