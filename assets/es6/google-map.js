import markerClusterer, { ClusterIcon } from 'marker-clusterer-plus-es2015';
import SnazzyInfoWindow from 'snazzy-info-window';
import ProgressBar from 'progressbar.js'
import CustomMarker from './custom-marker';

const $ = jQuery;

class OpalHotelMapMaster {

	constructor( ele, options = {} ) {

		//
		this.ele = ele;

		// options
		this.options = this.getOptions( options );

		// init google map
		this.map = new google.maps.Map( this.ele, this.options );

		// map bounds
		this.bounds = new google.maps.LatLngBounds();

		// setMarkers
		this.setMarkers();

		// create map controls
		this.createControls();

		google.maps.event.addListener( this.map, 'tilesloaded', () => {
			if ( this.progressbar ) {
	    		this.progressbar.stop();
			}
	    }, false);
	}

	// Merge default options to map options
	getOptions( options = {} ) {
		let defaults = {
			scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            panControl: false,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            draggable: false,
            streetViewControl: false,
            overviewMapControl: false,
            zoomControlOptions: false,
            streetViewControlOptions: false,
            center: {
                lat: 51.5074,
                lng: 0.1278
            },
            zoom: 12,
            maxZoom: 16,
            fullscreenControl: false,
            // styles: [
            // 	{
            // 		"stylers": [
            // 			{
            // 				"saturation": -100
            // 			}
            // 		]
            // 	}
            // ]
		};

		return Object.assign( options, defaults );
	}

	// set markers
	setMarkers( places = [ ...this.options.places ] ) {

		this.markers = [];
		// let loops placces
		for ( let place of places ) {
			this._addMarker( place );
		}
		this.map.fitBounds( this.bounds );
        this.map.panToBounds( this.bounds );

        this.markerClusterer = new markerClusterer( this.map, this.markers, {
        	ignoreHidden: true,
            maxZoom: 12,
            styles: [{
                textColor: '#000000',
                url: '',
                height: 70,
                width: 70,
                gridSize: 70
            }]
        } );
	}

	// add single marker
	_addMarker( place = { id: null, title: null, permalink: null, lat: null, lng: null, address: null } ) {
		let markerIcon = {
	        path: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
	        fillColor: '#e25a00',
	        fillOpacity: 0.95,
	        scale: 3,
	        strokeColor: '#fff',
	        strokeWeight: 3,
	        anchor: new google.maps.Point(12, 24)
	    };

	    let marker = new CustomMarker( place );

		this.markers.push( marker );
		// bounds
		this.bounds.extend( marker.position );

		// contentBox content
		let contentBox = `<div class="opalhotel-marker">`;
			contentBox += `<img src="` + place.thumbnail + `" />`;
			contentBox += `<h3>` + place.title + `</h3>`;
			contentBox += `<p>` + place.address + `</p>`;
			contentBox += `</div>`;

		let closeDelayed = false;
		let infoBox = new SnazzyInfoWindow({
	        marker: marker,
	        wrapperClass: 'opalhotel-snazzy-info-window',
	        offset: {
	            top: '-72px'
	        },
	        edgeOffset: {
	            top: 50,
	            right: 60,
	            bottom: 50
	        },
	        border: false,
	        closeButtonMarkup: '<button type="button" class="close">&#215;</button>',
	        content: wp.template( 'opalhotel-map-marker-content' )( place ),
	        callbacks: {
	            open() {
	                $( this.getWrapper() ).addClass('open');
	            },
	            afterOpen() {
	                var wrapper = $( this.getWrapper() );
	                wrapper.addClass('active');
	                wrapper.find('.close').on('click', () => {
	                	infoBox.close();
	                });
	            },
	            beforeClose() {
	                if ( ! closeDelayed ) {
	                    $( infoBox.getWrapper() ).removeClass('active');
				        setTimeout( () => {
				            closeDelayed = true;
				            infoBox.close();
				        }, 300);
	                    return false;
	                }
	                return true;
	            },
	            afterClose() {
	                var wrapper = $( this.getWrapper() );
	                wrapper.find('.close').off();
	                wrapper.removeClass('open');
	                closeDelayed = false;
	            }
	        }
	    });

	    google.maps.event.addListener( marker, 'click', () => {
	    	infoBox.open();
	    }, false);
	}

