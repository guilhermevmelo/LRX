<?php
ini_set("auto_detect_line_endings", true);
header("Content-Encoding: CP-1250");
require_once "../classes/LRX/autoload.php";

use \LRX\Correio\MalaDireta;

?>
<!doctype html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>LRX - Mala direta</title>
    </head>
    <body>
<?php

$arquivos = array("");

foreach ($arquivos as $arq) {
    $arquivo = fopen($arq, 'r');
    echo "<h3>Abrindo arquivo " . $arq . "</h3>";
    if (!$arquivo) {
        echo "Erro ao abrir o arquivo";
        continue;
    }

    $array = array();
    while (($linha = fgetcsv($arquivo, 10000, ";")) !== false) {
        $empresa = $linha[2] != "" ? $linha[2] . "<br>" : "";
        $logradouro = $linha[3];
        $complemento = $linha[4] == "" ? "" : ", " . $linha[4];
        $bairro = $linha[5];
        $cep = $linha[6];
        $cidade = $linha[7];
        $email = strtolower($linha[10]);
        $email = str_replace(array(";", '"', "'"), array(",", "", ""), $email);
        $responsavel = $linha[12];

        $endereco = $empresa . $logradouro . $complemento . "<br>" . $bairro . ", " . $cidade . "<br>" . $cep;

        $string = array("email" => $email,
                        "campos" => array(
                            array("chave" => "responsavel", "valor" => $responsavel),
                            array("chave" => "empresa", "valor" => $empresa),
                            array("chave" => "endereco", "valor" => $endereco),
                        )
        );

        if ($email != '')
            array_push($array, $string);
    }
    fclose($arquivo);

    $assunto = "Laboratório de Raios-X da UFC";
    $mensagem = "
        <p style=\"text-align: left; max-width: 600px;\">Ilmo(a). Sr(a). <strong>{responsavel}</strong><br>{endereco}</p>
        <p style=\"text-align: left; max-width: 600px;\">Prezado(a) Sr(a).</p>
        <p style=\"text-align:justify; max-width: 600px;\">Levo ao conhecimento de V.Sa. que somos pesquisadores do Laboratório de Raios-X da Universidade Federal do Ceará com experiência de 10 anos  em identificação e quantificação de fases minerais,  obtenção de tamanho de partículas e microdeformação e quantificação de elementos químicos constituintes de rocha, minerais e minérios. Este laboratório conta com um difratômetro e um espectrômetro de raios-X de última geração.</p>
        <p style=\"text-align:justify; max-width: 600px;\">Cabe-nos informar-lhes que já fizemos análises para diversas empresas tais como: Companhia Vale do Rio Doce, Companhia Siderúrgica do Pecém, Embrapa, Vicunha, Carbomil, Oxinor, Okyta Mineração, UNIFOR, Termoceará, NUTEC, Cagece, Dias Branco, Petroreconcavo, FAE, INACE, Tintas Hidracor, COGERH, Central Mineral NE, Ampla e outros.</p>
        <p style=\"text-align:justify; max-width: 600px;\">O nosso objetivo é fazer chegar ao conhecimento de V.Sa. que de alguma forma este laboratório pode melhorar o desempenho de sua empresa ou indústria e para isto oferecemos nossos conhecimentos.</p>
        <p style=\"text-align:justify; max-width: 600px;\">Certos de que teremos despertado vossa atenção no sentido de uma boa e firme resposta a possíveis problemas que podemos resolver é que ficamos aguardando a sua comunicação. Para maiores esclarecimentos sobre valores que são cobrados, acesse o seguinte endereço: <a href=\"http://www.raiosx.ufc.br/site/?page_id=285\" target=\"_blank\">http://www.raiosx.ufc.br/site/?page_id=285</a>.</p>
        <p style=\"text-align:justify; max-width: 600px;\">Sem mais para o momento, e em caso dúvidas estaremos à disposição para maiores esclarecimentos.</p>
        <p style=\"text-align: right; max-width: 600px;\">Atenciosamente,</p>
        <p style=\"text-align: right; max-width: 600px;\">Prof. Dr. José Marcos Sasaki<br>Coordenador do Laboratório</p>
    ";
    $md = new MalaDireta(array("assunto" => $assunto, "mensagem" => $mensagem), $array);
    $md->enviar(true);
}
?>
    </body>
    </html>










