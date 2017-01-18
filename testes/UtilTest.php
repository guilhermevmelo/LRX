<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/12/15
 * Time: 20:09
 */

namespace LRX;

use LRX\Solicitacoes\Solicitacao;
use LRX\Usuarios\Usuario;
use PHPUnit_Framework_TestCase;
use function LRX\obterIniciais;

require_once __DIR__ . "/../classes/LRX/autoload.php";

class UtilTest extends PHPUnit_Framework_TestCase {
    
    public function testObterIniciais() {
        $this->assertEquals("GVM", obterIniciais("Guilherme Vieira Melo"));
    }

}
