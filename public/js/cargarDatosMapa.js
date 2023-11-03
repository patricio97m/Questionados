document.addEventListener('DOMContentLoaded', function () {
    initMap();
});

var map;
var marker;
var locationInput = document.getElementById('locationInput');
var pais = document.getElementById('pais');
var ciudad = document.getElementById('ciudad');

function initMap() {
    var mapOptions = {
        center: { lat: -34.6158238, lng: -58.4332985 },
        zoom: 10, // Nivel de zoom predeterminado.
    };

    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    // Inicializar el marcador en una ubicación predeterminada.
    marker = new google.maps.Marker({
        map: map,
        draggable: false,
    });

    var input = document.getElementById('locationInput');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        map.setCenter(place.geometry.location);
        map.setZoom(15);
        marker.setPosition(place.geometry.location);

        var city = '';
        var country = '';

        place.address_components.forEach(function (component) {
            if (component.types.includes('locality')) {
                city = component.long_name;
            }
            if (component.types.includes('country')) {
                country = component.long_name;
            }
        });

        // Actualiza el campo de entrada de ubicación con la ciudad y el país.
        locationInput.value = city + ', ' + country;
        ciudad.value = city;
        pais.value = country;
    });

    // Agregar un oyente de clic en el mapa.
    map.addListener('click', function (event) {
        marker.setPosition(event.latLng);

        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({ location: event.latLng }, function (results, status) {
            if (status === 'OK' && results[0]) {
                var place = results[0];
                var city = '';
                var country = '';

                place.address_components.forEach(function (component) {
                    if (component.types.includes('locality')) {
                        city = component.long_name;
                    }
                    if (component.types.includes('country')) {
                        country = component.long_name;
                    }
                });

                // Actualiza el campo de entrada de ubicación con la ciudad y el país.
                locationInput.value = city + ', ' + country;
                ciudad.value = city;
                pais.value = country;
            }
        });
    });
}
