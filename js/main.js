/**
 * Created by guilherme on 3/4/15.
 */

var tokens = [];
var usuario = null;
var tipo_sistema = null;
var timeoutErro = null;
var timeoutCronometroErro = null;

window.onload = iniciarAplicacao;
window.onhashchange = exibirSecao;

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

function reiniciarContadorErro(tempo) {
    if (tempo < 0) return;

    $('#ErroCronometro').html(tempo);

    timeoutCronometroErro = setTimeout(function () {
        reiniciarContadorErro(tempo - 1);
    }, 1000);
}

function apresentarErro(erro) {
    clearTimeout(timeoutErro);
    clearTimeout(timeoutCronometroErro);
    $('#DivErro').fadeOut('slow', function () {
        $('#ErroP').html(erro.mensagem);
        reiniciarContadorErro(6);
        $(this).fadeIn('slow');

        timeoutErro = setTimeout(function () {
            $('#DivErro').fadeOut('slow')
        }, 6 * 1000);
    })
}

function dataPhpParaJs(data) {
    var d = new Date(data);
    return d.toDateString();
}

function obterDetalhesSolicitacao(id) {

    $('#Detalhe').fadeOut('slow', function () {
        $.ajax({
            url: 'acao.php',
            type: 'get',
            data: {
                q: 'obterDetalhesSolicitacao',
                id_solicitacao: id,
                tipoSistema: tipo_sistema
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                $('#detalhe_Identificacao').html(r.solicitacao.identificacao);
                //$('#detalhe_Status').html();
                $('#detalhe_DataSolicitacao').html(dataPhpParaJs(r.solicitacao.data_solicitacao));
                if (r.solicitacao.data_entrega === null) {
                    $('#detalhe_DataRecebimento').html("A Amostra ainda não foi entregue.");
                } else {
                    $('#detalhe_DataRecebimento').html(dataPhpParaJs(r.solicitacao.data_recebiento));
                }
                $('#detalhe_Tipo').html(r.solicitacao.tipo_equipamento);
                $('#detalhe_Equipamento').html(r.solicitacao.equipamento);

                $('#detalhe_Cancelar').click(function() {
                    //TODO adicionar confirmacao
                    $(this).off('click');
                    $('#Detalhe').removeClass('ativo').fadeOut('slow');
                    $('#_sol' + r.solicitacao.id_solicitacao).effect('blind');

                    $.ajax({
                        url: 'acao.php',
                        type: 'get',
                        data: {
                            q: 'cancelarSolicitacao',
                            id: r.solicitacao.id_solicitacao,
                            uid: usuario.uid,
                            nivel_acesso: usuario.nivel_acesso
                        }
                    }).done(function (re) {
                        if (re.codigo !== 200)
                            apresentarErro(re);
                    });
                });

                $('#detalhe_Configuracao').html(r.solicitacao.configuracao);

                $('#Detalhe').fadeIn('slow').addClass('ativo');

            }
        }); 
    });


}

// TODO fundir as duas funcoes e uma só

