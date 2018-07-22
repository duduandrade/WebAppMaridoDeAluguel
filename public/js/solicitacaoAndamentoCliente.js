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
                } else if (result.status == 2) {
                    $("#status2").show();
                    $("#status1").hide();
                }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    });

}