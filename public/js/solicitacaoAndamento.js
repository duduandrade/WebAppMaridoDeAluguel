/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {

    $('.modal').modal();

});
function aceitarSolicitacao(solicita){
    var tempo = $("#tempoChegada").val();
    window.location.href = location.origin + '/maridoDeAluguel/aceitar/'+tempo+'/'+solicita; 
}
