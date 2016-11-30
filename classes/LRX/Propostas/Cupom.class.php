<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 06/12/15
 * Time: 01:11
 */

namespace LRX\Propostas;



/**
 * Class Cupom Representa a entidade Cupom
 * @package LRX
 */
class Cupom {
    /**
     * @var int|null
     */
    private $id = null;
    /**
     * @var null|int
     */
    private $codigo = null;
    /**
     * @var float [0, 1] Porcentagem em Desconto | ]1,+inf[ desconto em valor absoluto.
     */
    private $proposta = null;
    /**
     * @var float|int
     */
    private $desconto = 0;
    /**
     * @var bool
     */
    private $usado = false;


    /**
     * Cupom construtor.
     * @param float $desconto Dependendo do intervaldo do valor, representa: [0, 1] desconto em porcentagem | ]1,+inf[
     * desconto em valor absoluto.
     * @param string|null $codigo
     * @param bool $usado
     * @param int|null $id
     * @param Proposta|null $proposta
     */
    public function __construct(float $desconto, string $codigo = null, bool $usado = false, int $id = null, Proposta
    $proposta = null) {
        $this->codigo = $codigo ?? $desconto*100 . strtoupper(uniqid());
        $this->desconto = $desconto;
        $this->usado = $usado;
        $this->id = $id;
        $this->proposta = $proposta;
    }

    /**
     * Gera um novo cupom de acordo com o padrão definido e com valor de desconto passado por parâmetro.
     *
     * @param float $desconto Dependendo do intervaldo do valor, representa: [0, 1] desconto em porcentagem | ]1,+inf[
     * desconto em valor absoluto.
     * @return Cupom
     */
    public static function gerarCupom(float $desconto) {
        return new Cupom($desconto);
    }

    /**
     * @return null|string
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getDesconto() {
        return $this->desconto;
    }

    /**
     * @return boolean
     */
    public function foiUsado() {
        return $this->usado;
    }

    /**
     * @return Proposta
     */
    public function getProposta() {
        return $this->proposta;
    }

    /**
     * @param Proposta $proposta
     */
    public function setProposta($proposta) {
        $this->proposta = $proposta;
    }

    /**
     * Modifica o valor de usado para true, apenas caso já não seja.
     * @return bool true caso o valor de usado seja modificado | false caso o valor de usado já seja true.
     */
    public function usar() {
        if ($this->usado == true)
            return false;

        $this->usado = true;
        return true;
    }
}