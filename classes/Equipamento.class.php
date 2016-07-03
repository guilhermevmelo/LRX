<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:27
 */

namespace LRX;

require_once "autoload.php";

class Equipamento {
    const TODOS = 'TODOS';

    const TIPO_DIFRATOMETRO = 'DRX';
    const TIPO_FLUORESCENCIA = 'FRX';

    const TUBO_CU = 'Cu';
    const TUBO_CO = 'Co';
    const TUBO_PD = 'Pd';

    private $id = null;
    private $nome;
    private $tipo;      // [FRX, DRX]
    private $tubo;      // [Cu, Co, Pd]
    private $disponivel = false;    // [false => indisponível, true => disponível]
    private $servicos;

    public function __construct($id = null, $nome = null, $tipo = null, $tubo = null, $disponivel = null, $servicos =
    null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->tubo = $tubo;
        $this->disponivel = $disponivel;
        $this->servicos = $servicos;
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
     * @return null
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * @param null $nome
     */
    public function setNome($nome) {
        $this->nome = $nome;
    }

    /**
     * @return null
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * @param null $tipo
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    /**
     * @return null
     */
    public function getTubo() {
        return $this->tubo;
    }

    /**
     * @param null $tubo
     */
    public function setTubo($tubo) {
        $this->tubo = $tubo;
    }

    /**
     * @return null
     */
    public function disponivel() {
        return $this->disponivel;
    }

    /**
     * @param null $disponivel
     */
    public function disponibilizar() {
        $this->disponivel = true;
    }

    /**
     * @return null
     */
    public function getServicos() {
        return $this->servicos;
    }

    /**
     * @param null $servicos
     */
    public function setServicos($servicos) {
        $this->servicos = $servicos;
    }

}