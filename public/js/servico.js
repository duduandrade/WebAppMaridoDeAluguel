/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
console.log("carregou");

$(document).ready(function () {
    $('.sidenav').sidenav();
    $('#slide-out').removeClass('sidenav-fixed');
    $('.modal').modal();

});
$(function () {
    $('.icon-bar a').on('click', function () {
        $(this).parent().find('a.active').removeClass('active');
        $(this).addClass('active');
    });
});
function visibilidade(id) {
    if (document.getElementById) {
        var divid = document.getElementById(id);
        var divs = document.getElementsByClassName("divsCategorias");
        for (var i = 0; i < divs.length; i++) {
            divs[i].style.display = "none";
        }
        divid.style.display = "block";


    }
    return false;
}

function procurarProfissional(codigoServico, valor) {
    $('#modal' + codigoServico + '').modal('open');
    $('#unidadeQuantidade' + codigoServico + '').on('keyup', function () {
        var quantidade = $(this).val();
        var final = parseFloat(valor) * quantidade;
        $('#valorTotal' + codigoServico + '').text("Valor total: R$" + parseFloat(final)); // get the current value of the input field.
    });

}
function solicitarOrcamento() {
    window.location.href = location.origin + '/maridoDeAluguel/orcamento/';
}
function salvarSessao(codigoServico, valor) {
    var quantidadeFinal = $('#unidadeQuantidade' + codigoServico + '').val();
    window.location.href = location.origin + '/maridoDeAluguel/set/' + codigoServico + '/' + quantidadeFinal;
}
function solicitarOrcamento() {

    window.location.href = location.origin + '/maridoDeAluguel/orcamento';
}