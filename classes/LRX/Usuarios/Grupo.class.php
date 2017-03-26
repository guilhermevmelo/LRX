<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:35
 */

namespace LRX\Usuarios;



class Grupo {
    protected $id;
    protected $criador;
    protected $responsaveis;
    protected $membros;
    protected $nome;
    protected $sigla;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCriador() {
        return $this->criador;
    }

    /**
     * @param mixed $criador
     */
    public function setCriador($criador) {
        $this->criador = $criador;
    }

    /**
     * @return mixed
     */
    public function getResponsaveis() {
        return $this->responsaveis;
    }

    /**
     * @param mixed $responsaveis
     */
    public function setResponsaveis($responsaveis) {
        $this->responsaveis = $responsaveis;
    }

    /**
     * @return mixed
     */
    public function getMembros() {
        return $this->membros;
    }

    /**
     * @param mixed $membros
     */
    public function setMembros($membros) {
        $this->membros = $membros;
    }

    /**
     * @return mixed
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome) {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getSigla() {
        return $this->sigla;
    }

    /**
     * @param mixed $sigla
     */
    public function setSigla($sigla) {
        $this->sigla = $sigla;
    }


}