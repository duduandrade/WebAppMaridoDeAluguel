/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$("#form_login").submit(function (e) {

    e.preventDefault();
    var formSerialize = $(this).serialize();
    var url = location.origin + '/maridoDeAluguel/login';
    $.ajax({
        type: "POST",
        url: url,
        data: formSerialize,
        success: function (result) {
            console.log(result);
            if (!result.erro) {
                window.location.href = location.origin + '/maridoDeAluguel/home';
            } else {
                alert(result.mensagem);
            }
        }
    });
});

$("#esqueciSenha").click(function () {
    $("#divSenha").hide();
    $("#btnPrincipal").hide();
    $("#esqueciSenha").hide();
    $("#form_senha").removeAttr('required');

    $("#esqueciSenhaMensagem").show();
    $("#btnEsqueciSenha").show();
});

$("#btnEsqueciSenha").click(function () {
    console.log("cliquei");
    

        var formSerialize = $("#form_login").serialize();
        var url = location.origin + '/maridoDeAluguel/esquecisenha';
        $.ajax({
            type: "POST",
            url: url,
            data: formSerialize,
            success: function (result) {
                console.log(result);
                if (!result.erro) {
                    console.log(result.mensagem);
                } else {
                    alert(result.mensagem);
                }
            }
        });
   
});
