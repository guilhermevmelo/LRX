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
    private $percentage;

    private function __construct($percentage) {
        $this->codigo = strtoupper(uniqid());
        $this->percentage = $percentage;
    }

    public static function getCupom(float $percentage) {
        return new Cupom($percentage);
    }

    public function getCodigo() {
        return $this->codigo;
    }
}