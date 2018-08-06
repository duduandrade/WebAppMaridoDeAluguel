

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
});
$("#form_cadastro").submit(function (e) {

    e.preventDefault();
    var formSerialize = $(this).serialize();

    var url = location.origin + '/maridoDeAluguel/cadastrar';
    $.ajax({
        type: "POST",
        url: url,
        data: formSerialize,
        success: function (result) {
            console.log(result);

            if (!result.erro) {
                M.toast({html: result.mensagem + " Faça login para utilizar a plataforma."});
                window.location.href = location.origin + '/maridoDeAluguel/login';

            } else {
                M.toast({html: result.mensagem});

            }
        }
    });
});