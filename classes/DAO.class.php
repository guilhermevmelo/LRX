<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:36
 */

namespace LRX;

require_once "autoload.php";

abstract class DAO {
    public abstract function criar($objeto);
    public abstract function obter($id);
    public abstract function atualizar($objeto);
    public abstract function deletar($id);

    public abstract function obterTodos();
    public abstract function existe();
}