<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:51
 */

namespace LRX;

require "autoload.php";

/**
 * Obtém apenas as iniciais de $nome e as concatena em maiúsculo.
 *
 * @param $nome string O nome a ter suas iniciais extraidas.
 * @return string   As iniciais concatenadas em maiúsculo.
 */
 function obterIniciais($nome) {
    $pedacos = explode(' ', $nome);
    $iniciais = '';

    foreach ($pedacos as $pedaco) {
        if(strlen($pedaco) > 0)
            $iniciais .= $pedaco[0];
    }

    return strtoupper($iniciais);
}

/**
 * Lança um objeto LRX|Erro com mensagens e códigos entendíveis pelo usuário.
 *
 * @param \Exception $ex    A Exceção cujo erro deve ser retornado.
 */
function tratarErro($ex) {
    // TODO Tratar cada exceção individualmente mara fornecer mensagens amigáveis ao usuário
    Erro::lancarErro(array(
        'codigo'    =>  $ex->getCode(),
        'mensagem'  =>  $ex->getMessage()
    ));
}

/**
 * Recebe um número de CPF no formato 000.000.000-00 e o converte para uma string de 11 dígitos
 *
 * @param $cpf string   Um número de CPF no formato 000.000.000-00
 * @return string       O mesmo cpf sem os pontos e o traço 00000000000
 */
function desformatarCPF($cpf) : string {
    // TODO: Verificar com expressão regular se o argumento está no formato

    //Separa o traço
    $arr = explode('-', $cpf);
    //Separa os números principais
    $num = explode('.', $arr[0]);
    //Adiciona os dígitos verificadores ao array de números
    array_push($num, $arr[1]);
    //Combina todos os elementos do array de números em uma string
    $numeros = implode("", $num);

    return $numeros;
}

/**
 * @param $expression
 */
function print_p($expression) {
//    echo "<pre>";
//    var_dump($expression);
//    echo "</pre>";
}

/**
 * Adiciona a função tratarExpection como o manipulador padrão de exceções
 */
// TODO: Habilitar a linha abaixo
set_exception_handler('LRX\tratarErro');

/**
 * Define o fuso-horário padrão do servidor para UTC-3 (America/Frtaleza)
 */
date_default_timezone_set('America/Fortaleza');