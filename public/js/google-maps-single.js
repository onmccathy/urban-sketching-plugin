/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function ($) {
        window.addEventListener("load", function(event) {
        
        initMap();
      });
  });
function initMap() {
    
    // sketch_single_data worddpress localised data container
    // see class-sket-sketch-manager-public.php
    var mapCentre = new google.maps.LatLng({lat: parseFloat(sketch_single_data.lat), lng: parseFloat(sketch_single_data.lng)})
    var map = new google.maps.Map(document.getElementById('map'),{
      zoom: 15,
      center: mapCentre
    });
    
    var marker = new google.maps.Marker({
      position: mapCentre,
      map: map
    });
    
  }

