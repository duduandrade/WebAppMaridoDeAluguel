/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$("#form_login").submit(function(e) {

        e.preventDefault();
        var formSerialize = $(this).serialize();

        var url = location.origin + '/maridoDeAluguel/login';
        $.ajax({
            type: "POST",
            url: url,
            data: formSerialize,
            success: function (result) {
                console.log(result);
              if (!result.erro){
                  window.location.href = location.origin + '/maridoDeAluguel/home';

              }else{
                   alert(result.mensagem);
              }
            }
        });
    });