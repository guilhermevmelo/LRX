/**
 * Created by guilherme on 3/4/15.
 */

var tokens = [];
var usuario = null;
var tipo_sistema = null;
var timeoutErro = null;
var timeoutCronometroErro = null;
var equipamentosDisponiveis = [];

window.onload = iniciarAplicacao;
window.onhashchange = exibirSecao;

/**
 * Função obtida de http://www.quirksmode.org/js/cookies.html
 * @param name
 * @param value
 * @param days
 */
function criarCookie(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires="+date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name+"="+value+expires+"; path=/";
}

/**
 * Função obtida de http://www.quirksmode.org/js/cookies.html
 * @param name
 * @returns {*}
 */
function lerCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)===" ") {c = c.substring(1,c.length);}
        if (c.indexOf(nameEQ) === 0) {return c.substring(nameEQ.length,c.length);}
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
    $("#" + campo).html(valor);
}

function atualizarTokens() {
    tokens.nome = usuario.nome;
    tokens.genero = usuario.genero;
    switch(usuario.titulo) {
        case 0:
            //tokens.titulo = usuario.genero === 1 ? "Sr. " : "Sra. ";
            tokens.titulo = "";
            break;
        case 1:
            tokens.titulo = usuario.genero === 1 ? "Profº. " : "Profª. ";
            break;
        case 2:
            tokens.titulo = usuario.genero === 1 ? "Dr. " : "Dra. ";
            break;

        default:
            tokens.titulo = usuario.genero === 1 ? "Sr. " : "Sra. ";
    }

    switch (usuario.nivel_acesso) {
        case 1:
            tokens.perfil = usuario.genero === 1 ? "aluno" : "aluna";
            tokens.Vocativo = "Você";
            tokens.vocativo = "você";

            break;
        case 2:
            tokens.perfil = usuario.genero === 1 ? "professor" : "professora";
            tokens.Vocativo = usuario.genero === 1 ? "O sr." : "A sra.";
            tokens.vocativo = usuario.genero === 1 ? "o sr." : "a sra.";
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
            tokens.perfil = usuario.genero === 1 ? "operador" : "operadora";
            tokens.Vocativo = "Você";
            tokens.vocativo = "você";
            break;
        case 6:
            tokens.perfil = usuario.genero === 1 ? "administrador" : "administradora";
            tokens.Vocativo = usuario.genero === 1 ? "O sr." : "A sra.";
            tokens.vocativo = usuario.genero === 1 ? "o sr." : "a sra.";
            break;
    }

    tokens.num_mensagens = usuario.mensagens.length;
    tokens.possui_mensagens = (tokens.num_mensagens === 0) ? "não possui mensagens" : ("possui " + tokens.num_mensagens + " mensagens");

}

function atualizarCampos() {
    var token;
    for (token in tokens) {
        if (tokens.hasOwnProperty(token)) {
            $("#_" + token).html(tokens[token]);
        }
    }
}

function reiniciarContadorErro(tempo) {
    if (tempo < 0) {return;}

    $("#ErroCronometro").html(tempo);

    timeoutCronometroErro = setTimeout(function () {
        reiniciarContadorErro(tempo - 1);
    }, 1000);
}

function apresentarErro(erro) {
    clearTimeout(timeoutErro);
    clearTimeout(timeoutCronometroErro);
    $("#DivErro").fadeOut("slow", function () {
        $("#ErroP").html(erro.mensagem);
        reiniciarContadorErro(6);
        $(this).fadeIn("slow");

        timeoutErro = setTimeout(function () {
            $("#DivErro").fadeOut("slow");
        }, 6 * 1000);
    });
}

function dataPhpParaJs(data) {
    var d = new Date(data);
    return d.toDateString();
}

