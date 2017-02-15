<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/9/16
 * Time: 17:47
 */

namespace LRX\Usuarios;
use LRX\Erro;
use LRX\Solicitacoes\SolicitacaoAcademicaDAO;
use function LRX\print_p;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;

/**
 * Class ProfessorDAO
 * @package LRX
 */
class ProfessorDAO /*extends DAO*/ {
    /**
     * @var \PDO
     */
    private $conexao;
    /**
     * @var UsuarioAcademicoDAO
     */
    private $uaDAO;

    /**
     * ProfessorDAO constructor.
     */
    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
        $this->uaDAO = new UsuarioAcademicoDAO();
    }


    /**
     * @param Professor $professor
     * @param bool $novo_usuario
     * @return bool
     */
    public function criar(Professor $professor, bool $novo_usuario = true) {
        if ($this->existeDocumento($professor->getDocumento()))
            return false;

        try {
            if ($novo_usuario)
                $this->uaDAO->criar($professor);

            $this->conexao->beginTransaction();

            $sql = sprintf("insert into professores values (:id_professor, :id_grupo, 0)");
            $consulta = $this->conexao->prepare($sql);
            
            $consulta->bindValue(':id_professor', $professor->getId());
            $valor_id_grupo = $professor->getGrupo() != null ? $professor->getGrupo()->getId() : null;
            $consulta->bindValue(':id_grupo', $valor_id_grupo);

            $consulta->execute();

            //print_p($consulta->errorInfo());

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
     * @param $documento
     * @return bool
     */
    public function existeDocumento($documento) {
        $sql = sprintf("select * from usuarios u, professores p where u.cpf = :documento and p.id_professor = u.id_usuario");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }


    /**
     * @param int $id
     * @param bool $em_array
     * @return Professor|bool
     */
    public function obter(int $id, $em_array = false) : Professor {
        $sql = sprintf("select u.*, p.id_grupo, p.habilitado from usuarios u, professores p where u.id_usuario = :id_usuario and p.id_professor = u.id_usuario limit 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $id);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        if ($em_array) {
            $p = array();
            // TODO: Terminar essa implementação
        } else {
            $p = new Professor($tupla['nome'], $tupla['email'], $tupla['cpf'], (int) $tupla['id_usuario'], $tupla['uid'],
                (int)$tupla['limite']);
            $p->setConfirmado(intval($tupla['confirmado']) == 1 ? true : false);
            $p->setEmailConfirmado(intval($tupla['email_confirmado']) == 1 ? true : false);
            $p->setCidade($tupla['cidade']);
            $p->setEstado($tupla['estado']);
            $p->setSenha($tupla['senha']);
            $p->setAreaDePesquisa($tupla['area_de_pesquisa']);
            $p->setDepartamento($tupla['departamento']);
            $p->setLaboratorio($tupla['laboratorio']);
            $p->setEmailAlternativo($tupla['email_alternativo']);
            $p->setNivelAcesso((int) $tupla['nivel_acesso']);
            $p->setGenero((int) $tupla['genero']);
            $p->setTelefone($tupla['telefone']);
            $p->setTitulo((int) $tupla['titulo']);
            $p->setIes($tupla['ies']);
            $p->setSaudacao((int) $tupla['saudacao']);
            $p->setHabilitado(intval($tupla['habilitado']) == 1 ? true : false);
        }

        return $p;
    }

    /**
     * @param string $cpf
     * @param bool $em_array
     * @return Professor|bool
     */
    public function obterPorDocumento(string $cpf, $em_array = false) : Professor {
        $sql = sprintf("select u.*, p.id_grupo, p.habilitado from usuarios u, professores p where u.cpf = :cpf and p.id_professor = u.id_usuario limit 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':cpf', $cpf);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        if ($em_array) {
            $p = array();
            // TODO: Terminar implementação.
        } else {
            $p = new Professor($tupla['nome'], $tupla['email'], $tupla['cpf'], (int) $tupla['id_usuario'], $tupla['uid'],
                (int)$tupla['limite']);
            $p->setConfirmado(intval($tupla['confirmado']) == 1 ? true : false);
            $p->setEmailConfirmado(intval($tupla['email_confirmado']) == 1 ? true : false);
            $p->setCidade($tupla['cidade']);
            $p->setEstado($tupla['estado']);
            $p->setSenha($tupla['senha']);
            $p->setAreaDePesquisa($tupla['area_de_pesquisa']);
            $p->setDepartamento($tupla['departamento']);
            $p->setLaboratorio($tupla['laboratorio']);
            $p->setEmailAlternativo($tupla['email_alternativo']);
            $p->setNivelAcesso((int) $tupla['nivel_acesso']);
            $p->setGenero((int) $tupla['genero']);
            $p->setTelefone($tupla['telefone']);
            $p->setTitulo((int) $tupla['titulo']);
            $p->setIes($tupla['ies']);
            $p->setSaudacao((int) $tupla['saudacao']);
	        $p->setHabilitado(intval($tupla['habilitado']) == 1 ? true : false);
        }

        return $p;
    }

    /**
     * @param Professor $professor
     * @return bool
     */
    public function atualizar(Professor $professor) {
        $professor_antigo = $this->obter($professor->getId());
        if ($professor_antigo === false)
            return false;

        try {
            $this->uaDAO->atualizar($professor);

            $this->conexao->beginTransaction();

            $sql = sprintf("update professores set id_grupo = :id_grupo, habilitado = :habilitado where id_professor = :id_professor");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_professor', $professor->getId(), \PDO::PARAM_INT);
            $valor_id_grupo = $professor->getGrupo() != null ? $professor->getGrupo()->getId() : null;
            $consulta->bindValue(':id_grupo', $valor_id_grupo, \PDO::PARAM_INT);
            $consulta->bindValue(':habilitado', $professor->estaHabilitado(), \PDO::PARAM_BOOL);

            $consulta->execute();

            print_p($consulta->errorInfo());

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
     * @param int $id
     * @param bool $deletar_usuario
     */
    public function deletar(int $id, bool $deletar_usuario = false) {
        // TODO: Verificar se o professor criou algum grupo.
        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("delete from professores where id_professor = :id_professor limit 1");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue("id_professor", $id);
            $consulta->execute();

            if ($deletar_usuario) {
                $sql2 = sprintf("delete from usuarios where id_usuario = :id_professor limit 1");
                $consulta = $this->conexao->prepare($sql2);
                $consulta->bindValue("id_professor", $id);
                $consulta->execute();
            }

            $this->conexao->commit();
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
        }
    }

    /**
     * @return array
     */
    public function obterTodos($em_array = false, $apenas_nao_confirmados = false) : array {
        $sql = $apenas_nao_confirmados ?
            sprintf("select u.*, p.id_grupo, p.habilitado from usuarios u, professores p where p.id_professor = u.id_usuario and confirmado = 0 order by id_usuario desc") :
            sprintf("select u.*, p.id_grupo, p.habilitado from usuarios u, professores p where p.id_professor = u.id_usuario order by id_usuario desc");
        $professores = array();
        $saDAO = new SolicitacaoAcademicaDAO();
        foreach ($this->conexao->query($sql) as $tupla) {
            if ($em_array) {
                $p = array();
                $p["id_usuario"] = intval($tupla["id_usuario"]);
                $p["nome"] = $tupla["nome"];
                $p["email"] = $tupla["email"];
                $p["cpf"] = $tupla["cpf"];
                $p["uid"] = $tupla["uid"];
                $p["confirmado"] = $tupla["confirmado"] == 1 ? true : false;
                $p["cidade"] = $tupla["cidade"];
                $p["estado"] = $tupla["estado"];
                $p["limite"] = intval($tupla["limite"]);
                $p["area_de_pesquisa"] = $tupla["area_de_pesquisa"];
                $p["departamento"] = $tupla["departamento"];
                $p["ies"] = $tupla["ies"];
                $p["laboratorio"] = $tupla["laboratorio"];
                $p["genero"] = intval($tupla["genero"]);
                $p["telefone"] = $tupla["telefone"];
                $p["nivel_acesso"] = intval($tupla["nivel_acesso"]);
                $p["em_andamento"] = $saDAO->obterNumeroSolicitacoesEmAndamento(intval($tupla["id_usuario"]))["aprovadas"];
	            $p["habilitado"] = intval($tupla['habilitado']) == 1 ? true : false;
            } else {
                $p = new Professor($tupla['nome'], $tupla['email'], $tupla['cpf'], (int) $tupla['id_usuario'], $tupla['uid'],
                    (int)$tupla['limite']);
                $p->setConfirmado($tupla['confirmado'] == 1 ? true : false);
                $p->setCidade($tupla['cidade']);
                $p->setEstado($tupla['estado']);
                $p->setSenha($tupla['senha']);
                $p->setAreaDePesquisa($tupla['area_de_pesquisa']);
                $p->setDepartamento($tupla['departamento']);
                $p->setLaboratorio($tupla['laboratorio']);
                $p->setEmailAlternativo($tupla['email_alternativo']);
                $p->setNivelAcesso((int) $tupla['nivel_acesso']);
                $p->setGenero((int) $tupla['genero']);
                $p->setTelefone($tupla['telefone']);
                $p->setTitulo((int) $tupla['titulo']);
                $p->setIes($tupla['ies']);
                $p->setSaudacao((int) $tupla['saudacao']);
	            $p->setHabilitado(intval($tupla['habilitado']) == 1 ? true : false);
            }

            array_push($professores, $p);
        }

        return $professores;
    }
}