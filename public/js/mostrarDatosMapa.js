document.addEventListener('DOMContentLoaded', function () {
    initMap();
});

var map;
var pais = document.getElementById('pais');
var ciudad = document.getElementById('ciudad');
var marker;

console.log(pais)
console.log(ciudad)

function initMap() {
    var mapOptions = {
        center: { lat: -34.6158238, lng: -58.4332985 },
        zoom: 10, // Nivel de zoom predeterminado.
    };

    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    // Función para centrar el mapa en una ubicación específica y agregar un marcador.
    function centerMapAtLocation(location) {
        map.setCenter(location);
        map.setZoom(15); // Puedes ajustar el nivel de zoom según tus preferencias.

        // Elimina el marcador existente (si lo hay).
        if (marker) {
            marker.setMap(null);
        }

        // Agrega un nuevo marcador en la ubicación encontrada.
        marker = new google.maps.Marker({
            map: map,
            position: location,
        });
    }

    // Extraer valores directamente de los campos "pais" y "ciudad".
    var city = ciudad.value;
    var country = pais.value;
    var locationQuery = city + ', ' + country;

    var geocoder = new google.maps.Geocoder();

    geocoder.geocode({ address: locationQuery }, function (results, status) {
        if (status === 'OK' && results[0] && results[0].geometry && results[0].geometry.location) {
            var location = results[0].geometry.location;

            // Centra el mapa en la ubicación obtenida y agrega un marcador.
            centerMapAtLocation(location);
        } else {
            // Manejo de error si no se puede geocodificar la ubicación.
            console.error('No se pudo geocodificar la ubicación.');
        }
    });
}