function obterDetalhesSolicitacao(id) {
    $("#Detalhe").fadeOut("slow", function () {
        $.ajax({
            url: "acao.php",
            type: "get",
            data: {
                q: "obterDetalhesSolicitacao",
                id_solicitacao: id,
                tipoSistema: tipo_sistema
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                $("#detalhe_Identificacao").html(r.solicitacao.identificacao);
                //$("#detalhe_Status").html();
                $("#detalhe_DataSolicitacao").html(dataPhpParaJs(r.solicitacao.data_solicitacao));
                if (r.solicitacao.data_entrega === null) {
                    $("#detalhe_DataRecebimento").html("A Amostra ainda não foi entregue.");
                } else {
                    $("#detalhe_DataRecebimento").html(dataPhpParaJs(r.solicitacao.data_entrega));
                }
                var detalhes_autorizar = $("#detalhe_Autorizar");
                if (usuario.nivel_acesso === 2 && r.solicitacao.status === 1) {
                    detalhes_autorizar.click(function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        $.ajax({
                            url: "acao.php",
                            type: "get",
                            data: {
                                q: "aprovarSolicitacao",
                                id_solicitacao: id,
                                id_professor: usuario.id
                            }
                        }).done(function (r) {
                            window.console.log(r);
                            if (r.codigo !== 200) {
                                apresentarErro(r);
                            } else {
                                //TODO: apresentar notificação
                                preencherSolicitacoes();
                                obterDetalhesSolicitacao(id);
                            }
                        });
                    });
                    detalhes_autorizar.show();
                } else {
                    detalhes_autorizar.hide();
                }


                $("#detalhe_Tipo").html(r.solicitacao.tipo_equipamento);
                $("#detalhe_Equipamento").html(r.solicitacao.equipamento);
                var statusH4 = $("#detalhe_Status");
                switch (r.solicitacao.status) {
                    case 1:
                        statusH4.addClass("cinza");
                        statusH4.html("Aguardando autorização do professor.");
                        break;

                    case 2:
                        statusH4.addClass("cinza");
                        statusH4.html("Aguardando aprovação do laboratório.");
                        break;

                    case 3:
                        statusH4.addClass("amarela");
                        statusH4.html("Aguardando confirmação de entrega da amostra");
                        break;

                    case 4:
                        statusH4.addClass("amarela");
                        statusH4.html("Na fila do equipamento");
                        break;

                    case 5:
                        statusH4.addClass("amarela");
                        statusH4.html("Em processo de análise.");
                        break;

                    case 6:
                        statusH4.addClass("azul");
                        statusH4.html("Análise Concluída. Aguardando recolhimento da amostra.");
                        break;

                    case 7:
                        statusH4.addClass("verde");
                        statusH4.html("Solicitação Finalizada.");
                        break;

                    case -1:
                        statusH4.addClass("vermelha");
                        statusH4.html("Cancelada pelo responsável.");
                        break;

                    case -2:
                        statusH4.addClass("vermelha");
                        statusH4.html("Cancelada pelo operador.");
                        break;

                    case -3:
                        statusH4.addClass("vermelha");
                        statusH4.html("Cancelada por falta de entrega da amostra.");
                        break;
                }

                $("#detalhe_Cancelar").click(function() {
                    //TODO adicionar confirmacao
                    $(this).off("click");
                    $("#Detalhe").removeClass("ativo").fadeOut("slow");
                    $("#_sol" + r.solicitacao.id_solicitacao).effect("blind");

                    $.ajax({
                        url: "acao.php",
                        type: "get",
                        data: {
                            q: "cancelarSolicitacao",
                            id: r.solicitacao.id_solicitacao,
                            uid: usuario.uid,
                            nivel_acesso: usuario.nivel_acesso
                        }
                    }).done(function (re) {
                        if (re.codigo !== 200) {
                            apresentarErro(re);
                        }
                    });
                });

                $("#detalhe_Configuracao").html(r.solicitacao.configuracao);

                $("#Detalhe").fadeIn("slow").addClass("ativo");

            }
        });
    });


}

// TODO fundir as duas funções em uma só

