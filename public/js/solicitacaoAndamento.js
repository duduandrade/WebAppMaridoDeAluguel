/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {

    $('.modal').modal();

});
function aceitarSolicitacao(solicita) {
    var tempo = $("#tempoChegada").val();
    window.location.href = location.origin + '/maridoDeAluguel/aceitar/' + tempo + '/' + solicita;
}
function verificarAceiteCliente(solicita) {
    var mensagem = {
        solicitacao: solicita
    };

    $.ajax({
        type: "POST",
        url: "verificarAceiteCliente",
        dataType: "json",
        contentType: 'application/json',
        data: JSON.stringify(mensagem),
        success: function (result) {
            console.log(result);
            if (result.autorizada) {
                location.reload();

            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}

function mudarPreco(solicita) {
    var mensagem = {
        solicitacao: solicita,
        novoPreco: $("#preco").val(),
        motivo: $("#motivo").val()
    };

    $.ajax({
        type: "POST",
        url: "alterarPreco",
        dataType: "json",
        contentType: 'application/json',
        data: JSON.stringify(mensagem),
        success: function (result) {
            console.log(result);
            window.setInterval(function () {

                verificarAceiteCliente(solicita);

            }, 30000); //300000 milissegundos = 5 minutos
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}
function finalizar(solicita) {
    var resposta = confirm("Você confirma que recebeu o pagamento?");
    if (resposta == true) {


        var mensagem = {
            solicitacao: solicita,
        };

        $.ajax({
            type: "POST",
            url: "finalizar",
            dataType: "json",
            contentType: 'application/json',
            data: JSON.stringify(mensagem),
            success: function (result) {
                console.log(result);
                alert("Solicitacao finalizada");
                window.location.href = location.origin + '/maridoDeAluguel/home';

            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
    }
}
function changeDisponivel(el) {
    if (el.checked) {
        var mensagem = {
            statusDisponivel: true
        };
        $("#mensagemDisponivel").text("Voce esta disponivel!");
    } else {
        var mensagem = {
            statusDisponivel: false
        };
        $("#mensagemDisponivel").text("Voce esta indisponivel");

    }
    $.ajax({
        type: "POST",
        url: "alterarStatusDisponivel",
        dataType: "json",
        contentType: 'application/json',
        data: JSON.stringify(mensagem),
        success: function (result) {
            console.log("result");
            console.log(result);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}