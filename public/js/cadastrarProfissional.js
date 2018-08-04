/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$("#form_senha_first").on("focusout", function (e) {
    if ($("#form_senha_first").val() != $("#form_senha_second").val()) {
        $("#form_senha_first").removeClass("valid").addClass("invalid");
    } else {
        $("#form_senha_first").removeClass("invalid").addClass("valid");
    }
});

$("#form_senha_second").on("keyup", function (e) {
    if ($("#form_senha_first").val() != $("#form_senha_second").val()) {
        $("#form_senha_second").removeClass("valid").addClass("invalid");
    } else {
        $("#form_senha_second").removeClass("invalid").addClass("valid");
    }
});
$("#form_senha_second").on("focusout", function (e) {
    if ($("#form_senha_first").val() != $("#form_senha_second").val()) {
        $("#form_senha_second").removeClass("valid").addClass("invalid");
    } else {
        $("#form_senha_second").removeClass("invalid").addClass("valid");
    }
});

$(document).ready(function () {
    $('#form_cpf').mask('000.000.000-00', {reverse: true});
    $("#form_telefone").mask("(00) 00000-0000");
    $("#form_cep").mask("99.999-999");

});

$("#button-cadastrar").click(function () {
    $('#formCadastrar').show();
    $(window).scrollTop($('#form_cadastro').offset().top);
});

$("#form_cadastro").submit(function (e) {
    $(".progress").show();
    console.log("teste");
    e.preventDefault();
    var formSerialize = $(this).serialize();

    var url = location.origin + '/maridoDeAluguel/profissional';
    console.log(formSerialize);
    $.ajax({
        type: "POST",
        url: url,
        data: formSerialize,
        success: function (result) {
            $(".progress").hide();
            console.log(result);
            if (!result.erro) {
                alert(result.mensagem + " Faça login para utilizar a plataforma.");
                window.location.href = location.origin + '/login';
                // alert(result.mensagem);
            } else {
                alert(result.mensagem);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
        }
    });
});