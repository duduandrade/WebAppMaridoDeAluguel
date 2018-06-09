/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$("#formNovaSenha_senha_first").on("focusout", function (e) {
    if ($("#formNovaSenha_senha_first").val() != $("#formNovaSenha_senha_second").val()) {
        $("#formNovaSenha_senha_first").removeClass("valid").addClass("invalid");
    } else {
        $("#formNovaSenha_senha_first").removeClass("invalid").addClass("valid");
    }
});

$("#formNovaSenha_senha_second").on("keyup", function (e) {
    if ($("#formNovaSenha_senha_first").val() != $("#formNovaSenha_senha_second").val()) {
        $("#formNovaSenha_senha_second").removeClass("valid").addClass("invalid");
    } else {
        $("#formNovaSenha_senha_second").removeClass("invalid").addClass("valid");
    }
});
$("#formNovaSenha_senha_second").on("focusout", function (e) {
    if ($("#formNovaSenha_senha_first").val() != $("#formNovaSenha_senha_second").val()) {
        $("#formNovaSenha_senha_second").removeClass("valid").addClass("invalid");
    } else {
        $("#formNovaSenha_senha_second").removeClass("invalid").addClass("valid");
    }
});


$("#form_nova_senha").submit(function (e) {

    e.preventDefault();
    var formSerialize = $(this).serialize();
    var url = window.location.href;
    $.ajax({
        type: "POST",
        url: url,
        data: formSerialize,
        success: function (result) {
            console.log(result);
            if (!result.erro) {
                   
                 alert(result.mensagem);
                window.location.href = location.origin + '/maridoDeAluguel/login';
            } else {
                alert(result.mensagem);
            }
        }
    });
});