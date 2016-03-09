<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 2/15/16
 * Time: 14:58
 */

namespace LRX;

$configuracoes = parse_ini_file(__DIR__."/../configuracao.ini", true);

define("SGBD", $configuracoes['BANCO_DE_DADOS']['sgbd']);
define("PORTA", $configuracoes['BANCO_DE_DADOS']['porta']);
define("SERVIDOR", $configuracoes['BANCO_DE_DADOS']['servidor']);
define("BANCO", $configuracoes['BANCO_DE_DADOS']['banco']);
define("USUARIO", $configuracoes['BANCO_DE_DADOS']['usuario']);
define("SENHA", $configuracoes['BANCO_DE_DADOS']['senha']);

define("DSN", SGBD.":host=".SERVIDOR.";port=".PORTA.";dbname=".BANCO);