function preencherSolicitacoes() {
    $(".listaSolicitacoes").empty();
    $.ajax({
        url: 'acao.php',
        type: 'get',
        data: {
            q: 'obterListaSolicitacoes',
            id: usuario.id,
            nivel_acesso: usuario.nivel_acesso,
            tipoSistema: tipo_sistema
        }
    }).done(function (r) {
        if (r.codigo !== 200) {
            apresentarErro(r);
        } else {
            var  n = 0;
            r.solicitacoes.forEach(function(_s) {

                // var elementoLi = "<li class=\"bloco relativo escondido\"><span class=\"solicitacaoMenuFlutuante floatRight\"><a" +
                //     " href=\"#/Dashboard/E/"+_s.id_solicitacao+"\">excluir</a> </span><h3" +
                // " class=\"solicitacaoIdentificacao\">"+_s.identificacao+"</h3>";
                //
                // elementoLi += "<h4 class=\"vermelho\">Aguardando autorização do professor</h4>";
                //
                //
                // elementoLi += "<p>Criada em <span class=\"solicitacaoDataSolicitacao\">"+_s.data_solicitacao+"</span><br>";
                // elementoLi += "<span class=\"solicitacaoDataSolicitacao\">Entregue em 15 de Abril</span>";
                // elementoLi += "</p></li>";

                var elementoLi = document.createElement('li');
                elementoLi.classList.add('bloco');
                elementoLi.classList.add('relativo');
                elementoLi.classList.add('escondido');

                elementoLi.id = "_sol" + _s.id_solicitacao;

                var identificacaoH3 = document.createElement('h3');
                identificacaoH3.classList.add('solicitacaoIdentificacao');
                identificacaoH3.innerHTML = _s.identificacao;
                elementoLi.appendChild(identificacaoH3);

                var statusH4 = document.createElement('h4');
                switch (_s.status) {
                    case 1:
                        statusH4.classList.add('cinza');
                        statusH4.innerHTML = 'Aguardando autorização do professor.';
                        break;

                    case 2:
                        statusH4.classList.add('cinza');
                        statusH4.innerHTML = 'Aguardando aprovação do laboratório.';
                        break;

                    case 3:
                        statusH4.classList.add('amarela');
                        statusH4.innerHTML = 'Aguardando confirmação de entrega da amostra';
                        break;

                    case 4:
                        statusH4.classList.add('amarela');
                        statusH4.innerHTML = 'Na fila do equipamento';
                        break;

                    case 5:
                        statusH4.classList.add('amarela');
                        statusH4.innerHTML = 'Em processo de análise.';
                        break;

                    case 6:
                        statusH4.classList.add('azul');
                        statusH4.innerHTML = 'Análise Concluída. Aguardando recolhimento da amostra.';
                        break;

                    case 7:
                        statusH4.classList.add('verde');
                        statusH4.innerHTML = 'Solicitação Finalizada.';
                        break;

                    case -1:
                        statusH4.classList.add('vermelha');
                        statusH4.innerHTML = 'Cancelada pelo responsável.';
                        break;

                    case -2:
                        statusH4.classList.add('vermelha');
                        statusH4.innerHTML = 'Cancelada pelo operador.';
                        break;

                    case -3:
                        statusH4.classList.add('vermelha');
                        statusH4.innerHTML = 'Cancelada por falta de entrega da amostra.';
                        break;
                }
                elementoLi.appendChild(statusH4);

                var bandeiraDiv = document.createElement('div');
                bandeiraDiv.classList.add('bandeiraEquipamento');
                switch (_s.id_equipamento) {
                    case 2:
                        bandeiraDiv.classList.add('panalytical');
                        break;
                    case 1:
                        bandeiraDiv.classList.add('rigakudrx');
                        break;
                    case 3:
                        // bandeiraDiv.classList.add('rigakufrx');
                        break;
                }
                elementoLi.appendChild(bandeiraDiv);

                var jElementoLi = $(elementoLi);

                jElementoLi.click(function() {
                    console.log('Click na solicitação', _s.id_solicitacao);
                    obterDetalhesSolicitacao(_s.id_solicitacao);
                    //$('.solicitacaoEmDetalhe').removeClass('solicitacaoEmDetalhe');
                    //$(this).addClass('solicitacaoEmDetalhe');
                });

                $(".listaSolicitacoes").append(elementoLi);

                setTimeout(function(){
                    $(elementoLi).fadeIn('slow').removeClass('escondido');
                }, 100*n++);

            });
        }
    });
}

