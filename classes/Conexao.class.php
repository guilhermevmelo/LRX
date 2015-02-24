<?php
/**
 * arquivo: 	Conexao.class.php
 * data:		20/02/2015
 * autor:		Guilherme Vieira
 * descrição:	Define a classe Conexao.
 */

/**
 * Class Conexao
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
			echo "Não foi possível estabelecer uma conexão com o Banco de Dados.";
		}
	}

	function __destruct() {
		mysqli_close($this->conexao);
	}
	
	public function consultar($sql) {
		try {
			$this->consulta = mysqli_query($this->conexao, $sql);
		} catch (Exception $e) {
			echo "Ocorreu um erro ao tentar realizar a consulta \"$sql\".";
		}
	}

    /**
     * num_rows()
     * @return int
     */
    public function numero_de_tuplas() {
        return mysqli_num_rows($this->consulta);
    }


    /**
     * fetch_array()
     * @return array|null
     */
    public function lista_de_tuplas() {
        return mysqli_fetch_array($this->consulta);
    }
}
 
 
?>
