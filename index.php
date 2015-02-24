<?php
 require_once("classes/Conexao.class.php");
 
 $con = new Conexao();
 
 $sql = "select * from users";
 $con->consultar($sql);
 

 
 ?>