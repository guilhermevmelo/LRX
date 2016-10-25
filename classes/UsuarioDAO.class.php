<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/16/16
 * Time: 00:21
 */

namespace LRX;


class UsuarioDAO {

    public function criar($objeto) {
        // TODO: Implement criar() method.
    }

    public function obter($id) {
        $conexao = new \PDO(DSN, USUARIO, SENHA);
        $sql = sprintf("SELECT id_usuario, nivel_acesso FROM usuarios WHERE id_usuario = :id LIMIT 1");

        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':id', $id);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return null;

        switch ((int)$tupla['nivel_acesso']) {
            case 1:
                $aDAO = new AlunoDAO();
                return $aDAO->obter((int)$tupla['id_usuario']);
                break;

            case 2:
                $pDAO = new ProfessorDAO();
                return $pDAO->obter((int)$tupla['id_usuario']);
                break;

            case 3:
                // TODO: Implementar Responsável por empresa
                break;

            case 4:
                // TODO: Implementar Financeiro
                break;

            case 5:
                $aDAO = new AlunoDAO();
                $u = $aDAO->obter((int)$tupla['id_usuario']);
                if ($u !== false)
                    return $u;

                $pDAO = new ProfessorDAO();
                $u = $pDAO->obter((int)$tupla['id_usuario']);
                return $u;
                break;

            case 6:
                //TODO: Implementar Administrador
                break;

            default:
                return null;
        }

        return null;
    }

    public static function obterPorUid($uid) {
        $conexao = new \PDO(DSN, USUARIO, SENHA);
        $sql = sprintf("SELECT id_usuario, nivel_acesso FROM usuarios WHERE uid = :uid LIMIT 1");

        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':uid', $uid);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return null;

        switch ((int)$tupla['nivel_acesso']) {
            case 1:
                $aDAO = new AlunoDAO();
                return $aDAO->obter((int)$tupla['id_usuario']);
                break;

            case 2:
                $pDAO = new ProfessorDAO();
                return $pDAO->obter((int)$tupla['id_usuario']);
                break;

            case 3:
                // TODO: Implementar Responsável por empresa
                break;

            case 4:
                // TODO: Implementar Financeiro
                break;

            case 5:
                $aDAO = new AlunoDAO();
                $u = $aDAO->obter((int)$tupla['id_usuario']);
                if ($u !== false)
                    return $u;

                $pDAO = new ProfessorDAO();
                $u = $pDAO->obter((int)$tupla['id_usuario']);
                return $u;
                break;

            case 6:
                //TODO: Implementar Administrador
                break;

            default:
                return null;
        }

        return null;
    }

    public function atualizar($objeto) {
        // TODO: Implement atualizar() method.
    }

    public function deletar($id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() {
        // TODO: Implement obterTodos() method.
    }

    public static function existeDocumento($documento) : bool {
        $conexao = new \PDO(DSN, USUARIO, SENHA);

        $sql = sprintf("select * from usuarios where cpf = :documento");
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public static function existeEmail($email) : bool {
        $conexao = new \PDO(DSN, USUARIO, SENHA);

        $sql = sprintf("select * from usuarios where email = :email");
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':email', $email);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public static function login($email, $senha) {
        $conexao = new \PDO(DSN, USUARIO, SENHA);
        $sql = sprintf("select id_usuario, nivel_acesso from usuarios where email = :email and senha = :senha limit 1");

        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':email', $email);
        $consulta->bindValue(':senha', $senha);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return null;

        switch ((int)$tupla['nivel_acesso']) {
            case 1:
                $aDAO = new AlunoDAO();
                return $aDAO->obter((int)$tupla['id_usuario']);
                break;

            case 2:
                $pDAO = new ProfessorDAO();
                return $pDAO->obter((int)$tupla['id_usuario']);
                break;

            case 3:
                // TODO: Implementar Responsável por empresa
                break;

            case 4:
                // TODO: Implementar Financeiro
                break;

            case 5:
                $aDAO = new AlunoDAO();
                $u = $aDAO->obter((int)$tupla['id_usuario']);
                if ($u !== false)
                    return $u;

                $pDAO = new ProfessorDAO();
                $u = $pDAO->obter((int)$tupla['id_usuario']);
                return $u;
                break;

            case 6:
                $pDAO = new ProfessorDAO();
                $u = $pDAO->obter((int)$tupla['id_usuario']);
                return $u;
                break;

            default:
                return null;
        }

       return null;
    }
}