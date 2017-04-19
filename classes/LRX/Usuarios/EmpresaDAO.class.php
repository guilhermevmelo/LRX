<?php
/**
 * Created by PhpStorm.
 * User: romulo
 * Date: 27/03/17
 * Time: 13:20
 */

namespace LRX\Usuarios;

use LRX\Erro;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;


class EmpresaDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar(Empresa $empresa) {
        if ($this->existeCnpj($empresa->getCnpj()))
            return false;

        try {

            $this->conexao->beginTransaction();

            $sql = sprintf("INSERT INTO empresas VALUES (null, 2, :documento, :nome, :sigla, :endereco, :inscricao_estadual, :inscricao_municipal, null, :site)");
            $consulta = $this->conexao->prepare($sql);

            //INSERT INTO empresas VALUES (null, 2, '48353227000147', 'riachuelo', 'rchlo', '{rua:augusta}', '', '', null, null)

            $consulta->bindValue(':documento', $empresa->getCnpj());
            $consulta->bindValue(':nome', $empresa->getRazaoNome());
            $consulta->bindValue(':sigla', $empresa->getSigla());
            $consulta->bindValue(':endereco', json_encode($empresa->getEndereco()));
            $consulta->bindValue(':inscricao_estadual', $empresa->getInscricaoEstadual());
            $consulta->bindValue(':inscricao_municipal', $empresa->getInscricaoMunicipal());
            $consulta->bindValue(':site', $empresa->getSite());
            $consulta->execute();

            \LRX\print_p($consulta->errorInfo());

            $sql2 = sprintf("select max(id_empresa) from empresas");
            $consulta2 = $this->conexao->query($sql2);

            $novoId = (int) $consulta2->fetchColumn(0);

            $empresa->setId($novoId);

            $this->conexao->commit();

            return $empresa;

        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo' => $pdoe->getCode(),
                'mensagem' => $pdoe->getMessage()));
            return false;
        }
    }

    private function existeCnpj(int $cnpj) : bool {
        $conexao = new \PDO(DSN, USUARIO, SENHA);

        $sql = sprintf("SELECT * FROM empresas WHERE documento = :documento");

        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':documento', $cnpj);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public function obter($id_empresa) : Empresa {

        $empresa = null;

        $conexao = new \PDO(DSN, USUARIO, SENHA);

        $sql = sprintf("SELECT * FROM empresas WHERE empresa_id = :id");

        $consulta = $conexao->prepare($sql);

        $consulta->bindValue(':id', $id_empresa);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla !== null) {
            $empresa = new Empresa($tupla['documento'], $tupla['nome'], $tupla['sigla'], $tupla['endereco'], $tupla['inscricao_estadual'], $tupla['inscricao_municipal'], $tupla['site']);
            $empresa->setId($tupla['id_empresa']);
        }

        return $empresa;

    }

    public function obterPorCnpj(String $cnpj) : Empresa {

        $empresa = null;

        $conexao = new \PDO(DSN, USUARIO, SENHA);

        $sql = sprintf("SELECT * FROM empresas WHERE documento = :documento");

        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':documento', $cnpj);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla !== true) {

            $empresa = new Empresa($tupla['documento'], $tupla['nome'], $tupla['sigla'], $tupla['endereco'], $tupla['inscricao_estadual'], $tupla['inscricao_municipal'], $tupla['site']);
            $empresa->setId($tupla['id_empresa']);

        }

        return $empresa;
    }

}