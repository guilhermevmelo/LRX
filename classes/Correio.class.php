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
        $this->mensagem = '
            <html>
                <head>
                    <title>'.$this->assunto.'</title>
                    <style type="text/css">
                    @font-face {
                        font-family: "DINPro";
                        src:    url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Regular.eot?#iefix") format("embedded-opentype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Regular.otf")  format("opentype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Regular.woff") format("woff"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Regular.ttf")  format("truetype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Regular.svg#DINPro-Regular") format("svg");
                        font-weight: normal;
                        font-style: normal;
                    }
                    
                    @font-face {
                        font-family: "DINPro";
                        src:    url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Light.eot?#iefix") format("embedded-opentype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Light.otf")  format("opentype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Light.woff") format("woff"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Light.ttf")  format("truetype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Light.svg#DINPro-Light") format("svg");
                        font-weight: 100;
                        font-style: normal;
                    }
                    
                    
                    @font-face {
                        font-family: "DINPro";
                        src:    url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Bold.eot?#iefix") format("embedded-opentype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Bold.otf")  format("opentype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Bold.woff") format("woff"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Bold.ttf")  format("truetype"),
                                url("http://guilhermevieira.com.br/raiosx/estilos/fontes/DINPro/DINPro-Bold.svg#DINPro-Bold") format("svg");
                        font-weight: bold;
                        font-style: normal;
                    }
                    
                    * {margin: 0; padding: 0;}
                    body {max-width: 600px; font-family: "DinPro", sans-serif;}
                    p {margin: 20px 6.666666%; line-height: 1.5em;}
                    div, div img {max-width: 100%;}
                    </style>
                </head>
                <body>
                <div><img src="http://guilhermevieira.com.br/raiosx/imagens/lrx_email_cabecalho.gif" alt="Laboratório de Raios X"></div>
                '.
            $this->corpo_da_mensagem
            .'
                <div><img src="http://guilhermevieira.com.br/raiosx/imagens/lrx_email_assinatura.gif" alt="Bloco 928, sala 34 - Campus do Pici"></div></body>
            </html>
        ';
    }

    /**
     *
     */
    public function visualizar() {
        echo $this->mensagem;
    }

    /**
     * @return bool
     */
    public function enviar() {
        return mail($this->destinatario, $this->assunto, $this->mensagem, $this->cabecalhos);
    }
}