function preencherSolicitacoesConcluidas() {
    $(".listaSolicitacoes").empty();
    $.ajax({
        url: 'acao.php',
        type: 'get',
        data: {
            q: 'obterListaSolicitacoesConcluidas',
            id: usuario.id,
            nivel_acesso: usuario.nivel_acesso,
            tipoSistema: tipo_sistema
        }
    }).done(function (r) {
        if (r.codigo !== 200) {
            apresentarErro(r);
        } else {
            var  n = 0;
            r.solicitacoes.forEach(function(_s) {

                // var elementoLi = "<li class=\"bloco relativo escondido\"><span class=\"solicitacaoMenuFlutuante floatRight\"><a" +
                //     " href=\"#/Dashboard/E/"+_s.id_solicitacao+"\">excluir</a> </span><h3" +
                // " class=\"solicitacaoIdentificacao\">"+_s.identificacao+"</h3>";
                //
                // elementoLi += "<h4 class=\"vermelho\">Aguardando autorização do professor</h4>";
                //
                //
                // elementoLi += "<p>Criada em <span class=\"solicitacaoDataSolicitacao\">"+_s.data_solicitacao+"</span><br>";
                // elementoLi += "<span class=\"solicitacaoDataSolicitacao\">Entregue em 15 de Abril</span>";
                // elementoLi += "</p></li>";

                var elementoLi = document.createElement('li');
                elementoLi.classList.add('bloco');
                elementoLi.classList.add('relativo');
                elementoLi.classList.add('escondido');

                elementoLi.id = "_sol" + _s.id_solicitacao;

                var identificacaoH3 = document.createElement('h3');
                identificacaoH3.classList.add('solicitacaoIdentificacao');
                identificacaoH3.innerHTML = _s.identificacao;
                elementoLi.appendChild(identificacaoH3);

                var statusH4 = document.createElement('h4');
                switch (_s.status) {
                    case 1:
                        statusH4.classList.add('cinza');
                        statusH4.innerHTML = 'Aguardando autorização do professor.';
                        break;

                    case 2:
                        statusH4.classList.add('cinza');
                        statusH4.innerHTML = 'Aguardando aprovação do laboratório.';
                        break;

                    case 3:
                        statusH4.classList.add('amarela');
                        statusH4.innerHTML = 'Aguardando confirmação de entrega da amostra';
                        break;

                    case 4:
                        statusH4.classList.add('amarela');
                        statusH4.innerHTML = 'Na fila do equipamento';
                        break;

                    case 5:
                        statusH4.classList.add('amarela');
                        statusH4.innerHTML = 'Em processo de análise.';
                        break;

                    case 6:
                        statusH4.classList.add('azul');
                        statusH4.innerHTML = 'Análise Concluída. Aguardando recolhimento da amostra.';
                        break;

                    case 7:
                        statusH4.classList.add('verde');
                        statusH4.innerHTML = 'Solicitação Finalizada.';
                        break;

                    case -1:
                        statusH4.classList.add('vermelha');
                        statusH4.innerHTML = 'Cancelada pelo responsável.';
                        break;

                    case -2:
                        statusH4.classList.add('vermelha');
                        statusH4.innerHTML = 'Cancelada pelo operador.';
                        break;

                    case -3:
                        statusH4.classList.add('vermelha');
                        statusH4.innerHTML = 'Cancelada por falta de entrega da amostra.';
                        break;
                }
                elementoLi.appendChild(statusH4);

                var bandeiraDiv = document.createElement('div');
                bandeiraDiv.classList.add('bandeiraEquipamento');
                switch (_s.id_equipamento) {
                    case 2:
                        bandeiraDiv.classList.add('panalytical');
                        break;
                    case 1:
                        bandeiraDiv.classList.add('rigakudrx');
                        break;
                    case 3:
                        // bandeiraDiv.classList.add('rigakufrx');
                        break;
                }
                elementoLi.appendChild(bandeiraDiv);

                var jElementoLi = $(elementoLi);

                jElementoLi.click(function() {
                    console.log('Click na solicitação', _s.id_solicitacao);
                    obterDetalhesSolicitacao(_s.id_solicitacao);
                    //$('.solicitacaoEmDetalhe').removeClass('solicitacaoEmDetalhe');
                    //$(this).addClass('solicitacaoEmDetalhe');
                });

                $(".listaSolicitacoes").append(elementoLi);

                setTimeout(function(){
                    $(elementoLi).fadeIn('slow').removeClass('escondido');
                }, 100*n++);

            });
        }
    });
}

