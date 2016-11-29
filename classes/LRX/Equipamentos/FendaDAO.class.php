<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/30/16
 * Time: 14:00
 */

namespace LRX\Equipamentos;

use LRX\Erro;

require_once "../autoload.php";

/**
 * Class FendaDAO
 * @package LRX
 */
class FendaDAO /*extends DAO*/ {
    /**
     * @var \PDO
     */
    private $conexao;

    /**
     * FendaDAO constructor.
     */
    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    /**
     * @param Fenda $fenda
     * @return bool
     */
    public function criar(Fenda $fenda) {
        if ($this->existe($fenda->getId()))
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("insert into fendas values (null, :nome, :disponivel)");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(':nome', $fenda->getNome());
            $consulta->bindValue(':disponivel', $fenda->disponivel(), \PDO::PARAM_BOOL);
            $consulta->execute();
            \LRX\print_p($consulta->errorInfo());

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return false;
        }
    }

    /**
     * @param $id
     * @return bool|Fenda
     */
    public function obter($id) {
        $sql = sprintf("select * from fendas where id_fenda = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        $f = new Fenda($tupla['nome'], $tupla['disponivel'] == 1 ? true : false, $tupla['id_fenda']);
        return $f;
    }

    /**
     * @param Fenda $fenda
     * @return bool
     */
    public function atualizar(Fenda $fenda) {
        $fenda_antiga = $this->obter($fenda->getId());
        if ($fenda_antiga === false)
            return false;

        try {
            $this->conexao->beginTransaction();

            $sql = sprintf("update fendas set nome = :nome, disponivel = :disponivel where id_fenda = :id");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(':nome', $fenda->getNome());
            $consulta->bindValue(':id', $fenda->getId());
            $consulta->bindValue(':disponivel', $fenda->disponivel(), \PDO::PARAM_BOOL);

            $consulta->execute();
            \LRX\print_p($consulta->errorInfo());

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return false;
        }
    }

    /**
     * @param $id
     */
    public function deletar($id) {
        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("delete from fendas where id_fenda = :id");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindParam(':id', $id);
            $consulta->execute();
            $this->conexao->commit();
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
        }
    }

    /**
     * @param bool $apenas_disponiveis
     * @return array
     */
    public function obterTodos($apenas_disponiveis = false) {
        $sql = sprintf("select * from fendas");
        if ($apenas_disponiveis)
            $sql .= sprintf(" where disponivel = true");
        $sql .= sprintf(" order by id_fenda asc");
        $fendas = array();

        foreach ($this->conexao->query($sql) as $tupla) {
            $f = new Fenda($tupla['nome'], $tupla['disponivel'] == 1 ? true : false, $tupla['id_fenda']);
            array_push($fendas, $f);
        }

        return $fendas;
    }

    /**
     * @param $id
     * @return bool
     */
    public function existe($id) {
        $sql = sprintf("select * from fendas where id_fenda = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindParam(':id', $id);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }
}