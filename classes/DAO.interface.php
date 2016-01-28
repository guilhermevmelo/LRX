<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 20:36
 */

namespace LRX;

require_once "autoload.php";

interface DAO {
    public function criar($objeto);
    public function ler($id);
    public function atualizar($objeto);
    public function deletar($id);

    public function obterTodos();
}