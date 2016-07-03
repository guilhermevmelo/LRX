<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 4/13/16
 * Time: 17:13
 */

namespace LRX;


class SolicitacaoAcademicaDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar(SolicitacaoAcademica $solicitacao) {
        $this->conexao->beginTransaction();
        /* Obtém o número de amostras atuais do usuário solicitante e o incrementa */
        $numAmostra = $this->quantidadePorUsuario($solicitacao->getSolicitante(), $solicitacao->getEquipamento()
                ->getTipo()) + 1;
        /* Adiciona a identificação única gerada pela regra definida em Solicitacao e usando o número obtido
        acima */
        $solicitacao->gerarIdentificacao($numAmostra);

        $sDAO = new SolicitacaoDAO();
        $sDAO->criar($solicitacao);

        $sql = sprintf("insert into solicitacoes_academicas values (:id_solicitacao, :id_usuario)");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_solicitacao', $solicitacao->getId(), \PDO::PARAM_INT);
        $consulta->bindValue('id_usuario', $solicitacao->getSolicitante()->getId(), \PDO::PARAM_INT);
        $consulta->execute();
        print_p($consulta->errorInfo());

        $this->conexao->commit();
    }

    public function obter($id, $emArray = true) {
        $sql = sprintf("select * from solicitacoes_academicas sa, solicitacoes s where sa.id_solicitacao = s.id_solicitacao and sa.id_solicitacao
 = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id, \PDO::PARAM_INT);
        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);
        //print_p($tupla);
        if ($tupla === false)
            return false;
        if ($emArray) {
            $eDAO = new EquipamentoDAO();
            $e = $eDAO->obter(intval($tupla['id_equipamento']));

            // TODO: Adequar ao tipo de usuário solicitante.
            $pDAO = new ProfessorDAO();
            $p = $pDAO->obter(intval($tupla['id_usuario']));

            $s = array(
                "id_solicitacao"    => intval($tupla['id_solicitacao']),
                "id_solicitante"    => intval($tupla['id_usuario']),
                "solicitante"       => $p->getNome(),
                "id_equipamento"    => intval($tupla['id_equipamento']),
                "equipamento"       => $e->getNome(),
                "tipo_equipamento"  => $e->getTipo(),
                "status"            => intval($tupla['status']),
                "data_solicitacao"  => date_create($tupla['data_solicitacao'])->format(\DateTime::W3C),
                "data_entrega"      => $tupla['data_recebimento'] != null ? date_create($tupla['data_recebimento'])
                    ->format(\DateTime::W3C) :
                    null,
                "identificacao"     => $tupla['identificacao_da_amostra'],
                "configuracao"      => json_decode($tupla['configuracao']),
                "composicao"        => $tupla['composicao'],
                "tipo"              => intval($tupla['tipo']),
                "tipo_outro"        => $tupla['tipo_outro'],
                "inflamavel"        => boolval($tupla['inflamavel']),
                "toxico"            => boolval($tupla['toxico']),
                "radioativo"        => boolval($tupla['radioativo']),
                "corrosivo"         => boolval($tupla['corrosivo']),
            );
        } else {
            $s = new SolicitacaoAcademica();
            $s->setId(intval($tupla['id_solicitacao']));
            $s->setSolicitante(intval($tupla['id_usuario']));
            $s->setEquipamento(intval($tupla['id_equipamento']));
            $s->setFenda(intval($tupla['id_fenda']));
            //        TODO: Adicionar Resultado $s->setResultado() 'id_resultado' => null
            $s->setDataSolicitacao(date_create($tupla['data_solicitacao']));
            $s->setDataConclusao(date_create($tupla['data_conclusao']));
            $s->setDataRecebimento(date_create($tupla['data_recebimento']));
            //
            //        'status' => string '1' (length=1)
            //            'configuracao' => string '[]' (length=2)
            //            'identificacao_da_amostra' => string 'GVM001F001' (length=10)
            //            'composicao' => string '' (length=0)
            //            'tipo' => string '1' (length=1)
            //            'tipo_outro' => null
            //            'inflamavel' => string '0' (length=1)
            //            'radioativo' => string '0' (length=1)
            //            'toxico' => string '0' (length=1)
            //            'corrosivo' => string '0' (length=1)
            //            'higroscopico' => string
            //            'seguranca_outro' => null
            //            'observacoes' => null
        }
        return $s;
    }

    public function atualizar(SolicitacaoAcademica $solicitacao) {
        // TODO: Implement atualizar() method.
    }

    public function deletar($id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() {
        // TODO: Implement obterTodos() method.
    }

    public function obterTodosPorUsuario($id, $emArray = true) {
        $sql = sprintf("select * from solicitacoes_academicas sa, solicitacoes s where sa.id_solicitacao = s.id_solicitacao and sa.id_usuario
 = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id, \PDO::PARAM_INT);
        $consulta->execute();

        $solicitacoes = array();

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($emArray) {
                $eDAO = new EquipamentoDAO();
                $e = $eDAO->obter(intval($tupla['id_equipamento']));

                // TODO: Adequar ao tipo de usuário solicitante.
                $pDAO = new ProfessorDAO();
                $p = $pDAO->obter(intval($tupla['id_usuario']));

                $s = array(
                    "id_solicitacao"    => intval($tupla['id_solicitacao']),
                    "id_solicitante"    => intval($tupla['id_usuario']),
                    "solicitante"       => $p->getNome(),
                    "id_equipamento"    => intval($tupla['id_equipamento']),
                    "equipamento"       => $e->getNome(),
                    "tipo_equipamento"  => $e->getTipo(),
                    "status"            => intval($tupla['status']),
                    "data_solicitacao"  => date_create($tupla['data_solicitacao']),
                    "identificacao"     => $tupla['identificacao_da_amostra'],
                    "configuracao"      => json_decode($tupla['configuracao']),
                    "composicao"        => $tupla['composicao'],
                    "tipo"              => intval($tupla['tipo']),
                    "tipo_outro"        => $tupla['tipo_outro'],
                    "inflamavel"        => boolval($tupla['inflamavel']),
                    "toxico"            => boolval($tupla['toxico']),
                    "radioativo"        => boolval($tupla['radioativo']),
                    "corrosivo"         => boolval($tupla['corrosivo']),
                );
            } else {
                $s = new SolicitacaoAcademica();
                $s->setId(intval($tupla['id_solicitacao']));
                $s->setSolicitante(intval($tupla['id_usuario']));
                $s->setEquipamento(intval($tupla['id_equipamento']));
                $s->setFenda(intval($tupla['id_fenda']));
//        TODO: Adicionar Resultado $s->setResultado() 'id_resultado' => null
                $s->setDataSolicitacao(date_create($tupla['data_solicitacao']));
                $s->setDataConclusao(date_create($tupla['data_conclusao']));
                $s->setDataRecebimento(date_create($tupla['data_recebimento']));
                $s->setStatus((int)$tupla['status']);



//            'higroscopico' => string
//            'seguranca_outro' => null
//            'observacoes' => null

            }

            array_push($solicitacoes, $s);
        }

        return $solicitacoes;
    }

    public function existe() {
        // TODO: Implement existe() method.
    }

    public function quantidadePorUsuario(UsuarioAcademico $usuario, $tipo = Equipamento::TODOS) {

        switch ($tipo) {
            case Equipamento::TIPO_DIFRATOMETRO:
                $sql = sprintf("select count(*) 
                                from solicitacoes_academicas sa, solicitacoes s, equipamentos e 
                                where sa.id_solicitacao = s.id_solicitacao 
                                  and s.id_equipamento = e.id_equipamento
                                  and e.tipo = 'DRX'
                                  and sa.id_usuario = :id_usuario");
                break;

            case Equipamento::TIPO_FLUORESCENCIA:
                $sql = sprintf("select count(*) 
                                from solicitacoes_academicas sa, solicitacoes s, equipamentos e 
                                where sa.id_solicitacao = s.id_solicitacao 
                                  and s.id_equipamento = e.id_equipamento
                                  and e.tipo = 'FRX'
                                  and sa.id_usuario = :id_usuario");
                break;


            case Equipamento::TODOS:
                $sql = sprintf("select count(*) from solicitacoes_academicas where id_usuario = :id_usuario");
                break;

            default:
                $sql = sprintf("select count(*) from solicitacoes_academicas where id_usuario = :id_usuario");
                break;
        }
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $usuario->getId());
        $consulta->execute();
        $totalSolicitacoes = $consulta->fetchColumn(0);
        return intval($totalSolicitacoes);
    }
}