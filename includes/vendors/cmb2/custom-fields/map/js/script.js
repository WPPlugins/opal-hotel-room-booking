(function( $ ) {
	'use strict';

	var maps = [];
 
	$( '.cmb-type-opalhotel-map' ).each( function() {
		initializeMap( $( this ) );
	});

	function initializeMap( mapInstance ) {
		var searchInput = mapInstance.find( '.opal-map-search' );
		var mapCanvas = mapInstance.find( '.opal-map' );
		var latitude = mapInstance.find( '.opal-map-latitude' );
		var longitude = mapInstance.find( '.opal-map-longitude' );
		var latLng = new google.maps.LatLng( 54.800685, -4.130859 );
		var zoom = mapInstance.find( '.opal-map-zoom' ).val() || 5;

		// If we have saved values, let's set the position and zoom level
		if ( latitude.val().length > 0 && longitude.val().length > 0 ) {
			latLng = new google.maps.LatLng( latitude.val(), longitude.val() );
		}

		// Map
		var mapOptions = {
			center: latLng,
			zoom: parseInt( zoom )
		};
		var map = new google.maps.Map( mapCanvas[0], mapOptions );
		map.addListener( 'zoom_changed', function() {
		    mapInstance.find('.opal-map-zoom').val( map.getZoom() );
	  	} );
		latitude.on('change', function() {
			map.setCenter( new google.maps.LatLng( latitude.val(), longitude.val() ) );
		});

		longitude.on('change', function() {
			map.setCenter( new google.maps.LatLng( latitude.val(), longitude.val() ) );
		});

		// Marker
		var markerOptions = {
			map: map,
			draggable: true,
			title: 'Drag to set the exact location'
		};
		var marker = new google.maps.Marker( markerOptions );

		//if ( latitude.val().length > 0 && longitude.val().length > 0 ) {
			marker.setPosition( latLng );
	//	}

		// Search
		var autocomplete = new google.maps.places.Autocomplete( searchInput[0] );
		autocomplete.bindTo( 'bounds', map );

		autocomplete.addListener( 'place_changed', function() {
			var place = autocomplete.getPlace();
			if ( ! place.geometry ) {
				return;
			}

			if ( place.geometry.viewport ) {
				map.fitBounds( place.geometry.viewport );
			} else {
				map.setCenter( place.geometry.location );
				map.setZoom( 17 );
			}

			marker.setPosition( place.geometry.location );

			latitude.val( place.geometry.location.lat() );
			longitude.val( place.geometry.location.lng() );
		});

		$( searchInput ).keypress( function( event ) {
			if ( 13 === event.keyCode ) {
				event.preventDefault();
			}
		});

		// Allow marker to be repositioned
		google.maps.event.addListener( marker, 'drag', function() {
			latitude.val( marker.getPosition().lat() );
			longitude.val( marker.getPosition().lng() );
		});

		maps.push( map );
	}

	// Resize map when meta box is opened
	if ( typeof postboxes !== 'undefined' ) {
		postboxes.pbshow = function () {
			var arrayLength = maps.length;
			for (var i = 0; i < arrayLength; i++) {
				var mapCenter = maps[i].getCenter();
				google.maps.event.trigger( maps[i], 'resize' );
				maps[i].setCenter( mapCenter );
			}
		};
	}

	// When a new row is added, reinitialize Google Maps
	$( '.cmb-repeatable-group' ).on( 'cmb2_add_row', function( event, newRow ) {
		var groupWrap = $( newRow ).closest( '.cmb-repeatable-group' );
		groupWrap.find( '.cmb-type-opal-map' ).each( function() {
			initializeMap( $( this ) );
		});
	});

})( jQuery );