function preencherSolicitacoes() {
    $("#ListaSolicitacoes").empty();
    $("#Detalhe").fadeOut("slow");
    $("#NenhumaSolicitação").fadeOut("slow");
    $.ajax({
        url: "acao.php",
        type: "get",
        data: {
            q: "obterListaSolicitacoes",
            id: usuario.id,
            nivel_acesso: usuario.nivel_acesso,
            tipoSistema: tipo_sistema
        }
    }).done(function (r) {
        if (r.codigo !== 200) {
            apresentarErro(r);
        } else {
            window.console.log(r);
            var  n = 0;

            if (r.solicitacoes.length === 0) {
                $("#NenhumaSolicitação").fadeIn("slow");
            }

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

                var elementoLi = document.createElement("li");
                elementoLi.classList.add("bloco");
                elementoLi.classList.add("relativo");
                elementoLi.classList.add("escondido");

                elementoLi.id = "_sol" + _s.id_solicitacao;

                var identificacaoH3 = document.createElement("h3");
                identificacaoH3.classList.add("solicitacaoIdentificacao");
                identificacaoH3.innerHTML = _s.identificacao;
                elementoLi.appendChild(identificacaoH3);

                var statusH4 = document.createElement("h4");
                switch (_s.status) {
                    case 1:
                        statusH4.classList.add("cinza");
                        statusH4.innerHTML = "Aguardando autorização do professor.";
                        break;

                    case 2:
                        statusH4.classList.add("cinza");
                        statusH4.innerHTML = "Aguardando aprovação do laboratório.";
                        break;

                    case 3:
                        statusH4.classList.add("amarela");
                        statusH4.innerHTML = "Aguardando confirmação de entrega da amostra";
                        break;

                    case 4:
                        statusH4.classList.add("amarela");
                        statusH4.innerHTML = "Na fila do equipamento";
                        break;

                    case 5:
                        statusH4.classList.add("amarela");
                        statusH4.innerHTML = "Em processo de análise.";
                        break;

                    case 6:
                        statusH4.classList.add("azul");
                        statusH4.innerHTML = "Análise Concluída. Aguardando recolhimento da amostra.";
                        break;

                    case 7:
                        statusH4.classList.add("verde");
                        statusH4.innerHTML = "Solicitação Finalizada.";
                        break;

                    case -1:
                        statusH4.classList.add("vermelha");
                        statusH4.innerHTML = "Cancelada pelo responsável.";
                        break;

                    case -2:
                        statusH4.classList.add("vermelha");
                        statusH4.innerHTML = "Cancelada pelo operador.";
                        break;

                    case -3:
                        statusH4.classList.add("vermelha");
                        statusH4.innerHTML = "Cancelada por falta de entrega da amostra.";
                        break;
                }
                elementoLi.appendChild(statusH4);

                var bandeiraDiv = document.createElement("div");
                bandeiraDiv.classList.add("bandeiraEquipamento");
                switch (_s.id_equipamento) {
                    case 2:
                        bandeiraDiv.classList.add("panalytical");
                        break;
                    case 1:
                        bandeiraDiv.classList.add("rigakudrx");
                        break;
                    case 3:
                        // bandeiraDiv.classList.add("rigakufrx");
                        break;
                }
                elementoLi.appendChild(bandeiraDiv);

                var jElementoLi = $(elementoLi);

                jElementoLi.click(function() {
                    window.console.log("Click na solicitação", _s.id_solicitacao);
                    obterDetalhesSolicitacao(_s.id_solicitacao);
                    //$(".solicitacaoEmDetalhe").removeClass("solicitacaoEmDetalhe");
                    //$(this).addClass("solicitacaoEmDetalhe");
                });

                $("#ListaSolicitacoes").append(elementoLi);

                setTimeout(function(){
                    $(elementoLi).fadeIn("slow").removeClass("escondido");
                }, 100*n++);

            });
        }
    });
}

