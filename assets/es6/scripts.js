import OpalHotelMapMaster from "./google-map";
import AjaxActions from "./ajax-actions";

const $ = jQuery;

function opalhotel_load_hotels_available( action = AjaxActions, data = [], map = null ) {
	action.load_hotels_available( data ).then( ( res ) => {
		new AjaxActions( {
			element: res.element,
			map: map
		} );
	} );
};

$( document ).ready(function(){

	// map master
	let mapElements = $( '.opalhotel-map-master' );
	for ( let map of mapElements ) {
		if ( $( map ).parents( '.opalhotel-main-map' ).length == 0 ) {
			new OpalHotelMapMaster( map, $( map ).data() );
		}
	}

	// available
	let availableElements = $( '.opalhotel-hotel-available' );
	for ( let ele of availableElements ) {
		let parent = $( ele ).parents( '.opalhotel-main-map' );

		var map = null;
		if ( parent.length === 1 && parent.find( '.opalhotel-map-master' ) ) {
			let mapEles = parent.find( '.opalhotel-map-master' );
			for ( let mapEle of mapEles ) {
				map = new OpalHotelMapMaster( mapEle, $( mapEle ).data() );
			}
		}
		let action = new AjaxActions( { element: ele, map: map } );

		$( '.opalhotel_check_hotel_available' ).on( 'submit', ( e ) => {
			let data = $.merge( $( e.target ).serializeArray(), $( '.opalhotel-ajax-filter-hotel input' ).serializeArray() );
			data = $.merge( data, [ { name: 'paged', value: 1 } ] );

			opalhotel_load_hotels_available( action, data, map );

			return false;
		} );

		$( '.opalhotel-ajax-filter-hotel input[type="checkbox"]' ).on( 'change', ( e ) => {
			let data = $.merge( $( '.opalhotel-ajax-filter-hotel input' ).serializeArray(), $( '.opalhotel_check_hotel_available:first' ).serializeArray() );
			data = $.merge( data, [ { name: 'paged', value: 1 } ] );
			opalhotel_load_hotels_available( action, data, map );
			return false;
		} );

		// noUiSlider
		let sliders = $('.opalhotel-slide-ranger');
        sliders.each(function() {

            let _this = this;
            let unit = $(this).data( 'unit' );
            let decimals = $(this).data( 'decimals' );
            let min = $( '.slide-ranger-bar', this ).data( 'min' );
            let max = $( '.slide-ranger-bar', this ).data( 'max' );

            let imin = $( '.slide-ranger-min-input', this ).val();
            let imax = $( '.slide-ranger-max-input', this ).val();
            if ( typeof $.fn.noUiSlider !== 'undefined' ) {
                $( '.slide-ranger-bar', this ).noUiSlider({
                    range: {
                        'min': [min],
                        'max': [max]
                    },
                    start: [imin, imax],
                    connect: true,
                    serialization: {
                        lower: [
                            $.Link({
                                target: function(val) {
                                    $( '.slide-ranger-min-label', _this ).text( val );
                                    val = val.replace( unit, '' );
                                    $( '.slide-ranger-min-input', _this ).val( val );
                                }
                            })
                        ],
                        upper: [
                            $.Link({
                                target: function(val) {
                                    $( '.slide-ranger-max-label', _this ).text( val );
                                    val = val.replace(unit, '');
                                    $( '.slide-ranger-max-input', _this ).val( val );
                                }
                            })
                        ],
                        format: {
                            decimals: decimals,
                            prefix: unit,
                            suffix: unit
                        }
                    }
                }).change( function( e ){
                	let data = $.merge( $( '.opalhotel-ajax-filter-hotel input' ).serializeArray(), $( '.opalhotel_check_hotel_available:first' ).serializeArray() );
        			data = $.merge( data, [ { name: 'paged', value: 1 } ] );
        			opalhotel_load_hotels_available( action, data, map );
                });
            }
        });
	}

    let simpleSortable = $( 'select[name="sortable"]' );
    for ( let sortable of simpleSortable ) {
        if ( $( sortable ).parents( '.opalhotel-hotel-available' ).length == 0 ) {
            $( sortable ).on( 'change', ( e ) => {
                let val = $( e.target ).find( 'option:selected' ).data( 'sortable' );
                window.location.href = window.location.origin + window.location.pathname + '?sortable=' + val;
                return false;
            } );
        }
    }

});