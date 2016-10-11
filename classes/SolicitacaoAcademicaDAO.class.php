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
        //print_p($consulta->errorInfo());

        $this->conexao->commit();
    }

    public function obter($id, $emArray = true) {
        $sql = sprintf("select sa.*, s.*, u.nivel_acesso from solicitacoes_academicas sa, solicitacoes s, usuarios u where sa.id_solicitacao = s.id_solicitacao and sa.id_usuario = u.id_usuario and sa.id_solicitacao
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

            // TODO: Terminar adequação ao tipo de usuário solicitante.
            switch(intval($tupla['nivel_acesso'])) {
                case 1:
                    $uDAO = new AlunoDAO();
                    break;

                case 2:
                    $uDAO = new ProfessorDAO();
                    break;

                case 5:
                    $uDAO = new ProfessorDAO();
                    break;

                default:
                    $uDAO = new ProfessorDAO();
            }
            $u = $uDAO->obter(intval($tupla['id_usuario']));

            $s = array(
                "id_solicitacao"    => intval($tupla['id_solicitacao']),
                "id_solicitante"    => intval($tupla['id_usuario']),
                "solicitante"       => $u->getNome(),
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
                "higroscopico"      => boolval($tupla['higroscopico']),
                "seguranca_outro"   => boolval($tupla['seguranca_outro']),
                "observacoes"       => boolval($tupla['observacoes'])
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
            $s->setStatus(intval($tupla['status']));
            $s->setConfiguracao($tupla['configuracao']);
            $s->setIdentificacaoDaAmostra($tupla['identificacao_da_amostra']);
            $s->setComposicao($tupla['composicao']);
            $s->setTipo($tupla['tipo']);
            $s->setTipoOutro($tupla['tipo_outro']);
            $s->setInflamavel(boolval($tupla['inflamavel']));
            $s->setRadioativo(boolval($tupla['radioativo']));
            $s->setToxico(boolval($tupla['toxico']));
            $s->setCorrosivo(boolval($tupla['corrosivo']));
            $s->setHigroscopico(boolval($tupla['higroscopico']));
            $s->setSegurancaOutro($tupla['seguranca_outro']);
            $s->setObservacoes($tupla['observacoes']);
        }
        return $s;
    }

    public function atualizar(SolicitacaoAcademica $solicitacao) {
        $solicitacao_antiga = $this->existe($solicitacao->getId());
        
        if ($solicitacao_antiga === false)
            return false;

        try{
            $this->conexao->beginTransaction();

            $sDAO = new SolicitacaoDAO();
            $sDAO->atualizar($solicitacao);

            $sql = sprintf("update solicitacoes_academicas set id_solicitacao = :id_solicitacao, id_usuario =
            :id_usuario where 
            id_solicitacao = :id_solicitacao");

            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(':id_solicitacao', $solicitacao->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':id_usuario', $solicitacao->getSolicitante(), \PDO::PARAM_INT);
            $consulta->execute();
            //print_p($consulta->errorInfo());

            $this->conexao->commit();
            return true;

        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage(), $pdoe->getLine()));
            return false;
        }
    }

    public function deletar($id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() {
        // TODO: Implement obterTodos() method.
    }

    public function obterTodosPorUsuario($id, $emArray = true) {
        $sql = sprintf("select sa.*, s.*, u.nivel_acesso from solicitacoes_academicas sa, solicitacoes s, usuarios u where sa.id_solicitacao = s.id_solicitacao and u.id_usuario = sa.id_usuario and sa.id_usuario = :id and s.status < 7 and s.status > 0");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id, \PDO::PARAM_INT);
        $consulta->execute();

        $solicitacoes = array();
        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($emArray) {
                $eDAO = new EquipamentoDAO();
                $e = $eDAO->obter(intval($tupla['id_equipamento']));

                // TODO: Terminar adequação ao tipo de usuário solicitante.
                switch(intval($tupla['nivel_acesso'])) {
                    case 1:
                        $uDAO = new AlunoDAO();
                        break;

                    case 2:
                        $uDAO = new ProfessorDAO();
                        break;

                    case 5:
                        $uDAO = new ProfessorDAO();
                        break;

                    default:
                        $uDAO = new ProfessorDAO();
                }


                $u = $uDAO->obter(intval($tupla['id_usuario']));

                $s = array(
                    "id_solicitacao"    => intval($tupla['id_solicitacao']),
                    "id_solicitante"    => intval($tupla['id_usuario']),
                    "solicitante"       => $u->getNome(),
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
                    "higroscopico"      => boolval($tupla['higroscopico']),
                    "seguranca_outro"   => boolval($tupla['seguranca_outro']),
                    "observacoes"       => boolval($tupla['observacoes'])
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
                $s->setStatus(intval($tupla['status']));
                $s->setConfiguracao($tupla['configuracao']);
                $s->setIdentificacaoDaAmostra($tupla['identificacao_da_amostra']);
                $s->setComposicao($tupla['composicao']);
                $s->setTipo($tupla['tipo']);
                $s->setTipoOutro($tupla['tipo_outro']);
                $s->setInflamavel(boolval($tupla['inflamavel']));
                $s->setRadioativo(boolval($tupla['radioativo']));
                $s->setToxico(boolval($tupla['toxico']));
                $s->setCorrosivo(boolval($tupla['corrosivo']));
                $s->setHigroscopico(boolval($tupla['higroscopico']));
                $s->setSegurancaOutro($tupla['seguranca_outro']);
                $s->setObservacoes($tupla['observacoes']);
            }

            array_push($solicitacoes, $s);
            //Debug: array_push($solicitacoes, $consulta->queryString);
        }

        return $solicitacoes;
    }

    public function obterTodasConcluidasPorUsuario($id, $emArray = true) {
        $sql = sprintf("select sa.*, s.*, u.nivel_acesso from solicitacoes_academicas sa, solicitacoes s, usuarios u where sa.id_solicitacao = s.id_solicitacao and sa.id_usuario = u.id_usuario and sa.id_usuario
 = :id and (s.status < 0 or s.status = 7) order by data_conclusao desc");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id, \PDO::PARAM_INT);
        $consulta->execute();

        $solicitacoes = array();

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($emArray) {
                $eDAO = new EquipamentoDAO();
                $e = $eDAO->obter(intval($tupla['id_equipamento']));

                // TODO: Terminar adequação ao tipo de usuário solicitante.
                switch(intval($tupla['nivel_acesso'])) {
                    case 1:
                        $uDAO = new AlunoDAO();
                        break;

                    case 2:
                        $uDAO = new ProfessorDAO();
                        break;

                    case 5:
                        $uDAO = new ProfessorDAO();
                        break;

                    default:
                        $uDAO = new ProfessorDAO();
                }

                $u = $uDAO->obter(intval($tupla['id_usuario']));

                $s = array(
                    "id_solicitacao"    => intval($tupla['id_solicitacao']),
                    "id_solicitante"    => intval($tupla['id_usuario']),
                    "solicitante"       => $u->getNome(),
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
                    "higroscopico"      => boolval($tupla['higroscopico']),
                    "seguranca_outro"   => boolval($tupla['seguranca_outro']),
                    "observacoes"       => boolval($tupla['observacoes'])
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
                $s->setStatus(intval($tupla['status']));
                $s->setConfiguracao($tupla['configuracao']);
                $s->setIdentificacaoDaAmostra($tupla['identificacao_da_amostra']);
                $s->setComposicao($tupla['composicao']);
                $s->setTipo($tupla['tipo']);
                $s->setTipoOutro($tupla['tipo_outro']);
                $s->setInflamavel(boolval($tupla['inflamavel']));
                $s->setRadioativo(boolval($tupla['radioativo']));
                $s->setToxico(boolval($tupla['toxico']));
                $s->setCorrosivo(boolval($tupla['corrosivo']));
                $s->setHigroscopico(boolval($tupla['higroscopico']));
                $s->setSegurancaOutro($tupla['seguranca_outro']);
                $s->setObservacoes($tupla['observacoes']);
            }

            array_push($solicitacoes, $s);
        }

        return $solicitacoes;
    }

    public function obterTodasIncompletas($emArray = true) {
        $sql = sprintf("select sa.*, s.*, u.nivel_acesso from solicitacoes_academicas sa, solicitacoes s, usuarios u where sa.id_solicitacao = s
        .id_solicitacao and sa.id_usuario = u.id_usuario and s.status < 7 and s.status > 0");
        $consulta = $this->conexao->prepare($sql);
        $consulta->execute();

        $solicitacoes = array();

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($emArray) {
                $eDAO = new EquipamentoDAO();
                $e = $eDAO->obter(intval($tupla['id_equipamento']));

                // TODO: Terminar adequação ao tipo de usuário solicitante.
                switch(intval($tupla['nivel_acesso'])) {
                    case 1:
                        $uDAO = new AlunoDAO();
                        break;

                    case 2:
                        $uDAO = new ProfessorDAO();
                        break;

                    case 5:
                        $uDAO = new ProfessorDAO();
                        break;

                    default:
                        $uDAO = new ProfessorDAO();
                }
                $u = $uDAO->obter(intval($tupla['id_usuario']));

                $s = array(
                    "id_solicitacao"    => intval($tupla['id_solicitacao']),
                    "id_solicitante"    => intval($tupla['id_usuario']),
                    "solicitante"       => $u->getNome(),
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
                    "higroscopico"      => boolval($tupla['higroscopico']),
                    "seguranca_outro"   => boolval($tupla['seguranca_outro']),
                    "observacoes"       => boolval($tupla['observacoes'])
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
                $s->setStatus(intval($tupla['status']));
                $s->setConfiguracao($tupla['configuracao']);
                $s->setIdentificacaoDaAmostra($tupla['identificacao_da_amostra']);
                $s->setComposicao($tupla['composicao']);
                $s->setTipo($tupla['tipo']);
                $s->setTipoOutro($tupla['tipo_outro']);
                $s->setInflamavel(boolval($tupla['inflamavel']));
                $s->setRadioativo(boolval($tupla['radioativo']));
                $s->setToxico(boolval($tupla['toxico']));
                $s->setCorrosivo(boolval($tupla['corrosivo']));
                $s->setHigroscopico(boolval($tupla['higroscopico']));
                $s->setSegurancaOutro($tupla['seguranca_outro']);
                $s->setObservacoes($tupla['observacoes']);
            }

            array_push($solicitacoes, $s);
        }

        return $solicitacoes;
    }

    public function obterTodasConcluidas($emArray = true) {
        $sql = sprintf("select sa.*, s.*, u.nivel_acesso from solicitacoes_academicas sa, solicitacoes s, usuarios u where sa.id_solicitacao = s.id_solicitacao and sa.id_usuario = u.id_usuario and (s.status < 0 or s.status = 7)");
        $consulta = $this->conexao->prepare($sql);
        $consulta->execute();

        $solicitacoes = array();

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($emArray) {
                $eDAO = new EquipamentoDAO();
                $e = $eDAO->obter(intval($tupla['id_equipamento']));

                // TODO: Terminar adequação ao tipo de usuário solicitante.
                switch(intval($tupla['nivel_acesso'])) {
                    case 1:
                        $uDAO = new AlunoDAO();
                        break;

                    case 2:
                        $uDAO = new ProfessorDAO();
                        break;

                    case 5:
                        $uDAO = new ProfessorDAO();
                        break;

                    default:
                        $uDAO = new ProfessorDAO();
                }
                $u = $uDAO->obter(intval($tupla['id_usuario']));

                $s = array(
                    "id_solicitacao"    => intval($tupla['id_solicitacao']),
                    "id_solicitante"    => intval($tupla['id_usuario']),
                    "solicitante"       => $u->getNome(),
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
                    "higroscopico"      => boolval($tupla['higroscopico']),
                    "seguranca_outro"   => boolval($tupla['seguranca_outro']),
                    "observacoes"       => boolval($tupla['observacoes'])
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
                $s->setStatus(intval($tupla['status']));
                $s->setConfiguracao($tupla['configuracao']);
                $s->setIdentificacaoDaAmostra($tupla['identificacao_da_amostra']);
                $s->setComposicao($tupla['composicao']);
                $s->setTipo($tupla['tipo']);
                $s->setTipoOutro($tupla['tipo_outro']);
                $s->setInflamavel(boolval($tupla['inflamavel']));
                $s->setRadioativo(boolval($tupla['radioativo']));
                $s->setToxico(boolval($tupla['toxico']));
                $s->setCorrosivo(boolval($tupla['corrosivo']));
                $s->setHigroscopico(boolval($tupla['higroscopico']));
                $s->setSegurancaOutro($tupla['seguranca_outro']);
                $s->setObservacoes($tupla['observacoes']);
            }

            array_push($solicitacoes, $s);
        }

        return $solicitacoes;
    }

    public function existe($id) {
        $sql = sprintf("select * from solicitacoes_academicas where id_solicitacao = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
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