function preencherSolicitacoesConcluidas() {
    $("#ListaSolicitacoes").empty();
    $("#Detalhe").fadeOut("slow");
    $("#NenhumaSolicitação").fadeOut("slow", function () {
        $.ajax({
            url: "acao.php",
            type: "get",
            data: {
                q: "obterListaSolicitacoesConcluidas",
                id: usuario.id,
                nivel_acesso: usuario.nivel_acesso,
                tipoSistema: tipo_sistema
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                window.console.log(r);
                var  n = 0;

                if (r.solicitacoes.length === 0) {
                    $("#NenhumaSolicitação").fadeIn("slow");
                }

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

                    var elementoLi = document.createElement("li");
                    elementoLi.classList.add("bloco");
                    elementoLi.classList.add("relativo");
                    elementoLi.classList.add("escondido");

                    elementoLi.id = "_sol" + _s.id_solicitacao;

                    var identificacaoH3 = document.createElement("h3");
                    identificacaoH3.classList.add("solicitacaoIdentificacao");
                    identificacaoH3.innerHTML = _s.identificacao;
                    elementoLi.appendChild(identificacaoH3);

                    var statusH4 = document.createElement("h4");
                    switch (_s.status) {
                        case 1:
                            statusH4.classList.add("cinza");
                            statusH4.innerHTML = "Aguardando autorização do professor.";
                            break;

                        case 2:
                            statusH4.classList.add("cinza");
                            statusH4.innerHTML = "Aguardando aprovação do laboratório.";
                            break;

                        case 3:
                            statusH4.classList.add("amarela");
                            statusH4.innerHTML = "Aguardando confirmação de entrega da amostra";
                            break;

                        case 4:
                            statusH4.classList.add("amarela");
                            statusH4.innerHTML = "Na fila do equipamento";
                            break;

                        case 5:
                            statusH4.classList.add("amarela");
                            statusH4.innerHTML = "Em processo de análise.";
                            break;

                        case 6:
                            statusH4.classList.add("azul");
                            statusH4.innerHTML = "Análise Concluída. Aguardando recolhimento da amostra.";
                            break;

                        case 7:
                            statusH4.classList.add("verde");
                            statusH4.innerHTML = "Solicitação Finalizada.";
                            break;

                        case -1:
                            statusH4.classList.add("vermelha");
                            statusH4.innerHTML = "Cancelada pelo responsável.";
                            break;

                        case -2:
                            statusH4.classList.add("vermelha");
                            statusH4.innerHTML = "Cancelada pelo operador.";
                            break;

                        case -3:
                            statusH4.classList.add("vermelha");
                            statusH4.innerHTML = "Cancelada por falta de entrega da amostra.";
                            break;
                    }
                    elementoLi.appendChild(statusH4);

                    var bandeiraDiv = document.createElement("div");
                    bandeiraDiv.classList.add("bandeiraEquipamento");
                    switch (_s.id_equipamento) {
                        case 2:
                            bandeiraDiv.classList.add("panalytical");
                            break;
                        case 1:
                            bandeiraDiv.classList.add("rigakudrx");
                            break;
                        case 3:
                            // bandeiraDiv.classList.add("rigakufrx");
                            break;
                    }
                    elementoLi.appendChild(bandeiraDiv);

                    var jElementoLi = $(elementoLi);

                    jElementoLi.click(function() {
                        window.console.log("Click na solicitação", _s.id_solicitacao);
                        obterDetalhesSolicitacao(_s.id_solicitacao);
                        //$(".solicitacaoEmDetalhe").removeClass("solicitacaoEmDetalhe");
                        //$(this).addClass("solicitacaoEmDetalhe");
                    });

                    $("#ListaSolicitacoes").append(elementoLi);

                    setTimeout(function(){
                        $(elementoLi).fadeIn("slow").removeClass("escondido");
                    }, 100*n++);

                });
            }
        });
    });

}

function obterEquipamentos() {
    $.ajax({
        url: "acao.php",
        type: "get",
        async: false,
        data: {
            q: "obterListaEquipamentos",
            apenasDisponiveis: 1
        }
    }).done(function (r) {
        equipamentosDisponiveis = r.equipamentos;
    });
}

function atualizarOpcoesDeEquipamentos(tipo, elementoOption) {
    /* Limpa os equipamentos listados */
    $(elementoOption).empty();

    /* Adiciona apenas os disponíveis */
    equipamentosDisponiveis.forEach(function (_e) {
        if (_e.tipo === tipo) {
            var e = document.createElement("option");
            e.value = _e.id_equipamento;
            e.innerHTML = _e.nome;

            elementoOption.appendChild(e);
        }
    });
}

function obterParteDoHash(numeroDaParte) {
    var hash = window.location.hash;
    var partes = hash.split("/");
    return partes[numeroDaParte];
}

function exibirSecao() {
    var hash = obterParteDoHash(1);
    var estadoAtual = $(".estadoAtual");

    window.console.log(hash, usuario);

    switch (hash) {
        case "Inicio":
            if (usuario !== null) {
                location.hash = "#/Dashboard";
            } else {
                estadoAtual.fadeOut("slow", function () {
                    $(this).removeClass("estadoAtual");
                    $("#Inicio").fadeIn("slow", function () {
                        $(this).addClass("estadoAtual");
                    });
                });
            }
            break;

        case "Dashboard":
            if (usuario === null) {
                location.hash = "#/Inicio";
            } else {
                estadoAtual.fadeOut("slow", function () {
                    $(this).removeClass("estadoAtual");
                    $("#Dashboard").fadeIn("slow", function () {
                        $(this).addClass("estadoAtual");
                        $("#ListaSolicitacoes").addClass("ativo");
                        preencherSolicitacoes();
                    });
                });
            }
            break;

        case "NovoUsuario":
            // TODO: Terminar
            if (obterParteDoHash(2) === "Confirmar") {
                $.ajax({
                    url: "acao.php",
                    type: "get",
                    data: {
                        q: "confirmarEmail",
                        uid: obterParteDoHash(3)
                    }
                }).done(function (r) {
                    apresentarErro(r);
                });
                window.location.hash = "#/Inicio";
            } else {
                estadoAtual.fadeOut("slow", function () {
                    $(this).removeClass("estadoAtual");
                    $("#NovoUsuario").fadeIn("slow", function () {
                        $(this).addClass("estadoAtual");
                    });
                });
            }
            break;

        case "NovaSolicitacao":
            $("#Principal, #Detalhe").fadeOut("slow", function () {
                obterEquipamentos();
                $("#NovaSolicitacao").fadeIn("slow");
            });
            break;

        case "Sair":
            $("#ListaSolicitacoes").empty();
            $("#Detalhe").fadeOut("slow");
            apagarCookie("uid");
            apagarCookie("tipoSistema");
            usuario = null;
            $("header").effect("drop", {direction: "up"});
            location.hash = "#/Inicio";
            break;

        default:
            location.hash = "#/Inicio";
    }
}

