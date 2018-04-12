/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function ($) {
    window.addEventListener("load", function (event) {

        initMap();
    });
});

var map;
var marker;
function initMap() {

    var mapCentre;
    console.log(sketch_map_data);
    if (sketch_map_data.loclat && sketch_map_data.loclng) {
        var mapCentre = new google.maps.LatLng({lat: parseFloat(sketch_map_data.loclat), lng: parseFloat(sketch_map_data.loclng)})
    } else {
        var mapCentre = new google.maps.LatLng({lat: parseFloat(sketch_map_data.lat), lng: parseFloat(sketch_map_data.lng)})
    }
    //
    // Displays map at div id map 
    // see <div id="map" style="clear: both; height: 600px;"></div>
    //
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: mapCentre
    });

    placeMarkerAndPanTo(mapCentre, map)

    map.addListener('click', function (e) {
        geocodeLatLng(map, e.latLng);
    });
}

function placeMarkerAndPanTo(latLng, map) {
    marker = new google.maps.Marker({
        position: latLng,
        map: map,
        
    });
    map.panTo(latLng);

}

/*
 * 
 */

function geocodeLatLng(map, latlng) {

    var geocoder = new google.maps.Geocoder;
    var infowindow = new google.maps.InfoWindow;


    geocoder.geocode({'location': latlng}, function (results, status) {
        if (status === 'OK') {
            if (results[0]) {
                // remove markers
               
                marker.setMap(null);

                marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                   
                });
                google.maps.event.addListener(marker, "click", function () {
                    infowindow.open(map, marker);
                });
                infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);
                
                document.getElementById("sket-address-text").value = results[0].formatted_address;
                document.getElementById("sket-lat-text").value = results[0].geometry.location.lat();
                document.getElementById("sket-lng-text").value = results[0].geometry.location.lng();
                
                var addrComponents = get_addr_components(results[0].address_components);
                
                document.getElementById("sket-street-number-text").value = addrComponents.street_number;
                document.getElementById("sket-route-text").value = addrComponents.route;
                document.getElementById("sket-postal-code-text").value = addrComponents.postal_code;
                document.getElementById("sket-sublocality-text").value = addrComponents.sublocality;
                document.getElementById("sket-locality-text").value = addrComponents.locality;
                document.getElementById("sket-country-text").value = addrComponents.country;
                document.getElementById("sket-place-id-text").value = results[0].place_id;;
                // TODO place should also display here.

            } else {
                window.alert('No results found');
            }
        } else {
            window.alert('Geocoder failed due to: ' + status);
        }
    }
    );
}

function get_addr_components(address_components) {
    
    
    var components={}; 
    jQuery.each(address_components, function(k,v1) {
        jQuery.each(v1.types, function(k2, v2){
            components[v2]=v1.long_name;
        });
    });
    return components;    

}


