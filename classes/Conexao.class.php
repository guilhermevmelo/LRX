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
	
    function __construct($argument) {
    	try {
    		$this->conexao = mysqli_connect(Conexao::SERVIDOR, Conexao::USUARIO, Conexao::SENHA, Conexao::BD);
		} catch (exception $ex) {
			echo "Não foi possível estabelecer uma conexão com o Banco de dados.";
		}
	}
}
 
 
?>
