<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 29/11/16
 * Time: 15:03
 */

namespace LRX\Correio;


class MalaDireta {
    private $destinatarios;

    function __construct($destinatarios = array()) {
        $this->destinatarios = $destinatarios;
    }

    public function adicionarDestinatario($destinatario) {
        array_push($this->destinatarios, $destinatario);
    }


}