@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Rotas</div>

                <div class="card-body">
                    <form action="" method="post" id="form_route">
                        <div class="form-group">
                            <label for="origem">Origem</label>
                            <input type="text" class="form-control" id="route_from" name="Origem"  onFocus="geolocate()" placeholder="Name" value="" required>
                        </div>

                        <div class="form-group">
                            <label for="origem">Destino</label>
                            <input type="text" class="form-control" id="route_to" name="Destino"  onFocus="geolocate()" placeholder="Name" value="" required>
                        </div>
                        
                      
                        <input type="submit" value="Traçar rota" class="form-control btn btn-success" />
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Rotas</div>

                <div class="card-body">
                    <div id="showFrom" style="width:100%;">
                        
                    </div>
                    <div id="showTo" style="width:100%;">
                        
                    </div>
                    <div id="mapShowPosition" style="height: 374px; width:100%;">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=AIzaSyA89LD5Q5a4Tya-D9L815mOzIWJwj_D-MA&libraries=places"></script>

    <script type="text/javascript">
		var map;
		var directionsService = new google.maps.DirectionsService();
		var info = new google.maps.InfoWindow({maxWidth: 200});
		
		var marker = new google.maps.Marker({
			title: 'Google Belo Horizonte',
			position: new google.maps.LatLng('-19.92965', '-43.94078')
		});

        
      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };
		
		function initialize() {
			var options = {
					zoom: 15,
					center: marker.position,
					mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			
			map = new google.maps.Map($('#mapShowPosition')[0], options);
			
			marker.setMap(map);
			
			google.maps.event.addListener(marker, 'click', function() {
				info.setContent('Avenida Bias Fortes, 382 - Lourdes, Belo Horizonte - MG, 30170-010, Brasil');
				info.open(map, marker);
			}); 

             autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('route_from')),
            {types: ['geocode']});

        autocomplete2 = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('route_to')),
            {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
            autocomplete.addListener('place_changed', fillInAddress);
		}

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
            document.getElementById(component).value = '';
            document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
            }
            }
        }

        // Bias the autocomplete object to the user's geographical location,
        // as supplied by the browser's 'navigator.geolocation' object.
        function geolocate() {
            if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
            }
        }
		
		$(document).ready(function() {
            initialize();
			$('#form_route').submit(function() {
				info.close();
				marker.setMap(null);
				
				var directionsDisplay = new google.maps.DirectionsRenderer();
				
				var request = {
						origin: $("#route_from").val(),
						destination: $("#route_to").val(),
						travelMode: google.maps.DirectionsTravelMode.DRIVING,
                        provideRouteAlternatives: true,
				};

                var end_from = $("#route_from").val();
                var end_to = $("#route_to").val();

                $.get( 'https://maps.googleapis.com/maps/api/geocode/json?address='+$('#route_from').val()+'&key=AIzaSyA89LD5Q5a4Tya-D9L815mOzIWJwj_D-MA', function(response) {
                    end_from =  $("#route_from").val() + " Lat: " + response.results[0].geometry.location.lat + " Lng: " + response.results[0].geometry.location.lng
                })
                .fail(function() {
                    alert( "error" );
                });

                $.get( 'https://maps.googleapis.com/maps/api/geocode/json?address='+$('#route_from').val()+'&key=AIzaSyA89LD5Q5a4Tya-D9L815mOzIWJwj_D-MA', function(response) {
                    end_to = $("#route_to").val() + " Lat: " + response.results[0].geometry.location.lat + " Lng: " + response.results[0].geometry.location.lng;
                })
                .fail(function() {
                    alert( "error" );
                });

                $("#showFrom").html("Endereço origem: " + end_from);
                $("#showTo").html("Endereço destino: " + end_to);

				directionsService.route(request, function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay.setDirections(response);
							directionsDisplay.setMap(map);
						}
				});
				
				return false;
			});
		});
	</script>
@endsection