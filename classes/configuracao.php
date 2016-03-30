<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 2/15/16
 * Time: 14:58
 */

namespace LRX;

$configuracoes = parse_ini_file(__DIR__."/../configuracao.ini", true);

/**
 * SGBD string  O SGBD utilizado na instalação da aplicação.
 */
define("SGBD", $configuracoes['BANCO_DE_DADOS']['sgbd']);
/**
 * PORTA int    A porta do servidor de banco de dados.
 */
define("PORTA", $configuracoes['BANCO_DE_DADOS']['porta']);
/**
 * SERVIDOR string  O endereço do servidor de banco de dados.
 */
define("SERVIDOR", $configuracoes['BANCO_DE_DADOS']['servidor']);
/**
 * BANCO string O nome do esquema de banco de dados.
 */
define("BANCO", $configuracoes['BANCO_DE_DADOS']['banco']);
/**
 * USUARIO string O usuário do banco de dados a ser utilizado pela aplicação.
 */
define("USUARIO", $configuracoes['BANCO_DE_DADOS']['usuario']);
/**
 * SENHA string A senha para o usuário fornecido.
 */
define("SENHA", $configuracoes['BANCO_DE_DADOS']['senha']);

/**
 * DSN string A string DSN a ser utilizada pela extensão PDO.   
 */
define("DSN", SGBD.":host=".SERVIDOR.";port=".PORTA.";dbname=".BANCO);
