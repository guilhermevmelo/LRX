<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/4/16
 * Time: 09:14
 */

namespace LRX\Propostas;

use LRX\Erro;



/**
 * Class CupomDAO Gerencia as transações no banco de dados para a classe LRX\Cupom.
 * @package LRX
 */
class CupomDAO /*extends DAO*/ {
    /**
     * @var null|\PDO uma instância de conexão com o banco de dados.
     */
    private $conexao = null;

    /**
     * CupomDAO construtor.
     */
    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    /**
     * Adiciona um Cupom ao banco de dados.
     *
     * @param Cupom $cupom O cupom a ser adicionado ao banco de dados.
     */
    public function criar(Cupom $cupom) {
        try {
            $this->conexao->beginTransaction();

            $sql = sprintf("insert into cupons (id_cupom, id_proposta, codigo, desconto, usado) values (null, :id_proposta, :codigo, :desconto,
:usado)");

            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':desconto', $cupom->getDesconto());
            $consulta->bindValue(':codigo', $cupom->getCodigo());
            $consulta->bindValue(':usado', $cupom->foiUsado(), \PDO::PARAM_BOOL);
            $valor_id_proposta = $cupom->getProposta() != null ? $cupom->getProposta()->getId() : null;
            $consulta->bindValue(':id_proposta', $valor_id_proposta);

            $consulta->execute();

            $this->conexao->commit();
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
        }

    }

    /**
     * Obtém um Cupom do banco de dados dado o id dele.
     *
     * @param int $id Valor do id do Cupom a ser obtido do banco de dados.
     * @return bool|Cupom Retorna false caso não haja um cupom com o id solicitado ou o Cupom, caso haja.
     */
    public function obter(int $id) {
        $sql = sprintf("select * from cupons where id_cupom = :id_cupom limit 1");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id_cupom", $id);
        $consulta->execute();
        
        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);
        if ($tupla === false)
            return false;

        return new Cupom($tupla["desconto"], $tupla["codigo"], $tupla["usado"] === 0 ? false : true,
            $tupla["id_cupom"]);
    }

    /**
     * Obtém um Cupom do banco de dados dado o código dele.
     *
     * @param string $codigo Valor do código a do Cupom a ser obtido do banco de dados.
     * @return bool|Cupom Retorna false caso não haja um cupom com o codigo solicitado ou o Cupom, caso haja.
     */
    public function obterPorCodigo(string $codigo) {
        $sql = sprintf("select * from cupons where codigo = :codigo limit 1");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":codigo", $codigo);
        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);
        if ($tupla === false)
            return false;

        return new Cupom($tupla["desconto"], $tupla["codigo"], $tupla["usado"] === 0 ? false : true,
            $tupla["id_cupom"]);
    }

    /**
     * Atualiza os atributos de um Cupom existente no banco de dados
     *
     * @param Cupom $cupom O Cupom a ser atualizado.
     * @return bool Retorna false caso o Cupom a ser atualizado não exista, ou ocorra um erro; true caso tudo ocorra
     * corretamente.
     */
    public function atualizar(Cupom $cupom) {
        $cupom_antigo = $this->obter($cupom->getId());
        if ($cupom_antigo === false)
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("update cupons set desconto = :desconto, codigo = :codigo, usado = :usado, id_proposta = :id_proposta where id_cupom =
 :id_cupom");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(":id_cupom", $cupom->getId());
            $consulta->bindValue(":desconto", $cupom->getDesconto());
            $consulta->bindValue(":codigo", $cupom->getCodigo());
            $consulta->bindValue(":usado", $cupom->foiUsado(), \PDO::PARAM_BOOL);

            $valor_id_proposta = $cupom->getProposta() != null ? $cupom->getProposta()->getId() : null;
            $consulta->bindValue(':id_proposta', $valor_id_proposta);

            $consulta->execute();

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
     * Remove do banco de dados o Cupom cujo id corresponda ao parâmentro.
     *
     * @param int $id O id do Cupom a ser removido do banco de dados.
     */
    public function deletar(int $id) {
        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("delete from cupons where id_cupom = :id_cupom limit 1");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue("id_cupom", $id);
            $consulta->execute();
            $this->conexao->commit();
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
        }
    }

    /**
     * Obtém todos os Cupons cadastrados no banco de dados.
     *
     * @return array Um array de Cupons com todos os cupons do banco de dados.
     */
    public function obterTodos() {
        $sql = sprintf("select * from cupons order by id_cupom desc");
        $cupons = array();

        foreach ($this->conexao->query($sql) as $tupla) {
            // TODO: Adicionar a Proposta a cada Cupom.
            $c = new Cupom($tupla["desconto"], $tupla["codigo"], $tupla["usado"] == 0 ? false : true,
                $tupla["id_cupom"]);

            array_push($cupons, $c);
        }

        return $cupons;
    }

}