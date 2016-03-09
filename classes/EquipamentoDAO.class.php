<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 2/15/16
 * Time: 14:51
 */

namespace LRX;

require_once "autoload.php";

class EquipamentoDAO implements DAO {
    private $equipamento = null;
    private $conexao = null;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar($equipamento) {
        if ($this->conexao === null)
            return null;

        $sql = sprintf("insert into equipamentos values(null, :nome, :tipo, :tubo, :status)");
        $preparedStatement = $this->conexao->prepare($sql);
        $preparedStatement->bindParam(':nome', $equipamento->nome);
        $preparedStatement->bindParam(':tipo', $equipamento->tipo);
        $preparedStatement->bindParam(':tubo', $equipamento->tubo);
        $preparedStatement->bindParam(':status', $equipamento->status);




    }

    public function obter($id) {
        // TODO: Implement ler() method.
    }

    public function atualizar($equipamento) {
        // TODO: Implement atualizar() method.
    }

    public function deletar($id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() {
        // TODO: Implement obterTodos() method.
    }
}