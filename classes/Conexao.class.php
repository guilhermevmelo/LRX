<?php
/**
 * arquivo: 	Conexao.class.php
 * data:		20/02/2015
 * autor:		Guilherme Vieira
 * descrição:	Define a classe Conexao.
 */
 
/**
 * 
 */
class Conexao {
    const SERVIDOR 	= "localhost";
	const USUARIO	= "";
	const SENHA		= "";
	const BD		= "";
	
	private $conexao;
	private $consulta;
	
    function __construct() {
    	try {
    		$this->conexao = mysqli_connect(Conexao::SERVIDOR, Conexao::USUARIO, Conexao::SENHA, Conexao::BD);
		} catch (Exception $e) {
			echo "Não foi possível estabelecer uma conexão com o Banco de dados.";
		} finally {
			echo "out";
		}
	}
	
	function __destruct() {
		mysqli_close($this->conexao);
	}
	
	function consultar($sql) {
		try {
			$this->consulta = mysqli_query($this->conexao, $sql);
		} catch (Exception $e) {
			echo "Ocorreu um erro ao tentar realizar a consulta \"$sql\".";
		}
	}
}
 
 
?>
