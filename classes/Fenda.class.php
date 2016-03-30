<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/30/16
 * Time: 13:55
 */

namespace LRX;


class Fenda {
    private $id = null;
    private $nome;
    private $disponivel = false;

    public function __construct($nome, $disponivel, $id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->disponivel = $disponivel;
    }

    /**
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id) {
        $this->id = $id;
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
     * @return boolean
     */
    public function disponivel() {
        return $this->disponivel;
    }

    /**
     * @param boolean $disponivel
     */
    public function disponibilizar() {
        $this->disponivel = true;
    }
}