<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:42
 */

namespace LRX;

require_once "autoload.php";

class Professor extends UsuarioAcademico {
    private $limite;
    private $grupo;         // \LRX\Grupo
}