function iniciarAplicacao() {
    var _uid = lerCookie("uid");
    if (_uid !== null) {
        tipo_sistema = lerCookie("tipoSistema");
        $.ajax({
            url: "acao.php",
            type: "post",
            data: {
                q: "loginDireto",
                uid: _uid,
                tipoSistema: tipo_sistema
            },
            async: false
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                usuario = {};
                usuario.id = r.id_usuario;
                usuario.documento = r.documento;
                usuario.nome = r.nome;
                usuario.email = r.email;
                usuario.email_alternativo = r.email_alternativo;
                usuario.titulo = r.titulo;
                usuario.genero = r.genero;
                usuario.telefone = r.telefone;
                usuario.nivel_acesso = r.nivel_acesso;
                usuario.limite = r.limite;
                usuario.confirmado = r.confirmado;
                usuario.email_confirmado = r.email_confirmado;
                usuario.uid = r.uid;
                usuario.mensagens = r.mensagens;
                usuario.estado = r.estado;
                usuario.cidade = r.cidade;

                atualizarTokens();
                atualizarCampos();

                $("header").toggle("drop", {direction:"up"});
                location.hash = "#/Dashboard";
            }
        });

    } else {
        usuario = null;
    }
    exibirSecao();
}

function definirMascaras() {
    $("#frm_novo_usuario_documento").mask("000.000.000-00", {reverse: true});
    var options =  {onKeyPress: function(tel, e, field, options){
        var masks = ["(00) 00000-0000', '(00) 0000-00009"];
        var mask = (tel.length>14) ? masks[0] : masks[1];
        $("#frm_novo_usuario_telefone").mask(mask, options);
    }};

    $("#frm_novo_usuario_telefone").mask("(00) 0000-00009", options);
}

/**
 *  Função de validação de CPF
 *  obtida de http://www.geradordecpf.org/funcao-javascript-validar-cpf.html em 13/09/2016
 */
