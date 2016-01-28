<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 06/12/15
 * Time: 01:11
 */

namespace LRX;


class Cupom {
    private $codigo = null;
    private $desconto;

    private function __construct(float $desconto, string $codigo = null) {
        $this->codigo = $codigo ?? strtoupper(uniqid());
        $this->desconto = $desconto;
    }

    public static function generateCupom(float $desconto) {
        return new Cupom($desconto);
    }

    public function getCodigo() {
        return $this->codigo;
    }
}