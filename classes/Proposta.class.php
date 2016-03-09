<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/4/16
 * Time: 09:32
 */

namespace LRX;

class Proposta {
    private $id;
    private $solicitacao;
    private $operador;
    private $aceita;
    private $gerada_em;
    private $aceita_em;
    private $boleto_enviado;


    /**
     * Proposta constructor.
     * @param Solicitacao $solicitacao
     * @param int|null $id
     * @param Usuario|null $operador
     * @param bool $aceita
     * @param null $gerada_em
     * @param null $aceita_em
     * @param bool $boleto_enviado
     */
    public function __construct(Solicitacao $solicitacao, int $id = null, Usuario $operador = null, bool $aceita = false, $gerada_em = null, $aceita_em = null, bool $boleto_enviado = false) {
        $this->id = $id;
        $this->solicitacao = $solicitacao;
        $this->operador = $operador;
        $this->aceita = $aceita;
        $this->gerada_em = $gerada_em;
        $this->aceita_em = $aceita_em;
        $this->boleto_enviado = $boleto_enviado;
    }

    /**
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return Solicitacao
     */
    public function getSolicitacao() {
        return $this->solicitacao;
    }

    /**
     * @param Solicitacao $solicitacao
     */
    public function setSolicitacao($solicitacao) {
        $this->solicitacao = $solicitacao;
    }

    /**
     * @return Usuario|null
     */
    public function getOperador() {
        return $this->operador;
    }

    /**
     * @param Usuario|null $operador
     */
    public function setOperador($operador) {
        $this->operador = $operador;
    }

    /**
     * @return boolean
     */
    public function isAceita() {
        return $this->aceita;
    }

    /**
     * @param boolean $aceita
     */
    public function setAceita($aceita) {
        $this->aceita = $aceita;
    }

    /**
     * @return null
     */
    public function getGeradaEm() {
        return $this->gerada_em;
    }

    /**
     * @param null $gerada_em
     */
    public function setGeradaEm($gerada_em) {
        $this->gerada_em = $gerada_em;
    }

    /**
     * @return null
     */
    public function getAceitaEm() {
        return $this->aceita_em;
    }

    /**
     * @param null $aceita_em
     */
    public function setAceitaEm($aceita_em) {
        $this->aceita_em = $aceita_em;
    }

    /**
     * @return boolean
     */
    public function isBoletoEnviado() {
        return $this->boleto_enviado;
    }

    /**
     * @param boolean $boleto_enviado
     */
    public function setBoletoEnviado($boleto_enviado) {
        $this->boleto_enviado = $boleto_enviado;
    }

}