<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:31
 */

namespace LRX;

require_once "autoload.php";

/**
 * Class UsuarioAcademico
 * @package LRX
 */
abstract class UsuarioAcademico extends Usuario {
    /**
     * @var
     */
    protected $departamento;
    /**
     * @var
     */
    protected $laboratorio;
    /**
     * @var
     */
    protected $area_de_pesquisa;
    /**
     * @var
     */
    protected $grupo;

    /**
     * @var
     */
    protected $limite;

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


    /**
     * @return mixed
     */
    public function getDepartamento() {
        return $this->departamento;
    }

    /**
     * @param mixed $departamento
     */
    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    /**
     * @return mixed
     */
    public function getLaboratorio() {
        return $this->laboratorio;
    }

    /**
     * @param mixed $laboratorio
     */
    public function setLaboratorio($laboratorio) {
        $this->laboratorio = $laboratorio;
    }

    /**
     * @return mixed
     */
    public function getAreaDePesquisa() {
        return $this->area_de_pesquisa;
    }

    /**
     * @param mixed $area_de_pesquisa
     */
    public function setAreaDePesquisa($area_de_pesquisa) {
        $this->area_de_pesquisa = $area_de_pesquisa;
    }

    /**
     * @return mixed
     */
    public function getGrupo() {
        return $this->grupo;
    }

    /**
     * @param mixed $grupo
     */
    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }         // \LRX\Grupo


}