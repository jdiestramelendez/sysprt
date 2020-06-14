@extends('layouts.app')

@section('content')

  <div id="map"></div>

@endsection
@section('scripts')
 <script>

    const centerMap = {lat: -3.817315, lng: -38.598836}

          var map;
          function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
              center: centerMap,
              zoom: 12
            });
          }

//Depois de carregar os scripts
$(()=>{

var marker;

    //Eventos do maps property_changed
     map.addListener('click',(e) =>{
          marker = new google.maps.Marker({
            position: e.latLng,
            map: map,
            title: 'Ponto que você escolheu',
            icon: 'img/bus.png',
            animation: google.maps.Animation.DROP,
            draggable: true
        })
    })

})

  </script>
 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLdmyd2H-t2cBb0M_udCjGcn-Upgudx5I&callback=initMap"  async defer></script>
   @endsection

