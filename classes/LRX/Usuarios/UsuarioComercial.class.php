<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:36
 */

namespace LRX\Usuarios;



abstract class UsuarioComercial extends Usuario {
    protected $empresa = null;
    protected $limite;

    /**
     * @return null
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param null $empresa
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * @return mixed
     */
    public function getLimite() {
        return $this->limite;
    }

    /**
     * @param mixed $limite
     */
    public function setLimite($limite) {
        $this->limite = $limite;
    }



}