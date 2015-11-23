<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:51
 */

namespace LRX;

class Util {
    /**
     * Retorna apenas as iniciais de $nome concatenadas em maiúsculo.
     *
     * @param $nome     O nome a ter suas iniciais extraidas.
     * @return string   As iniciais concatenadas em maiúsculo.
     */
    public static function obterIniciais($nome) {
        $pedacos = explode(' ', $nome);
        $iniciais = '';

        foreach ($pedacos as $pedaco) {
            if(strlen($pedaco) > 0)
                $iniciais .= $pedaco[0];
        }

        return strtoupper($iniciais);
    }
}