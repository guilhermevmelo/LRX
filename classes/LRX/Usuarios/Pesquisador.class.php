<?php
/**
 * Created by PhpStorm.
 * User: romulo
 * Date: 27/03/17
 * Time: 12:51
 */

namespace LRX\Usuarios;


class Pesquisador extends UsuarioComercial {
    protected $habilitado;

    public function __construct($nome, $email, $documento, $id = null, $uid = null, $limite = 20, Empresa $empresa = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->documento = $documento;
        $this->limite = $limite;
        $this->empresa = $empresa;
        $this->uid = $uid ?? $this->gerarUid();
        $this->nivel_acesso = 3;
        $this->habilitado = false;
    }

    /**
     * @return bool
     */
    public function estaHabilitado(): bool {
        return $this->habilitado;
    }

    /**
     * @param bool $habilitado
     */
    public function setHabilitado( bool $habilitado ) {
        $this->habilitado = $habilitado;
    }


}