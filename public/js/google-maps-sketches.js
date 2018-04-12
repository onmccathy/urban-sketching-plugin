/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function ($) {
    window.addEventListener("load", function (event) {

        initMap(sketch_map_data.latCentre, sketch_map_data.lngCentre);
    });
});
function initMap(latCentre, lngCentre) {

    var mapCentre = new google.maps.LatLng({lat: parseFloat(latCentre), lng: parseFloat(lngCentre)})
    var geo_data = sketch_map_data.geo_data;
    var imagePath = sketch_map_data.cluster_images_url;
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 11,
        center: mapCentre,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var markers = [];
    geo_data.forEach(function (location_data) {

        var maplatlng = new google.maps.LatLng(
                location_data.lat,
                location_data.lng);
        var marker = new google.maps.Marker({
            position: maplatlng,
            map: map
        });

        var contentString = '<div>' +
                '<p><a href="' + location_data.permalink + '">' + location_data.post_title + '<p>' +
                '<img src="' + location_data.thumbnail_url + '"/></a>'
        '</div>'

        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });

        var oldInfoWindow = infowindow;
        google.maps.event.addListener(marker, 'click', (function (marker) {
            oldInfoWindow.close();
            return function () {
                infowindow.setContent(contentString);
                infowindow.setOptions({maxWidth: 200});
                infowindow.open(map, marker);
                oldInfoWindow = infowindow;
            }
        })(marker));
        markers.push(marker);

    });
    var mcOptions = {gridSize: 50, maxZoom: 12, imagePath: imagePath};
    var markerCluster = new MarkerClusterer(map, markers, mcOptions);

}

var dropdown = document.getElementById("cat");

function onCatChange() {
    var selected_location = dropdown.options[dropdown.selectedIndex].value
    if (selected_location > 0) {
        jQuery.ajax({
            url: sketch_map_data.ajaxurl,
            type: 'post',
            dataType: 'json',
            data: {
                'action': 'sket_map_select_handler',
                'sket_map_ajax_nonce': sketch_map_data.sket_map_ajax_nonce,
                'ajaxurl': sketch_map_data.ajaxurl,
                'sket_map_location': selected_location
            },
            success: function (response) {
                if (response.success) {
                    alert("Response " + response.latitude + ":" + response.latitude + ":" + response.zoomLevel);
                    console.log(response);
                } else {
                    alert("Response " + response.success + " : " + "Something Wrong : Loction Id : :" + response.data);
                }

            },
            error: function (errorThrown) {
                alert('Something went wrong');
                console.log(errorThrown);
            }
        });

    }
}
dropdown.onchange = onCatChange;



