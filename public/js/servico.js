/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
console.log("carregou");

$(document).ready(function () {
    $('input.autocomplete').autocomplete({
        data: {
            "Apple": null,
            "Microsoft": null,
            "Google": 'https://placehold.it/250x250'
        },
    });
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

function procurarProfissional(codigoServico) {

    window.location.href = location.origin + '/maridoDeAluguel/set/' + codigoServico;
}
function solicitarOrcamento() {

    window.location.href = location.origin + '/maridoDeAluguel/orcamento';
}