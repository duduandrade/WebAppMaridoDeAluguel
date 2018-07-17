/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function handlePermission() {
    navigator.permissions.query({name: 'geolocation'}).then(function (result) {
        if (result.state == 'granted') {
            report(result.state);

        } else if (result.state == 'prompt') {
            report(result.state);

            navigator.geolocation.getCurrentPosition(revealPosition, positionDenied, geoSettings);
        } else if (result.state == 'denied') {
            report(result.state);

        }
        result.onchange = function () {
            report(result.state);
        }
    });
}

function report(state) {
    console.log('Permission ' + state);
}


function pegaPosicao() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            console.log(pos);
            console.log(position);
            $.ajax({
                type: "POST",
                url: "localAtual",
                dataType: "json",
                contentType: 'application/json',
                data:JSON.stringify(pos),
                success: function (result) {
                    console.log("result");
                    console.log(result);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
            //   map.setCenter(pos);
        }, function () {
            // handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
        // Browser doesn't support Geolocation
        // handleLocationError(false, infoWindow, map.getCenter());
    }
}

handlePermission();
pegaPosicao();

window.setInterval(function () {

    handlePermission();
    pegaPosicao();

}, 300000); //300000 milissegundos = 5 minutos