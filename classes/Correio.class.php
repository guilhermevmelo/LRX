<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 17/11/16
 * Time: 10:22
 */

namespace LRX;


class Correio {
    private $cabecalhos;
    private $assunto;
    private $destinatario;
    private $mensagem;
    private $corpo_da_mensagem;

    public function __construct($destinatario = "", $assunto = "", $mensagem = "") {
        $this->cabecalhos  = 'MIME-Version: 1.0' . "\r\n";
        $this->cabecalhos .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $this->cabecalhos .= 'From: LRX <naoresponda@raiosx.fisica.ufc.br>' . "\r\n";

        $this->destinatario = $destinatario;

        $this->assunto = $assunto;
        $this->corpo_da_mensagem = $mensagem;
    }

    /**
     * @param string $cabecalhos
     */
    public function addCabecalhos(string $cabecalhos) {
        $this->cabecalhos .= $cabecalhos;
    }

    /**
     * @param string $assunto
     */
    public function setAssunto(string $assunto) {
        $this->assunto = $assunto;
    }

    /**
     * @param string $destinatario
     */
    public function setDestinatario(string $destinatario) {
        $this->destinatario = $destinatario;
    }

    /**
     * @param mixed $mensagem
     */
    public function setMensagem($mensagem) {
        $this->corpo_da_mensagem = $mensagem;
    }

    public function enviar() {
        $this->mensagem = '
            <html>
                <head>
                    <title>'.$this->assunto.'</title>
                </head>
                <body>'.
                    $this->corpo_da_mensagem
              .'</body>
            </html>
        ';


        return mail($this->destinatario, $this->assunto, $this->mensagem, $this->cabecalhos);
    }
}