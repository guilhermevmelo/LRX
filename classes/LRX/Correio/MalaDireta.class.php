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
    private $modelo;

    function __construct($modelo = array("assunto" => "", "mensagem" => ""), $destinatarios = array()) {
        $this->destinatarios = $destinatarios;
        $this->modelo = $modelo;
    }

    public function adicionarDestinatario($destinatario) {
        array_push($this->destinatarios, $destinatario);
    }

    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    public function enviar($verbose = false) {
        if ($verbose) echo "<ul><li>" /*. (new \DateTime())->format("d.m.Y H:i:s.u") */."Início do processo</li>";
        foreach ($this->destinatarios as $d) {
            $assunto = $this->modelo["assunto"];
            $mensagem = $this->modelo["mensagem"];
            foreach ($d["campos"] as $campo) {
                $assunto = str_replace('{'.$campo["chave"].'}', $campo["valor"], $assunto);
                $mensagem = str_replace('{'.$campo["chave"].'}', $campo["valor"], $mensagem);
            }
            $email = new Correio($d["email"], $assunto, $mensagem);
            if ($email->enviar()) {
                if ($verbose) echo "<li>" /*. (new \DateTime())->format("d.m.Y H:i:s.u") */. "Email enviado para " . $d["email"] . "</li>";
            } else {
                if ($verbose) echo "<li>"/*.(new \DateTime())->format("d.m.Y H:i:s.u")*/."Falha no envio para ".$d["email"]."</li>";
            }
        }
        if ($verbose) echo "<li>" /*. (new \DateTime())->format("d.m.Y H:i:s.u") */."Término do processo</li></ul>";
    }
}