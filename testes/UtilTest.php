<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/12/15
 * Time: 20:09
 */

namespace LRX;

require_once __DIR__."/../classes/autoload.php";

class UtilTest extends \PHPUnit_Framework_TestCase {

//    public function test

    public function testObterIniciais() {
        $s = new Solicitacao();
        $u = new Usuario();
        $u->nome = "Guilherme Vieira Melo";
        $s->usuario = $u;
        $s->gerarIdentificacao(false);
        $this->assertEquals("GVM", $s->gerarIdentificacao(true));

        $u->nome = "Barbara Marques Alves";
        $this->assertEquals("BMA", $s->gerarIdentificacao(true));

        $u->nome = "";
        $this->assertEquals("", $s->gerarIdentificacao(true));
    }

}
