const $ = jQuery;

let AjaxActions = class AjaxActions {

	constructor( data = { element: null, map: map } ) {
		// element
		this.ele = data.element;

		// map relationship
		this.map = data.map;

		this.ajaxFired = false;

		// int Actions
		this._initActions();
	}

	// Event handler
	_initActions() {
		$( this.ele ).find( 'a.page-numbers' ).on( 'click', ( e ) => {
			let data = $.merge( $( '.opalhotel-ajax-filter-hotel input' ).serializeArray(), $( '.opalhotel_check_hotel_available:first' ).serializeArray() );
			data = $.merge( data, [ { name: 'paged', value: $( e.target ).data('paged') } ] );
    		this.load_hotels_available( data ).then( ( res ) => {
    			if ( res.places ) {
					new AjaxActions( { element: res.element, map: this.map } );
    			}
    		} );

			return false;
		} );

		$( this.ele ).find( 'select[name="sortable"]' ).on( 'change', ( e ) => {
			let url = $( e.target ).val();
	    	let sortable = $( e.target ).find( 'option:selected' ).data( 'sortable' );
			let data = $.merge( [ { name: 'display', value: $( e.target ).find( 'button:focus' ).val() } ], $( '.opalhotel-ajax-filter-hotel input' ).serializeArray() );
			data = $.merge( data, $( '.opalhotel_check_hotel_available:first' ).serializeArray() );
			data = $.merge( data, [ { name: 'sortable', value: sortable } ] );
			this.load_hotels_available( data ).then( ( res ) => {
    			if ( res.places ) {
					new AjaxActions( { element: res.element, map: this.map } );
    			}
    		} );;

			return false;
		} );

		$( this.ele ).find( '.display-mode' ).on( 'submit', ( e ) => {
			let data = $.merge( [ { name: 'display', value: $( e.target ).find( 'button:focus' ).val() } ], $( '.opalhotel-ajax-filter-hotel input' ).serializeArray() );
			data = $.merge( data, $( '.opalhotel_check_hotel_available:first' ).serializeArray() );
			this.load_hotels_available( data ).then( ( res ) => {
    			if ( res.places ) {
					new AjaxActions( { element: res.element, map: this.map } );
    			}
    		} );;

			return false;
		} );

	}

	load_hotels_available( data = [] ) {

		return new Promise( ( resolved, reject ) => {
			// Stop ajax action before
			if ( this.ajaxFired && this.ajaxFired.state() === 'pending' ) {
	            this.ajaxFired.abort();
	        }

			let wrapper = $('.opalhotel-hotel-available');

			// merge default arguments
			$.each( wrapper.data(), ( index, value ) => {
				data.push( { name: index, value: value } );
			} );

			let sortable = wrapper.find( 'select[name="sortable"] option:selected' );
			if ( sortable.length == 1 ) {
				// data.push({ name: 'sortable', value: sortable.data( 'sortable' ) });
				data.push({ name: 'sortable', value: sortable.val() });
			}

			this.ajaxFired = $.ajax({
				type: 'POST',
				url: OpalHotel.simpleAjaxUrl,
				data: data,
				beforeSend: () => {
					wrapper.addClass( 'loading' );
					if ( this.map ) {
						this.map.startLoading();
					}
				}
			}).always( () => {
				for ( var i = 0; i < data.length; i++ ) {
					let item = data[i];
					if ( typeof item.name !== 'undefined' && ( item.name === '_wp_http_referer' || item.name === 'departure' || item.name === 'arrival' ) ) {
						data.splice( i, 1 );
					}
				}
				let hashString = $.param( data );
		        if ( window.history.pushState ) {
		            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?'+ hashString;
		            window.history.pushState( { path : newurl }, data, newurl );
		        }
		        opalhotel_jBox_init();

		        if ( this.map ) {
		        	this.map.endLoading();
		        }
			}).done( ( res ) => {
				if ( typeof res.html !== 'undefined' ) {
					let parent = wrapper.parent();
					wrapper.replaceWith( res.html );
					let newEle = parent.find( '.opalhotel-hotel-available' );

    				if ( this.map ) {
						this.map.repaint( res.places );
    				}
					// resolved
					resolved( {
						places: res.places,
						element: newEle
					} );
				}
			}).fail( () => {
				reject();
			} );
		} );
	}

}

export default AjaxActions;