function exibirSecao() {
    var hash = window.location.hash;
    var estadoAtual = $('.estadoAtual');
    console.log("aqui com hash ", hash, usuario);

    switch (hash) {
        case '#/Inicio':
            if (usuario !== null) {
                location.hash = '#/Dashboard';
                break;
            } else {
            estadoAtual.fadeOut('slow', function () {
                $(this).removeClass('estadoAtual');
                $('#Inicio').fadeIn('slow', function () {
                    $(this).addClass('estadoAtual');
                });
            });
            break;}

        case '#/Dashboard':
            if (usuario === null) {
                location.hash = '#/Inicio';
                break;
            } else {
            estadoAtual.fadeOut('slow', function () {
                $(this).removeClass('estadoAtual');

                $('#Dashboard').fadeIn('slow', function () {
                    $(this).addClass('estadoAtual');
                    $('#ListaSolicitacoes').addClass('ativo');

                    preencherSolicitacoes();
                });
            });
            break;}

        case '#/NovoUsuario':
            // TODO: Terminar
            estadoAtual.fadeOut('slow', function () {
                $(this).removeClass('estadoAtual');
                $('#NovoUsuario').fadeIn('slow', function () {
                    $(this).addClass('estadoAtual');
                });
            });
            break;

        case '#/NovaSolicitacao':
            $('#Principal, #Detalhe').fadeOut('slow', function () {
                $('#NovaSolicitacao').fadeIn('slow');
            });
            break;

        case '#/Sair':
            apagarCookie('uid');
            apagarCookie('tipoSistema');
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
        tipo_sistema = lerCookie('tipoSistema');
        $.ajax({
            url: 'acao.php',
            type: 'post',
            data: {
                q: 'loginDireto',
                uid: _uid,
                tipoSistema: tipo_sistema
            },
            async: false
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

    } else {
        usuario = null;
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
        tipo_sistema = $('#frm_login_tipo_academico').is(':checked') ? 1 : 2;

        $.ajax({
            url: 'acao.php',
            type: 'post',
            data: {
                q: 'login',
                email: _email,
                senha: _senha,
                tipoSistema: tipo_sistema
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
                    criarCookie('tipoSistema', tipo_sistema, 3/24);
                }

                $('#frm_login_email').val('');
                $('#frm_login_senha').val('');

                location.hash = '#/Dashboard';
                $('#Inicio').fadeOut('slow');
                $('header').toggle('drop', {direction:"up"});
            }
        });
    });


    $('#linkNovaSolicitacao').click(function () {
        $('.ativo').removeClass('ativo').fadeOut('slow', function () {
            var options = document.getElementById('frm_nova_solicitacao_equipamento');
            /* Limpa os equipamentos listados */
            $(options).empty();

            /* Obtém os equipamentos disponíveis */

            $.ajax({
                url: 'acao.php',
                type: 'get',
                async: false,
                data: {
                    q: 'obterListaEquipamentos'
                }
            }).done(function (r) {

                r.equipamentos.forEach(function (_e) {
                    var e = document.createElement('option');
                    e.value = _e.id_equipamento;
                    e.innerHTML = _e.nome;

                    options.appendChild(e);
                });
            });

            $('#FormNovaSolicitacao').trigger('reset');

            $('#NovaSolicitacao').fadeIn('slow').addClass('ativo');
        });
    });

    $('#linkListarSolicitacoesAbertas').click(function () {
        $('.ativo').removeClass('ativo').fadeOut('slow', function () {
            $('#ListaSolicitacoes').addClass('ativo').fadeIn('slow');
            preencherSolicitacoes();
        });
    });

    $('#linkListarSolicitacoesConcluidas').click(function () {
        $('.ativo').removeClass('ativo').fadeOut('slow', function () {
            $('#ListaSolicitacoes').addClass('ativo').fadeIn('slow');
            preencherSolicitacoesConcluidas();
        });
    });

    $('#FormNovaSolicitacao').submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        clearTimeout(timeoutErro);
        $('#DivErro').fadeOut('slow');


        var _tipo_analise = $('#frm_nova_solicitacao_tipo_analise_drx').is(':checked') ? 'drx' : 'frx';
        var _id_equipamento = $('#frm_nova_solicitacao_equipamento').val();
        var _dois_theta_inicial = $('#frm_nova_solicitacao_2theta_inicial').val();
        var _dois_theta_final = $('#frm_nova_solicitacao_2theta_final').val();
        var _composicao = $('#frm_nova_solicitacao_composicao').val();
        var _inflamavel = $('#frm_nova_solicitacao_seguranca_inflamavel').is('checked');
        var _corrosivo = $('#frm_nova_solicitacao_seguranca_corrosivo').is('checked');
        var _toxico = $('#frm_nova_solicitacao_seguranca_toxico').is('checked');
        var _higroscopico = $('#frm_nova_solicitacao_seguranca_higroscopico').is('checked');
        var _radioativo = $('#frm_nova_solicitacao_seguranca_radioativo').is('checked');
        var _observacoes = $('#frm_nova_solicitacao_observacoes').val();

        $.ajax({
            url: 'acao.php',
            type: 'post',
            data: {
                q: 'novaSolicitacaoAcademica',
                id_usuario: usuario.id,
                nivel_acesso: usuario.nivel_acesso,
                tipo_analise: _tipo_analise,
                id_equipamento: _id_equipamento,
                dois_theta_inicial: _dois_theta_inicial,
                dois_theta_final: _dois_theta_final,
                composicao: _composicao,
                inflamavel: _inflamavel,
                corrosivo: _corrosivo,
                toxico: _toxico,
                higroscopico: _higroscopico,
                radioativo: _radioativo,
                observacoes: _observacoes
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                $('#NovaSolicitacao').removeClass('ativo').fadeOut('slow', function () {
                    $('.identificadorAmostra').html(r.identificacao);

                    $('#NovaSolicitacaoIdentificador').fadeIn('slow').addClass('ativo');
                });
            }
        });


    });

});