	/**
	 * Create map controls
	 *
	 */
	createControls() {
		let zoomControl = `<a class="zoom-in" href="javascript:void(0)"><i class="fa fa-plus" aria-hidden="true"></i></a><a class="zoom-out" href="javascript:void(0)"><i class="fa fa-minus" aria-hidden="true"></i></a>`;

		let control = document.createElement( 'div' );
		control.className = 'opalhotel-map-control opalhotel-zoom-control';
		control.style.marginBottom = control.style.marginTop = control.style.marginLeft = '10px';
		control.innerHTML = zoomControl;
		control.index = 1;
		this.map.controls[ google.maps.ControlPosition.RIGHT_TOP ].push( control );

		// zoom-in actions
		let zoomInActions = control.getElementsByClassName( 'zoom-in' );
		for ( let zoomInAc of zoomInActions ) {
			google.maps.event.addDomListener( zoomInAc, 'click', () => {
				this.map.setZoom( this.map.getZoom() + 1 );
			} );
		}
		// zoom-out
		let zoomOutActions = control.getElementsByClassName( 'zoom-out' );
		for ( let zoomOut of zoomOutActions ) {
			google.maps.event.addDomListener( zoomOut, 'click', () => {
				let zoom = this.map.getZoom() - 1;
				if ( zoom > 1 ) {
					this.map.setZoom( zoom );
				}
			} );
		}

		let reCenter = document.createElement( 'div' );
		reCenter.className = 'opalhotel-map-control';
		reCenter.innerHTML = `<a class="opalhotel-map-recenter"><i class="fa fa-compress"></i></a>`;//`<img src="`+ OpalHotel.assetsURI + `images/recenter.svg" />`;
		this.map.controls[ google.maps.ControlPosition.LEFT_CENTER ].push( reCenter );

		// re-center event action
		google.maps.event.addDomListener( reCenter, 'click', () => {
			let latlng = new google.maps.LatLng( this.options.center.lat, this.options.center.lng );
			this.map.fitBounds( this.bounds );
        	this.map.panToBounds( this.bounds );

        	// re-setCenter fixed fitbounds twice
        	let mapcenter = this.map.getCenter();
			this.map.setCenter(new google.maps.LatLng((mapcenter.lat() + 0.0000001),
			mapcenter.lng()));
		} );

		let geoLocation = document.createElement( 'div' );
		geoLocation.className = 'opalhotel-map-control';
		// geoLocation.style.width = geoLocation.style.height = '40px';
		geoLocation.innerHTML = `<a class="opalhotel-map-geolocation"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>`;//`<img src="` + OpalHotel.assetsURI + `images/geo-location.svg" />`;
		this.map.controls[ google.maps.ControlPosition.LEFT_CENTER ].push( geoLocation );

		// geo Location
		google.maps.event.addDomListener( geoLocation, 'click', () => {
			let center = this.getLocation().then( ( position ) => {
				this.map.setCenter( position );
				$( geoLocation ).addClass( 'active' );
			} ).catch( ( message = '' ) => {
				opalhotel_print_notice( message );
			} );
		} );

		let toggleLockMap = document.createElement( 'div' );
		toggleLockMap.className = 'opalhotel-map-control';
		// toggleLockMap.style.width = toggleLockMap.style.height = '40px';
		toggleLockMap.innerHTML = `<a class="opalhotel-map-toggle-lock-map"><i class="fa fa-lock" aria-hidden="true"></i></a>`;//`<img src="`+ OpalHotel.assetsURI + `images/lock.svg" />`;
		this.map.controls[ google.maps.ControlPosition.LEFT_CENTER ].push( toggleLockMap );

		// toggle lock map
		google.maps.event.addDomListener( toggleLockMap, 'click', () => {
			$( toggleLockMap ).toggleClass( 'active' );

			var draggable = false;
			var scrollwheel = false;
			if ( $( toggleLockMap ).hasClass( 'active' ) ) {
				draggable = scrollwheel = true;
				$( toggleLockMap ).find( 'i' ).removeClass( 'fa-lock' ).addClass('fa-unlock');
			} else {
				$( toggleLockMap ).find( 'i' ).addClass( 'fa-lock' ).removeClass('fa-unlock');
			}
			this.map.set( 'draggable', draggable );
			this.map.set( 'scrollwheel', scrollwheel );
		} );

		// toggle dropdown countries list
		let button = document.createElement( 'div' );
		button.className = 'opalhotel-map-control';
		button.innerHTML = `<a class="opalhotel-map-countries-button"><i class="fa fa-globe" aria-hidden="true"></i></a>`;//`<img src="`+ OpalHotel.assetsURI + `images/earth.jpg" />`;
		this.map.controls[ google.maps.ControlPosition.LEFT_TOP ].push( button );

		// let countryDiv = this.ele.getElementsByClassName( 'opalhotel-map-countries' );
		let dropdown = document.createElement( 'div' );
		dropdown.className = 'opalhotel-map-countries hide';

		dropdown.index = 1;
		dropdown.innerHTML = wp.template( 'opalhotel-map-countries-list' )();
		dropdown.style.left = '50px';
		this.map.controls[ google.maps.ControlPosition.TOP_LEFT ].push( dropdown );

		if ( typeof $.fn.mCustomScrollbar == 'function' ) {
			$( dropdown ).find( 'ul' ).mCustomScrollbar();
		}

		let countriesLine = dropdown.getElementsByTagName( 'li' );
		for ( let line of countriesLine ) {
			google.maps.event.addDomListener( line, 'click', () => {
				if ( this.layer ) {
					this.layer.setMap( null );
				}
		      	let geocoder = new google.maps.Geocoder();
		      	let location = $( line ).data( 'country' );

		      	geocoder.geocode({ 'address': location }, ( results, status ) => {
		          	if ( status == google.maps.GeocoderStatus.OK ) {
	              		this.map.fitBounds( results[0].geometry.bounds );
	              		let placeID = results[0].place_id;

	              		this.layer = new google.maps.FusionTablesLayer({
						  	query: {
						  		select: "kml_4326",
					          	from: 420419,
					          	where: "name_0 = '"+location+"'"
					     	},
						  	styles: [{
						  		polygonOptions: {
								    strokeOpacity: 0.4,
								    fillOpacity: 0.7,
								    fillColor: "#FF9800"
							  	}
							}]
						});

				        this.layer.setMap(this.map);
		          	} else {
		          		opalhotel_print_notice( OpalHotel.lable.oops );
		          	}
		      	});
		      	$( dropdown ).toggleClass( 'hide fade in' );
			}, false );
		}

		google.maps.event.addDomListener( button, 'click', () => {
			$( dropdown ).toggleClass( 'hide fade in' );
		} );

	}

