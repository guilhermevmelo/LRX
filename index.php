<!doctype html>
<!--
  - - arquivo:   index.php
  - - data:      25/02/2015
  - - autor:     Guilherme Vieira
  - - descrição: Página inicial da aplicação.
  -->
<html>
<head>
    <meta charset="utf-8">
    <title>Laboratório de Raios-X | Solicitação de Análises</title>
    <link rel="stylesheet" href="estilos/all.css" media="all">
    <script src="js/base.js"></script>
    <script src="js/retina.js"></script>
</head>
<body>
<div id="Pagina">
    <header role="presentation">
        <p id="BoasVindas">
            <span id="_cumprimento">
                Olá <span id="_titulo">Dr.</span> <span id="_nome">José Marcos Sasaki</span>
            </span>
            <br>
            <span id="_segunda_linha">
                <span id="_Vocativo">O sr.</span> <span id="_num_amostras">não possui</span> amostras em processo de análise
            </span>
        </p>
        <nav>
            <ul>
                <li><a href="#">Lorem Ipsum</a></li>
                <li><a href="#">Dolor</a></li>
                <li><a href="#">Sit Amet</a></li>
                <li><a href="#">Consectectur</a></li>
                <li><a href="#">Adiscpiscing</a></li>
                <li><a href="#">Ellit</a></li>
            </ul>
        </nav>
    </header>
    <main role="main">
        <div id="Inicio">
            <div id="DivLogin" class="bloco">
                <div class="container">
                    <form id="FormLogin" name="FormLogin" action="#/Login" method="post">
                        <fieldset>
                            <label for="frm_login_documento">CPF</label>
                            <input type="text" name="frm_login_documento" id="frm_login_documento" maxlength="14">
                        </fieldset>
                        <fieldset>
                            <label for="frm_login_senha">Senha</label>
                            <input type="password" name="frm_login_senha" id="frm_login_senha">
                        </fieldset>
                        <div>
                            <fieldset class="floatLeft">
                                <input type="radio" name="frm_login_tipo" id="frm_login_tipo_academico" checked>
                                <label for="frm_login_tipo_academico">Acadêmico</label>
                                <br>
                                <input type="radio" name="frm_login_tipo" id="frm_login_tipo_comercial">
                                <label for="frm_login_tipo_comercial">Comercial</label>
                            </fieldset>
                            <fieldset class="floatRight" style="padding-top: 4px;">
                                <input type="button" name="frm_login_sbmt" id="frm_login_sbmt" value="Entrar">
                            </fieldset>
                        </div>
                        <p class="clear">
                            <a href="#/RecuperarConta">Esqueci a senha</a><br>
                            <a href="#/NovoUsuario">Cadastre-se</a>
                        </p>
                    </form>
                </div>
            </div>
            <div id="Apresentacao" class="bloco">
                <h1>Sistema de Solicitação de Análises de Raios-X</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec dui a elit consectetur tincidunt vel rutrum enim.
                    Sed in fermentum eros, in bibendum sem. In consectetur, nunc et dapibus hendrerit, nulla nisl suscipit lorem, vel tincidunt turpis ante ac dolor.
                    Integer tincidunt, dolor eget rhoncus rutrum, ipsum nulla molestie nunc, ac tincidunt ante odio sit amet metus.
                    Nunc luctus, orci vitae hendrerit imperdiet, quam felis pellentesque turpis, eget consectetur ex orci eget arcu.
                    Phasellus fringilla vel metus sed ullamcorper. Sed vehicula ultrices ipsum id suscipit.
                    Nunc maximus, mauris sed sodales placerat, erat magna euismod leo, sed porta nibh tortor sit amet orci.
                    Suspendisse in sollicitudin lorem. Ut pretium non dolor ac tristique. Integer venenatis nulla non tortor convallis tempus non vestibulum nibh.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec dui a elit consectetur tincidunt vel rutrum enim.
                    Sed in fermentum eros, in bibendum sem. In consectetur, nunc et dapibus hendrerit, nulla nisl suscipit lorem, vel tincidunt turpis ante ac dolor.
                    Integer tincidunt, dolor eget rhoncus rutrum, ipsum nulla molestie nunc, ac tincidunt ante odio sit amet metus.
                    Nunc luctus, orci vitae hendrerit imperdiet, quam felis pellentesque turpis, eget consectetur ex orci eget arcu.
                    Phasellus fringilla vel metus sed ullamcorper. Sed vehicula ultrices ipsum id suscipit.
                    Nunc maximus, mauris sed sodales placerat, erat magna euismod leo, sed porta nibh tortor sit amet orci.
                    Suspendisse in sollicitudin lorem. Ut pretium non dolor ac tristique. Integer venenatis nulla non tortor convallis tempus non vestibulum nibh.</p>

            </div>
        </div>
    </main>
    <footer role="contentinfo">
        <p id="Fomentadores">
            <img src="imagens/logo_lrx_pequena.png" alt="Laboratório de Raios X" class="floatLeft">
            <img src="imagens/logo_ufc_vertical_pequena.png" alt="Laboratório de Raios X" class="floatLeft">
            <img src="imagens/logo_cnpq_pequena.png" alt="Laboratório de Raios X">
        </p>
        <address>
            Universidade Federal do Ceará - UFC<br>
            Departamento de Física<br>
            Bloco 928, sala 34<br>
            Caixa Postal 6030<br>
            Campus do Pici<br>
            CEP 60455-970, Fortaleza - Ceara - Brazil<br>
            Telefone: 55 (0xx85) 3366 9013 (sala 34)<br>
            Telefone: 55 (0xx85) 3366 9917 (lab.)<br>
            FAX: 55 (0xx85) 3366 9450
        </address>
        <nav id="MenuRodape">
            <ul>
                <li><a href="#">Conheça os equipamentos</a></li>
            </ul>
        </nav>
        <p class="clear alignCenter">Copyright &copy; 2015 Laboratório de Raios X</p>
    </footer>
</div>
</body>
</html>