function validaCPF(cpf)
{
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11)
        return false;
    for (i = 0; i < cpf.length - 1; i++)
        if (cpf.charAt(i) != cpf.charAt(i + 1))
        {
            digitos_iguais = 0;
            break;
        }
    if (!digitos_iguais)
    {
        numeros = cpf.substring(0,9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--)
            soma += numeros.charAt(10 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;
        numeros = cpf.substring(0,10);
        soma = 0;
        for (i = 11; i > 1; i--)
            soma += numeros.charAt(11 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
            return false;
        return true;
    }
    else
        return false;
}

function exibeOpcoesDoEquipamentoSelecionado(equipamento, opcoesDRX, opcoesFRX) {
    switch (parseInt(equipamento)) {
        case 1: // Rigaku DMAXB
            opcoesFRX.slideUp("slow", function () {
                opcoesDRX.slideDown("slow").removeClass("escondido");

                $("#frm_nova_solicitacao_delta_2theta").val(0.02);
            }).addClass("escondido");
            break;
        case 2: // PANalytical X'Pert PRO
            opcoesFRX.slideUp("slow", function () {
                opcoesDRX.slideDown("slow").removeClass("escondido");

                $("#frm_nova_solicitacao_delta_2theta").val(0.013);
            }).addClass("escondido");
            break;
        case 3: // Rigaku ZSX mini II
            opcoesDRX.slideUp("slow", function () {
                opcoesFRX.slideDown("slow").removeClass("escondido");
            }).addClass("escondido");
            break;
    }
}

$(document).ready(function () {

    atualizarCampos();
    definirMascaras();

    /**
     * Adiciona a opção de validar CPF ao validador.
     * O pacote "brazil" do validador já possui um, mas o default considera válidos
     * números de CPF em que todos os números são iguais.
     */
    $.formUtils.addValidator({
        name : "_cpf",
        validatorFunction : function(value, $el, config, language, $form) {
            var bloco1 = value.substring(0, 3);
            var bloco2 = value.substring(4, 7);
            var bloco3 = value.substring(8, 11);
            var bloco4 = value.substring(12, 14);

            var cpfNums = bloco1+bloco2+bloco3+bloco4;
            //window.console.log(value, cpfNums);

            return validaCPF(cpfNums);
        },
        errorMessage : "CPF inválido",
        errorMessageKey: "badCPF"
    });

    /**
     * Adiciona gatilhos de validação dos formulários
     */
    $.validate({
        modules: "jsconf, security, html5, toggleDisabled, brazil",
        onModulesLoaded: function () {
            $.setupValidation({
                lang: "pt",
                form: "#frmNovoUsuarioPasso1, #frmNovoUsuarioPasso2, #frmNovoUsuarioPasso3, #FormNovaSolicitacao, #FormLogin",
                validate: {
                    "#frm_novo_usuario_documento": {
                        validation: "_cpf"
                    },
                    "#frm_novo_usuario_senha": {
                        validation: "length",
                        length: "min8",
                        "error-msg": "A senha deve conter no mínimo 8 dígitos"
                    },
                    "#frm_novo_usuario_confirma_senha": {
                        validation: "confirmation",
                        confirm: "frm_novo_usuario_senha",
                        "error-msg": "As senhas não conferem"
                    },
                    "#frm_novo_usuario_telefone": {
                        validation: "brphone"
                    },
                    "#frm_login_senha" : {
                        validation: "length",
                        length: "min8",
                        "error-msg": "A senha deve conter no mínimo 8 dígitos"
                    }
                }
            });
        }
    });

    /**
     * Adiciona um gatilho para dispensar a mensagem de erro ao clique.
     */
    $("#Erro").click(function () {
        clearTimeout(timeoutErro);
        $(this).fadeOut("slow");
    });

    /**
     * Adiciona um gatilho para enviar a requisição de login e processar o resultado.
     */
    $("#FormLogin").submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        //$("#frm_login_sbmt").setAttribute('disabled', "disabled");

        clearTimeout(timeoutErro);
        $("#DivErro").fadeOut("slow");

        var shaObj = new jsSHA("SHA-1", "TEXT");
        shaObj.update($("#frm_login_senha").val());
        var _senha = shaObj.getHash("HEX");
        var _email = $("#frm_login_email").val();
        var _permanecer = $("#frm_login_manter_logado").is(":checked");
        tipo_sistema = $("#frm_login_tipo_academico").is(":checked") ? 1 : 2;

        $.ajax({
            url: "acao.php",
            type: "post",
            data: {
                q: "login",
                email: _email,
                senha: _senha,
                tipoSistema: tipo_sistema
            }
        }).done(function (r) {
            if (r.codigo !== 200) {
                apresentarErro(r);
            } else {
                usuario = {};
                usuario.id = r.id_usuario;
                usuario.documento = r.documento;
                usuario.nome = r.nome;
                usuario.email = r.email;
                usuario.email_alternativo = r.email_alternativo;
                usuario.titulo = r.titulo;
                usuario.genero = r.genero;
                usuario.telefone = r.telefone;
                usuario.nivel_acesso = r.nivel_acesso;
                usuario.limite = r.limite;
                usuario.confirmado = r.confirmado;
                usuario.email_confirmado = r.email_confirmado;
                usuario.uid = r.uid;
                usuario.mensagens = r.mensagens;
                usuario.estado = r.estado;
                usuario.cidade = r.cidade;

                atualizarTokens();
                atualizarCampos();

                if (_permanecer === true) {
                    criarCookie("uid", usuario.uid, 3/24);
                    criarCookie("tipoSistema", tipo_sistema, 3/24);
                }

                $("#frm_login_email").val("");
                $("#frm_login_senha").val("");

                location.hash = "#/Dashboard";
                $("#Inicio").fadeOut("slow");
                $("header").toggle("drop", {direction:"up"});
            }
        });
    });

    /**
     * Cadastro
     */
    $("#frmNovoUsuarioPasso1").submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        var sbmtNovoUsuarioPasso1 = $("#sbmtNovoUsuarioPasso1");
        sbmtNovoUsuarioPasso1.attr("disabled', 'disabled");

        var _documento = $("#frm_novo_usuario_documento").val();
        var _email = $("#frm_novo_usuario_email").val();

        $.ajax({
            url: "acao.php",
            type: "get",
            data: {
                q: "verificarDocumento",
                documento: _documento,
                email: _email
            }
        }).done(function (r) {
            if (r.codigo === 200) {
                window.console.log(r);
                if (r.existeDocumento || r.existeEmail) {
                    apresentarErro({mensagem:"Esse "+ (r.existeDocumento?"documento":"email") + " já está cadastrado. Caso não lembre sua senha, clique <a href=\"#/RecuperarConta\">aqui</a>."});
                    sbmtNovoUsuarioPasso1.removeAttr("disabled");
                } else{
                    $("#NovoUsuarioPasso1").fadeOut("slow", function () {
                        $(".passoAtual").removeClass("passoAtual");
                        $("#NovoUsuarioPasso2").fadeIn("slow").addClass("passoAtual");
                    });
                }
            }
        });
    });

    $("#frmNovoUsuarioPasso2").submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        $("#NovoUsuarioPasso2").fadeOut("slow", function () {
            $(".passoAtual").removeClass("passoAtual");
            $("#NovoUsuarioPasso3").fadeIn("slow").addClass("passoAtual");
        });
    });

    $("#frmNovoUsuarioPasso3").submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        var sbmtNovoUsuarioFinalizar = $("#btnNovoUsuarioFinalizar");
        sbmtNovoUsuarioFinalizar.attr("disabled', 'disabled");

        $("#NovoUsuarioPasso3").fadeOut("slow", function () {
            $(".passoAtual").removeClass("passoAtual");
            $("#NovoUsuarioPassoFinal").fadeIn("slow").addClass("passoAtual");
        });

        var shaObj = new jsSHA("SHA-1", "TEXT");
        shaObj.update($("#frm_novo_usuario_senha").val());
        var _senha = shaObj.getHash("HEX");
        var _documento = $("#frm_novo_usuario_documento").val();
        var _email = $("#frm_novo_usuario_email").val();
        var _nome = $("#frm_novo_usuario_nome").val();
        var _genero = $("#frm_novo_usuario_genero").val();
        var _email_alternativo = $("#frm_novo_usuario_email_alternativo").val();
        var _cidade = $("#frm_novo_usuario_cidade").val();
        var _estado = $("#frm_novo_usuario_estado").val();
        var _telefone = $("#frm_novo_usuario_telefone").val();
        var _ies = $("#frm_novo_usuario_ies").val();
        var _departamento = $("#frm_novo_usuario_departamento").val();
        var _laboratorio = $("#frm_novo_usuario_laboratorio").val();
        var _area_de_pesquisa = $("#frm_novo_usuario_area_de_pesquisa").val();
        var _titulo = $("#frm_novo_usuario_titulo").val();

        $.ajax({
            url: "acao.php",
            type: "post",
            data: {
                q: "cadastrarUsuario",
                documento: _documento,
                email: _email,
                nome : _nome,
                senha: _senha,
                genero:_genero,
                email_alternativo: _email_alternativo,
                cidade:_cidade,
                estado: _estado,
                telefone:_telefone,
                ies:_ies,
                departamento:_departamento,
                laboratorio:_laboratorio,
                area_de_pesquisa:_area_de_pesquisa,
                titulo:_titulo
            }
        }).done(function (r) {
            if (r.codigo === 200) {
                window.console.log(r);
                $("#NovoUsuarioFinalh1").html("Cadastro concluído");
                $("#NovoUsuarioFinalP").html("Sua solicitação de cadastro foi enviada com sucesso. Verifique seu email para informações adicionais.");
                $("#NovoUsuarioPassoFinal").append("<a href=\"#/Inicio\" title=\"Voltar à tela inicial\" class=\"botao vermelho\">Voltar à tela inicial</a>");
            } else {
                apresentarErro(r.mensagem);
                $("#NovoUsuarioFinalh1").html("Cadastro não concluído");
                $("#NovoUsuarioFinalP").html("Ocorreu um erro com sua solicitação de cadastro. Favor tentar novamente em alguns minutos. Caso o problema persista, entre em contato com os técnicos do laboratório no email lrxufc@gmail.com");
                $("#NovoUsuarioPassoFinal").append("<a href=\"#/Inicio\" title=\"Voltar à tela inicial\" class=\"botao vermelho\">Voltar à tela inicial</a>");
            }
            $(".passoAtual").removeClass("passoAtual");
            $("#NovoUsuarioPasso1").addClass("passoAtual");
        });
    });

    /**
     * Adiciona um gatilho para atualizar as opções de equipamentos, de acordo
     * com os disponíveis previamente obtidos do servidor.
     */
    $("[name=frm_nova_solicitacao_tipo_analise]").change(function() {
        $("#fld_nova_solicitacao_equipamento").slideDown("slow");

        var tipo = $(this).val();
        var options = document.getElementById("frm_nova_solicitacao_equipamento");
        atualizarOpcoesDeEquipamentos(tipo, options);
        $("#div_nova_solicitacao_comum").slideDown("slow").removeClass("escondido");
        exibeOpcoesDoEquipamentoSelecionado($("#frm_nova_solicitacao_equipamento").val(), $("#div_nova_solicitacao_config_drx"), $("#div_nova_solicitacao_config_frx"));
    });

    $("#frm_nova_solicitacao_equipamento").change(function () {
        exibeOpcoesDoEquipamentoSelecionado($(this).val(), $("#div_nova_solicitacao_config_drx"), $("#div_nova_solicitacao_config_frx"));
    });

    $("#linkNovaSolicitacao").click(function () {
        $("#NenhumaSolicitação").fadeOut("slow", function() {
            $(".ativo").removeClass("ativo").fadeOut("slow", function () {
                /* Obtém os equipamentos disponíveis */
                obterEquipamentos();
                $("#FormNovaSolicitacao").trigger("reset");
                $("#NovaSolicitacao").fadeIn("slow").addClass("ativo");
            });
        });

    });

    $("#linkListarSolicitacoesAbertas").click(function () {
        $(".ativo").removeClass("ativo").fadeOut("slow", function () {
            $("#ListaSolicitacoes").addClass("ativo").fadeIn("slow");
            preencherSolicitacoes();
        });
    });

    $("#linkListarSolicitacoesConcluidas").click(function () {
        $(".ativo").removeClass("ativo").fadeOut("slow", function () {
            $("#ListaSolicitacoes").addClass("ativo").fadeIn("slow");
            preencherSolicitacoesConcluidas();
        });
    });



    $("#FormNovaSolicitacao").submit(function (evento) {
        evento.stopPropagation();
        evento.preventDefault();

        clearTimeout(timeoutErro);
        $("#DivErro").fadeOut("slow");


        var _tipo_analise = $("#frm_nova_solicitacao_tipo_analise_drx").is(":checked") ? "drx" : "frx";
        var _id_equipamento = $("#frm_nova_solicitacao_equipamento").val();
        var _observacoes = $("#frm_nova_solicitacao_observacoes").val();

        var _tipo_amostra = $("#frm_nova_solicitacao_tipo_amostra").val();
        var _composicao = $("#frm_nova_solicitacao_composicao").val();

        // Segurança
        var _inflamavel = $("#frm_nova_solicitacao_seguranca_inflamavel").is("checked");
        var _corrosivo = $("#frm_nova_solicitacao_seguranca_corrosivo").is("checked");
        var _toxico = $("#frm_nova_solicitacao_seguranca_toxico").is("checked");
        var _higroscopico = $("#frm_nova_solicitacao_seguranca_higroscopico").is("checked");
        var _radioativo = $("#frm_nova_solicitacao_seguranca_radioativo").is("checked");

        // Apenas FRX
        var _tipo_medida = $("#frm_nova_solicitacao_tipo_medida_semi_quantitativa").is(":checked") ? "semi-quantitativa" : "quantitativa";
        var _tipo_resultado = $("#frm_nova_solicitacao_tipo_resultados_elementos").is(":checked") ? "elementos" : "oxidos";

        // Apenas DRX
        var _dois_theta_inicial = $("#frm_nova_solicitacao_2theta_inicial").val();
        var _dois_theta_final = $("#frm_nova_solicitacao_2theta_final").val();
        var _delta_dois_theta = $("#frm_nova_solicitacao_delta_2theta").val();


        $.ajax({
            url: "acao.php",
            type: "post",
            data: {
                q: "novaSolicitacaoAcademica",
                id_usuario: usuario.id,
                nivel_acesso: usuario.nivel_acesso,
                tipo_analise: _tipo_analise,
                id_equipamento: _id_equipamento,
                dois_theta_inicial: _dois_theta_inicial,
                dois_theta_final: _dois_theta_final,
                delta_dois_theta: _delta_dois_theta,
                tipo_medida: _tipo_medida,
                tipo_resultado: _tipo_resultado,
                tipo_amostra: _tipo_amostra,
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
                $("#NovaSolicitacao").removeClass("ativo").fadeOut("slow", function () {
                    $(".identificadorAmostra").html(r.identificacao);

                    $("#NovaSolicitacaoIdentificador").fadeIn("slow").addClass("ativo");
                });
            }
        });

    });

});