	// map control
	getLocation() {
		return new Promise( ( resolved, reject ) => {
			if ( typeof this.ajaxGetLocation !== 'undefined' ) {
				this.ajaxGetLocation.abort();
			}
			if ( this.currentPosition ) {
				resolved( this.currentPosition );
			}
			navigator.geolocation.getCurrentPosition( ( position, a, b ) => {
				let pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                if ( ! this.currentPosition ) {
                    this.currentPosition = pos;
                }

                resolved( this.currentPosition );
			}, () => {

				this.ajaxGetLocation = $.ajax({
					url: OpalHotel.simpleAjaxUrl,
					type: 'POST',
					data: {
						action: 'opalhotel_get_position_by_agent_ip',
						nonce: OpalHotel.nonces.get_location_by_ip
					},
					beforeSend: () => {
						// this.ele.addClass( 'processing' );
					}
				}).always( () => {
					// this.ele.removeClass( 'processing' );
				} ).done( ( res ) => {
					if ( res.status === true ) {
						if ( ! this.currentPosition ) {
		                    this.currentPosition = res;
		                }

						resolved( this.currentPosition );

						if ( typeof res.message !== 'undefined' ) {
							opalhotel_print_notice( res.message, 'success' );
						}
					} else {
						reject( res.message );
					}
				} ).fail( () => {
					reject();
				} );
			} );
		} );
	}

	// Markers
	repaint( places = [] ) {
		// markers
		this.markers = [];

		this.markerClusterer.clearMarkers();
		// map bounds
		this.bounds = new google.maps.LatLngBounds();

		// add new marker
		for ( let place of places ) {
			this._addMarker( place );
		}
		this.map.fitBounds( this.bounds );
        this.map.panToBounds( this.bounds );

        this.markerClusterer = new markerClusterer( this.map, this.markers, {
        	ignoreHidden: true,
            maxZoom: 12,
            styles: [{
                textColor: '#000000',
                url: '',
                height: 70,
                width: 70,
                gridSize: 70
            }]
        } );
	}

	startLoading() {

		this.progressbarDiv = document.createElement( 'div' );
		this.progressbarDiv.className = 'opalhotel-progress-map';
		this.ele.append( this.progressbarDiv );
		if ( ! this.progressbar ) {

			this.progressbar = new ProgressBar.Circle( this.progressbarDiv, {
  				strokeWidth: 5,
			  	trailWidth: 5,
			  	easing: 'easeInOut',
			    color: '#FCB03C'
			});
		}

		let timeout = setInterval( () => {
			this.progressbar.animate( 0.1, {
				easing: this.getEffect()
			} );
			clearInterval( timeout );
		}, 100 );
	}

	endLoading() {
		if ( this.progressbar.value() >= 0.1 ) {
			this.progressbar.set( 1.0 );
			if ( this.progressbarDiv ) {
				$( this.progressbarDiv ).remove();
			}
			if ( this.progressbar ) {
				this.progressbar.destroy();
				this.progressbar = null;
			}
		} else {
			this.progressbar.animate( 1.0, {
				easing: this.getEffect()
			}, () => {
				if ( this.progressbarDiv ) {
					$( this.progressbarDiv ).remove();
				}
				if ( this.progressbar ) {
					this.progressbar.destroy();
					this.progressbar = null;
				}
			} );
		}
	}

	getEffect() {
		let effects = [ 'linear', 'easeOut', 'easeIn', 'easeInOut' ];
		return effects[ Math.floor( Math.random() * 4 ) ];
	}

}

export default OpalHotelMapMaster;