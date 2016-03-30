/**
 * Created by guilherme on 3/4/15.
 */

window.onhashchange = exibirSecao;
window.onload = iniciarAplicacao;

var tokens = [];
var usuario = null;
var timeoutErro = null;

/**
 * Função obitida de http://www.quirksmode.org/js/cookies.html
 * @param name
 * @param value
 * @param days
 */
function criarCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

/**
 * Função obtida de http://www.quirksmode.org/js/cookies.html
 * @param name
 * @returns {*}
 */
function lerCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

/**
 * Função obtida de http://www.quirksmode.org/js/cookies.html
 * @param name
 */
function apagarCookie(name) {
    criarCookie(name,"",-1);
}

function preencherCampo(campo, valor) {
    $('#' + campo).html(valor);
}

function atualizarTokens() {
    tokens.nome = usuario.nome;
    tokens.genero = usuario.genero;
    switch(usuario.titulo) {
        case 0:
            //tokens.titulo = usuario.genero == 1 ? "Sr. " : "Sra. ";
            tokens.titulo = "";
            break;
        case 1:
            tokens.titulo = usuario.genero == 1 ? "Profº. " : "Profª. ";
            break;
        case 2:
            tokens.titulo = usuario.genero == 1 ? "Dr. " : "Dra. ";
            break;

        default:
            tokens.titulo = usuario.genero == 1 ? "Sr. " : "Sra. ";
    }

    switch (usuario.nivel_acesso) {
        case 1:
            tokens.perfil = usuario.genero == 1 ? "aluno" : "aluna";
            tokens.Vocativo = "Você";
            tokens.vocativo = "você";

            break;
        case 2:
            tokens.perfil = usuario.genero == 1 ? "professor" : "professora";
            tokens.Vocativo = usuario.genero == 1 ? "O sr." : "A sra.";
            tokens.vocativo = usuario.genero == 1 ? "o sr." : "a sra.";
            break;
        case 3:
            tokens.perfil = "responsável por empresa";
            tokens.Vocativo = "Você";
            tokens.vocativo = "você";
            break;
        case 4:
            tokens.perfil = "agente financeiro";
            tokens.Vocativo = "Você";
            tokens.vocativo = "você";
            break;
        case 5:
            tokens.perfil = usuario.genero == 1 ? "operador" : "operadora";
            tokens.Vocativo = "Você";
            tokens.vocativo = "você";
            break;
        case 6:
            tokens.perfil = usuario.genero == 1 ? "administrador" : "administradora";
            tokens.Vocativo = usuario.genero == 1 ? "O sr." : "A sra.";
            tokens.vocativo = usuario.genero == 1 ? "o sr." : "a sra.";
            break;
    }

    tokens.num_mensagens = usuario.mensagens.length;
    tokens.possui_mensagens = (tokens.num_mensagens === 0) ? "não possui mensagens" : ("possui " + tokens.num_mensagens + " mensagens");

}

function atualizarCampos() {
    var token;
    for (token in tokens) {
        if (tokens.hasOwnProperty(token)) {
            $('#_' + token).html(tokens[token]);
        }
    }
}

function apresentarErro(erro) {
    clearTimeout(timeoutErro);
    $('#DivErro').fadeOut('slow', function () {
        $('#ErroP').html(erro.mensagem);
        $(this).fadeIn('slow');

        timeoutErro = setTimeout(function () {
            $('#DivErro').fadeOut('slow')
        }, 6 * 1000);
    })
}

function exibirSecao() {
    var hash = window.location.hash;
    var estadoAtual = $('.estadoAtual');
    console.log("aqui com hash ", hash);

    switch (hash) {
        case '#/Inicio':
            if (usuario !== null) {
                location.hash = '#/Dashboard';
                break;
            }
            estadoAtual.fadeOut('slow', function () {
                $(this).removeClass('estadoAtual');
                $('#Inicio').fadeIn('slow', function () {
                    $(this).addClass('estadoAtual');
                });
            });
            break;

        case '#/Dashboard':
            estadoAtual.fadeOut('slow', function () {
                $(this).removeClass('estadoAtual');
                $('#Dashboard').fadeIn('slow', function () {
                    $(this).addClass('estadoAtual');
                });
            });
            break;

        case '#/Sair':
            apagarCookie('uid');
            usuario = null;
            $('header').effect('drop', {direction: 'up'});
            location.hash = '#/Inicio';
            break;

        default:
            location.hash = '#/Inicio';
    }
}

function iniciarAplicacao() {
    var _uid = lerCookie('uid');
    if (_uid !== null) {
        $.ajax({
            url: 'acao.php',
            type: 'post',
            data: {
                q: 'loginDireto',
                uid: _uid,
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                usuario = [];
                usuario.id = r.id_usuario;
                usuario.documento = r.documento;
                usuario.nome = r.nome;
                usuario.email = r.email;
                usuario.email_alternativo = r.email_alternativo;
                usuario.titulo = r.titulo;
                usuario.genero = r.genero;
                usuario.telefone = r.telefone;
                usuario.nivel_acesso = r.nivel_acesso;
                usuario.confirmado = r.confirmado;
                usuario.email_confirmado = r.email_confirmado;
                usuario.uid = r.uid;
                usuario.mensagens = r.mensagens;
                usuario.estado = r.estado;
                usuario.cidade = r.cidade;

                atualizarTokens();
                atualizarCampos();

                $('header').toggle('drop', {direction:"up"});
                location.hash = '#/Dashboard';
            }
        });

    }
    exibirSecao();
}

$(document).ready(function () {
    atualizarCampos();

    /**
     * Adiciona um gatilho para dispensar a mensagem de erro ao clique.
     */
    $('#Erro').click(function () {
        clearTimeout(timeoutErro);
        $(this).fadeOut('slow');
    });

    /**
     * Adiciona um gatilho para enviar via AJAX a requisição de login e processar o resultado.
     */
    $('#FormLogin').submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        //$('#frm_login_sbmt').setAttribute('disabled', 'disabled');

        clearTimeout(timeoutErro);
        $('#DivErro').fadeOut('slow');

        var shaObj = new jsSHA("SHA-1", "TEXT");
        shaObj.update($('#frm_login_senha').val());
        var _senha = shaObj.getHash("HEX");
        var _email = $('#frm_login_email').val();
        var _permanecer = $('#frm_login_manter_logado').is(':checked');

        $.ajax({
            url: 'acao.php',
            type: 'post',
            data: {
                q: 'login',
                email: _email,
                senha: _senha
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                usuario = [];
                usuario.id = r.id_usuario;
                usuario.documento = r.documento;
                usuario.nome = r.nome;
                usuario.email = r.email;
                usuario.email_alternativo = r.email_alternativo;
                usuario.titulo = r.titulo;
                usuario.genero = r.genero;
                usuario.telefone = r.telefone;
                usuario.nivel_acesso = r.nivel_acesso;
                usuario.confirmado = r.confirmado;
                usuario.email_confirmado = r.email_confirmado;
                usuario.uid = r.uid;
                usuario.mensagens = r.mensagens;
                usuario.estado = r.estado;
                usuario.cidade = r.cidade;

                atualizarTokens();
                atualizarCampos();

                if (_permanecer === true) {
                    criarCookie('uid', usuario.uid, 3/24);
                }

                $('#frm_login_email').val('');
                $('#frm_login_senha').val('');

                location.hash = '#/Dashboard';
                $('#Inicio').fadeOut('slow');
                $('header').toggle('drop', {direction:"up"});
            }
        });
    });


});