<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/10/16
 * Time: 15:45
 */

namespace LRX\Usuarios;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;

class GrupoDAO {

    private $conexao = null;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar(Grupo $grupo) {
        // TODO: Implement criar() method.
    }

    public function obter(int $id) : Grupo {
        // TODO: Implement obter() method.
        return null;
    }

    public function atualizar(Grupo &$grupo) {
        // TODO: Implement atualizar() method.
    }

    public function deletar(int $id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() : array {
        // TODO: Implement obterTodos() method.
        return array();
    }
}