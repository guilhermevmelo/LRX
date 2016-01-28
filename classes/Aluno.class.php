<?php

/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:39
 */

namespace LRX;

require_once "autoload.php";

class Aluno extends UsuarioAcademico {
    private $professor; // LRX\Professor
    private $vinculo;
}