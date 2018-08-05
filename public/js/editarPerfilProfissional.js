$(document).ready(function () {
    $('#cpf').mask('000.000.000-00', {reverse: true});
    $("#celular").mask("(00) 00000-0000");
    $("#cep").mask("99.999-999");

});

document.getElementById("fileToUpload").onchange = function () {
    var reader = new FileReader();

    reader.onload = function (e) {
        // get loaded data and render thumbnail.
        document.getElementById("image").src = e.target.result;
    };

    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
};

function salvarForm() {
    $("#idForm").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        var mensagem = {
            nome: $("#nome").val(),
            cpf: $("#cpf").val(),
            telefone: $("#celular").val(),
            enderecoresidencia: $("#enderecoresidencia").val(),
            numero: $("#numero").val(),
            bairro: $("#bairro").val(),
            cep: $("#cep").val()
        };
        console.log(JSON.stringify(mensagem));
        $.ajax({
            type: "POST",
            url: url,
            data: JSON.stringify(mensagem),
            dataType: "json",
            contentType: 'application/json',
            success: function (data)
            {
                console.log(data); // show response from the php script.
            }
        });


    });
}