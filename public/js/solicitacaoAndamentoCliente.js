/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('.fixed-action-btn').floatingActionButton();



    $('.tooltipped').tooltip();

    $('.sidenav').sidenav();
    $('.modal').modal();
});

function cancelar(idSolicitacao) {
    var mensagem = {
        solicitacao: idSolicitacao
    };
    console.log("cancelar");
    $.ajax({
        type: "POST",
        url: "cancelar",
        dataType: "json",
        contentType: 'application/json',
        data: JSON.stringify(mensagem),
        success: function (result) {
            console.log(result);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });

}
function verificaStatusSolicitacao(mensagem) {
    console.log(JSON.stringify(mensagem));
    $.ajax({
        type: "POST",
        url: "verificarStatusSolicitacao",
        dataType: "json",
        contentType: 'application/json',
        data: JSON.stringify(mensagem),
        success: function (result) {
            console.log("result");
            console.log(result);
            console.log(JSON.stringify(result.data));
            if (!result.erro) {
                console.log(result.status);
            }
            if (result.status == 1) {
                $("#status1").show();
                $("#status2").hide();
                $("#status3").hide();
                $("#textoTrocaPreco").html("Não há informações para exibir");

            } else if (result.status == 2) {
                $("#status2").show();
                $("#status1").hide();
                $("#status3").hide();
                $("#textoTempoChegada").html("Profissional confirmado. Tempo de chegada aproximado:" + result.data.tempoChegada + "  min. </h5><br>");
                $("#textoTrocaPreco").html("Não há informações para exibir");

            } else if (result.status == 3 && result.data != null) {
                $("#status3").show();
                $("#status2").hide();
                $("#status1").hide();
                $("#textoTrocaPreco").html('<i class="material-icons">sim_card_alert</i>' +
                        'O profissional solicitou alteração do preço por motivo de <b> ' +
                        '' + result.data.motivoTrocaPreco + ' </b>.<br>' +
                        'O valor anterior <b> R$ ' + result.data.precoFinal + ' para R$ ' + result.data.novoValor + ' </b>' +
                        'Se você concorda, clique em CONCORDO, senão, o serviço será cancelado clicando em CANCELAR.');
            }

        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });

}