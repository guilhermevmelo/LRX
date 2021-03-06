<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:27
 */

namespace LRX\Equipamentos;



class Equipamento {
    const TODOS = 'TODOS';

    const TIPO_DIFRATOMETRO = 'DRX';
    const TIPO_FLUORESCENCIA = 'FRX';

    const TUBO_CU = 'Cu';
    const TUBO_CO = 'Co';
    const TUBO_PD = 'Pd';
    const TUBO_RH = 'Rh';

    private $id = null;
    private $nome;
    private $tipo;      // [FRX, DRX]
    private $tubo;      // [Cu, Co, Pd, Rh]
    private $disponivel = false;    // [false => indisponível, true => disponível]
    private $servicos;
    private $obs;

    public function __construct($id = null, $nome = null, $tipo = null, $tubo = null, $disponivel = false, $servicos =
    null, $obs = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->tubo = $tubo;
        $this->disponivel = $disponivel;
        $this->servicos = $servicos;
        $this->obs = $obs;
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
	 * @return bool
	 */
    public function getDisponivel() {
        return $this->disponivel;
    }

	/**
	 * @param bool $disponivel
	 */
    public function setDisponivel(bool $disponivel = true) {
        $this->disponivel = $disponivel;
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

	/**
	 * @return null
	 */
	public function getObs() {
		return $this->obs;
	}

	/**
	 * @param null $obs
	 */
	public function setObs( $obs ) {
		$this->obs = $obs;
	}
}