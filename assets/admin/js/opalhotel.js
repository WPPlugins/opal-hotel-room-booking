'use strict';
(function($){

	/* object init */
	var OpalHotelAdmin = {

		/* init */
		init: function() {

			// tootip
			this.tootip();

			// metaboxes tabs
			this.metaboxes.init();

			/* init sortable */
			this.sortable();
		},

		/* display tooltip description */
		tootip: function() {
			$( '.opalhotel_tiptip' ).tipTip({
			    'attribute': 'data-tip',
			    'fadeIn': 50,
			    'fadeOut': 50,
			    'delay': 200
		    });
		},

		sortable: function(){
			$( '.opalhotel_sortable_container' ).sortable();
		},

		/* metabox js process */
		metaboxes: {

			init: function() {

				var _doc = $( document );

				/* booking metaboxes */
				this.booking.init();

				/* metaboxes tabs */
				_doc.on( 'click', '.opalhotel_tab a', this.metaboxes_tabs );

				/* gallery metaboxes */
				_doc.on( 'click', '.opalhotel_add_gallery', this.add_gallery );

				/* gallery media callback */
				_doc.on( 'opalhotel_gallery_selected', this.gallery_callback );

				/* remove media */
				_doc.on( 'click', '.opalhotel_room_images .delete', this.remove_gallery );

				/* add extra metaboxes */
				_doc.on( 'click', '.opalhotel_add_extra', this.add_extra );

				/* remove extra */
				_doc.on( 'click', '.opalhotel_remove_extra', this.remove_extra );

				/* add package */
				_doc.on( 'click', '#opalhotel_room_add_package', this.add_package );

				/* remove package */
				_doc.on( 'click', '#package_room_data .remove_package', this.remove_room_package );

				/* select2 with search package in room data */
				this.select2();

				$( '#opalhotel_pricing_panel' ).accordion();
				/* pricing calendar */
				this.pricing_calendar();
				_doc.on( 'click', '#opalhotel_update_pricing', this.update_pricing );
				_doc.on( 'click', '#opalhotel_pricing_filter', this.filter_pricing );



				////
				/////// 
				$('.opalhotel_metabox_data_container .btn-action').click( function() {
					var $tmp = $('#extra_amenities .extra_amenities_tmp'); 
					var $c = $tmp.clone().removeClass('extra_amenities_tmp').addClass('extra_amenities_ipts');
					$tmp.before( $c );
				} );

				$('.opalhotel_metabox_data_container').delegate( '.btn-remove', 'click' , function() {  
					if( confirm($(this).data('confirmed')) ){
						$(this).parents('tr').remove();
					}
					
				} );
			},

			/* display tab */
			metaboxes_tabs: function( e ) {
				e.preventDefault();

				var _self = $(this),
					_target = _self.attr( 'href' ),
					_container = _self.parents( '.opalhotel_metabox_data_container' );

				/* hide old */
				_container.find( '.opalhotel_tab a' ).removeClass( 'active' );
				_container.find( '.opalhotel_room_data_panel' ).removeClass( 'active' );

				/* show */
				_self.addClass( 'active' );
				$( _target ).addClass( 'active' );

				return false;
			},

			/* media */
			add_gallery: function( e ) {
				e.preventDefault();
				var _self = $(this);
				/* show lightbox media */
				OpalHotelAdmin.galleries( _self );
			},

			/* add gallery callback */
			gallery_callback: function( e, target, attachments ) {
				var _target = $( target ),
					_html = [];

				for ( var i = 0; i < attachments.length; i ++ ) {
					var attachment = attachments[ i ];
					if ( _target.hasClass( 'opalhotel_add_gallery' ) ) {
						_html.push( '<li class="image" data-attachment_id="' + attachment.id + '">' );

						_html.push( '<img width="150" height="150" src="'+ attachment.url +'" />' );

						_html.push( '<ul class="actions">' );

						_html.push( '<li><a href="#" class="delete tips" data-tip="' + OpalHotel.label.delete +'"><i class="fa fa-times" aria-hidden="true"></i></a></li>' );

						_html.push( '</ul>' );

						_html.push( '<input type="hidden" name="_gallery[]" value="' + attachment.id + '" />' )

						_html.push( '</li>' );
					}
				}

				$( '.opalhotel_room_images' ).append( _html.join( '' ) );
			},

			/* remove galleries */
			remove_gallery: function( e ) {
				e.preventDefault();
				var _self = $( this ),
					_image = _self.parents( '.image' );

				_image.remove();
				return false;
			},

			/* add extra */
			add_extra: function( e ) {
				e.preventDefault();
				var _self = $( this ),
					_tmpl = _self.attr( 'data-template' ),
					_table = _self.parents( 'table:first' ),
					_name = _self.attr( 'data-name' ),
					_tmpl = wp.template( _tmpl )({ name: _name });

				// console.debug( _self.parents('table:first') );
				_table.find( 'tbody' ).append( _tmpl );
			},

			/* remove extra */
			remove_extra: function( e ) {
				e.preventDefault();
				var _self = $( this ),
					_tr = _self.parents( 'tr:first' ).remove();
			},

			/* add package to room */
			add_package: function( e ) {
				e.preventDefault();
				var	_self = $( this ),
					_packages = $( '#opalhotel_room_package' ),
					package_id = _packages.val(),
					room_id 	= $( '#post_ID' ).val();
				if ( package_id === null ) {
					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.select.empty
		                }
		            });
					return false;
				}

				$.ajax({
					url: OpalHotel.ajaxurl,
					method: 'POST',
					data: {
						'package_id'		: package_id,
						'room_id'			: room_id,
						'action'			: 'opalhotel_add_room_package'
					},
					beforeSend: function() {
						_self.append( '<i class="fa fa-spinner fa-spin"></i>' );
					}
				}).done( function( res ){
					/* stop ajax effect */
					_self.find( '.fa' ).remove();

					if ( res.status === false && typeof res.message !== 'undefined' ){
						_self.opalhotel_modal({
			                tmpl: 'opalhotel-confirm',
			                settings: {
			                	message: res.message
			                }
			            });
					} else {
						var template = wp.template( 'opalhotel-package' )( res ),
							_sort_container = $( '#opalhotel-room-packages' ),
							html = [];

						/* push wrap */
						if ( _sort_container.length === 0 ) {
							html.push( '<div class="opalhotel_field_group opalhotel_sortable_container" id="opalhotel-room-packages">' );
						}

						/* push template*/
						html.push( template );

						/* push end wrap */
						if ( _sort_container.length === 0 ) {
							html.push( '</div>' );
						}

						html = html.join('');
						if ( _sort_container.length === 0 ) {
							$( '#package_room_data' ).append( html );
							OpalHotelAdmin.sortable();
						} else {
							_sort_container.append( html );
						}
					}
				} ).fail( function(){
					/* stop ajax effect */
					_self.find( '.fa' ).remove();
					/* alert error message */
					_self.opalhotel_modal({
			                tmpl: 'opalhotel-confirm',
			                settings: {
			                	message: OpalHotel.something_wrong
			                }
			            });
				} );

				return false;
			},

			remove_room_package: function( e ) {
				e.preventDefault();
				var _self = $( this ),
					_paren = _self.parents( '.opalhotel_sortable' ).remove();
				return false;
			},

			/* select2 admin */
			select2: function(){
				/* select pageages */
				var _packages = $( '#opalhotel_room_package' ),
					_room_id = $('#post_ID').val();
				_packages.select2({
                    placeholder: OpalHotel.select.placeholder,
                    formatInputTooShort: OpalHotel.select,
                    minimumInputLength: 3,
                    ajax: {
                        url: OpalHotel.ajaxurl,
                        dataType: 'json',
                        type: 'POST',
                        quietMillis: 50,
                        data: function ( term ) {
                            return {
                                package: term.term,
                                room_id : _room_id,
                                action: 'opalhotel_load_package_api'
                            };
                        },
                        processResults: function ( data ) {
                            return {
                                results: $.map( data, function ( item ) {
                                    return {
                                        text    : item.post_title,
                                        id      : item.ID
                                    }
                                })
                            };
                        },
                        cache: true
                    }
                });

			},

			/* pricing load */
			pricing_calendar: function(){
				var _calendar = $( '#opalhotel-room-pricing .calendar' ),
					room_id = $( '#post_ID' ).val();
				if ( typeof room_id === 'undefined' || ! room_id ) {
					return;
				}
				_calendar.fullCalendar({
				    header: {
						left: '',
						center: 'title',
						right: ''
				    },
				    timezone: OpalHotel.timezone_string,
				    height: 400,
				    // defaultDate: '2016-05-12',
				    editable: false,
				    eventLimit: false, // allow "more" link when too many events
				    events: function( start, end, timezone, callback ){
				    	$.ajax({
				            url: OpalHotel.ajaxurl,
				            data: {
				                // our hypothetical feed requires UNIX timestamps
				                start: start.unix(),
				                end: end.unix(),
				                action: 'opalhotel_load_pricing',
				                room_id: room_id
				            },
				            success: function(doc) {
				            	callback(doc);
				            }
				        });
					},
				});
			},

			update_pricing: function( e ) {
				e.preventDefault();
				// e.stopPropagation();
				var _self = $( this ),
					_parent = $( '.opalhotel_datepick_wrap.update_pricing' ),
					_data = $( '.opalhotel_datepick_wrap.update_pricing input, .opalhotel_datepick_wrap.update_pricing select' ).serializeArray();
				var data = {};
				for( var i = 0; i < Object.keys( _data ).length; i++ ){
					var _ob = _data[i];
					if ( data.hasOwnProperty( _ob.name ) ) {
						data[ _ob.name ] = $.makeArray( data[ _ob.name ] );
						data[ _ob.name ].push( _ob.value );
					} else {
						data[ _ob.name ]	= _ob.value;
					}
				}
				var arrival = _parent.find( 'input[name="pricing_arrival"]' ); // console.debug( arrival );
				arrival = arrival.datepicker( 'getDate' );
				arrival = new Date( arrival ).getTime() / 1000;

				var departure = _parent.find( 'input[name="pricing_departure"]' );
				departure = departure.datepicker( 'getDate' );
				departure = new Date( departure ).getTime() / 1000;

				if ( ! arrival || ! departure ) {
					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.datepicker_invalid
		                }
		            });
		            return false;
				}

				if ( _parent.find( 'input[name="amount"]' ).val() == '' ) {
					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.enter_amount
		                }
		            });
		            return false;
				}

				if ( _parent.find( 'input[type="checkbox"]:checked' ).length === 0 ) {
					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.select_date_week
		                }
		            });
		            return false;
				}

				data.action = 'opalhotel_update_pricing';
				data.room_id = $( '#post_ID' ).val();

				$.ajax({
					url: OpalHotel.ajaxurl,
					type: 'POST',
					data: data,
					beforeSend: function(){
						_self.append( '<i class="fa fa-spin fa-spinner"></i>' );
					},
				}).always(function(){
					_self.find( '.fa' ).remove();
				}).done( function( res ){
					if ( res.status === false && typeof res.message !== 'undefined' ) {
						_self.opalhotel_modal({
			                tmpl: 'opalhotel-confirm',
			                settings: {
			                	message: res.message
			                }
			            });
					} else {
						$( '#opalhotel-room-pricing .calendar' ).fullCalendar( 'gotoDate', res.go_to_date );
						$( '#opalhotel-room-pricing .calendar' ).fullCalendar( 'refetchEvents' );
						$( 'input[name^="week_days"]' ).attr( 'checked', false );
						$( 'input[name="amount"]' ).val('');
						$( '.opalhotel-has-datepicker' ).datepicker( 'setDate', null );
						$( 'select[name="price_type"] option' ).removeAttr( 'selected' );
					}
				} ).fail( function() {
					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.something_wrong
		                }
		            });
				});

				return false;
			},

			/* pricing filter */
			filter_pricing: function( e ) {
				e.preventDefault();
				var _self = $( this ),
					_parent = _self.parents( '.filter_pricing:first' ),
					_month = _parent.find( 'select[name="month"]' ).val(),
					_year = _parent.find( 'select[name="year"]' ).val(),
					_room_id = $( '#post_ID' ).val();

				if ( typeof _room_id === 'undefined' || ! _room_id ) {
					return false;
				}

				var month_number = _month;
				if ( parseInt( _month ) < 10 ) {
					month_number = '0' + month_number;
				}
				month_number = _year + '-' + month_number + '-01';
				$( '#opalhotel-room-pricing .calendar' ).fullCalendar( 'gotoDate', month_number );
				$( '#opalhotel-room-pricing .calendar' ).fullCalendar( 'refetchEvents' );

				return false;
			},

			booking: {

				init: function() {

					var _doc = $( document );

					_doc.on( 'change', '.opalhotel-status-action', this.change_status );
					_doc.on( 'click', '.opalhotel-edit-customer', this.edit_customer );

					this.select_customer_email();
					this.select_customer_id();
					this.select_coupon();

					_doc.on( 'click', '.remove_order_item', this.remove_order_item );
					_doc.on( 'opalhotel_submited_action', this.submit_modal );
					_doc.on( 'click', '.add_coupon', this.add_coupon );
					_doc.on( 'click', '.remove_coupon', this.remove_coupon );
					_doc.on( 'click', '.add_line_item', this.add_line_item );
					_doc.on( 'opalhotel_modal_open', this.add_line_item_modal_callback );
					_doc.on( 'click', '.order_check_avaibility', this.order_check_avaibility );
					_doc.on( 'click', '.edit_order_item', this.edit_order_item );
				},

				change_status: function () {
					var _self = $( this ),
						_text = _self.find( 'option:selected' ).attr( 'data-desc' ),
						_desc = $( '.opalhotel-status-desc' );

					_desc.slideUp( 400, function(){
						_desc.html( _text ).slideDown();
					} );
				},

				edit_customer: function() {
					var _self = $( this ),
						_section = _self.parents( '#opalhotel-customer-data' ),
						_input = _section.find( 'input, select, textarea' );

					_self.toggleClass( 'active' );

					if ( _self.hasClass( 'active' ) ) {
						_input.removeAttr( 'disabled' );
					} else {
						_input.attr( 'disabled', true );
					}

					return false;
				},

				select_customer_email: function () {
					var _select = $( '#_customer_email_select' ),
						_placeholder = _select.attr( 'data-placeholder' );
					_select.select2({
		                minimumInputLength: 3,
		                allowClear:  true,
						placeholder: _placeholder,
						formatInputTooShort: OpalHotel.select,
		                ajax: {
		                    url: ajaxurl,
		                    dataType: 'json',
		                    type: 'POST',
		                    quietMillis: 50,
		                    data: function ( email ) {
		                        return {
		                            email: email.term,
		                            action: 'opalhotel_load_order_user_email',
		                            nonce: OpalHotel.load_customer_by_email_nonce
		                        };
		                    },
		                    processResults: function ( data ) {
		                        return {
		                            results: $.map( data, function ( item ) {
		                                return {
		                                    text: item.meta_value,
		                                    id: item.meta_value
		                                }
		                            })
		                        };
		                    },
		                    cache: true
		                }
		            });
				},

				/* customer id */
				select_customer_id: function() {
					var _select = $( '#_customer_id' ),
						_placeholder = _select.attr( 'data-placeholder' );
					_select.select2({
						formatInputTooShort: OpalHotel.select,
		                minimumInputLength: 3,
		                allowClear:  true,
						placeholder: _placeholder,
		                ajax: {
		                    url: ajaxurl,
		                    dataType: 'json',
		                    type: 'POST',
		                    quietMillis: 50,
		                    data: function ( name ) {
		                        return {
		                            name: name.term,
		                            action: 'opalhotel_load_order_user_name',
		                            nonce: OpalHotel.load_customer_by_user_name_nonce
		                        };
		                    },
		                    processResults: function ( data ) {
		                        return {
		                            results: $.map( data, function ( item ) {
		                                return {
		                                    text: '(#' + item.ID + ') ' + item.user_email,
		                                    id: item.ID
		                                }
		                            })
		                        };
		                    },
		                    cache: true
		                }
		            });
				},

				/* coupon */
				select_coupon: function() {
					var _select = $( '#_coupon_code' ),
						_placeholder = _select.attr( 'data-placeholder' );
					_select.select2({
						formatInputTooShort: OpalHotel.select,
		                minimumInputLength: 3,
		                allowClear:  true,
						placeholder: _placeholder,
		                ajax: {
		                    url: ajaxurl,
		                    dataType: 'json',
		                    type: 'POST',
		                    quietMillis: 50,
		                    data: function ( code ) {
		                        return {
		                            code: code.term,
		                            action: 'opalhotel_load_coupon_available',
		                            order_id: _select.attr( 'data-order-id' ),
		                            nonce: OpalHotel.load_coupon_available_nonce
		                        };
		                    },
		                    processResults: function ( data ) {
		                        return {
		                            results: $.map( data, function ( item ) {
		                                return {
		                                    text: item.post_title,
		                                    id: item.ID
		                                }
		                            })
		                        };
		                    },
		                    cache: true
		                }
		            });
				},

				/* add coupon */
				add_coupon: function ( e ) {
					e.preventDefault();
					var _self = $( this ),
						input = $( '#_coupon_code' ),
						id = input.val(),
						order_id = _self.attr( 'data-order-id' );
					if ( typeof id === 'undefined' || ! id ) {
						/* alert error message */
						_self.opalhotel_modal({
			                tmpl: 'opalhotel-confirm',
			                settings: {
			                	message: OpalHotel.coupon_code_empty
			                }
			            });
					} else {
						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'opalhotel_order_add_coupon',
								id: id,
								order_id: order_id,
								nonce: OpalHotel.add_coupon_nonce
							},
							dataType: 'json',
							beforeSend: function() {
								OpalHotelAdmin.metaboxes.booking.beforeSend()
							}
						}).always(function(){
							OpalHotelAdmin.metaboxes.booking.afterSend()
						}).done( function( res ) {
							if ( res.status === true && typeof res.html !== 'undefined' ) {
								$('.opalhotel_order_items').replaceWith( res.html );
							}

							if ( res.status === false && typeof res.message ) {
								/* alert error message */
								_self.opalhotel_modal({
					                tmpl: 'opalhotel-confirm',
					                settings: {
					                	message: res.message
					                }
					            });
							}
						}).fail( function(){
							/* alert error message */
							_self.opalhotel_modal({
				                tmpl: 'opalhotel-confirm',
				                settings: {
				                	message: OpalHotel.something_wrong
				                }
				            });
						});
					}
				},

				/* remove coupon */
				remove_coupon: function( e ) {
					e.preventDefault();
					var _self = $( this ),
						_order_id = _self.attr( 'data-order-id' );

					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.remove_coupon_message,
		                	action: 'opalhotel_order_remove_coupon',
		                	order_id: _order_id,
		                	nonce: OpalHotel.remove_coupon_nonce
		                }
		            });
				},

				/* remove order item */
				remove_order_item: function( e ) {
					e.preventDefault();
					var _self = $( this ),
						_order_id = _self.attr( 'data-order-id' ),
						_order_item_id = _self.attr( 'data-id' );

					_self.opalhotel_modal({
		                tmpl: 'opalhotel-confirm',
		                settings: {
		                	message: OpalHotel.remove_item_message,
		                	action: 'opalhotel_remove_order_item',
		                	order_item_id: _order_item_id,
		                	order_id: _order_id,
		                	nonce: OpalHotel.remove_order_item_nonce
		                }
		            });
				},

				/* datepicker avaiable form */
				add_line_item_modal_callback: function( e, target, form ) {
					e.preventDefault();
					if ( target === 'opalhotel-template-order-item' ) {
						var arrival = $( '#container input[name="arrival"]' ),
							departure = $( '#container input[name="departure"]' ),
							form = $( '#opalhotel_backbone_modal form:first' ),
							room_select = form.find( 'select[name="room_id"]' );

						arrival.datepicker({
							closeText       : OpalHotel.datepicker.closeText,
		                    currentText     : OpalHotel.datepicker.currentText,
		                    monthNames      : OpalHotel.datepicker.monthNames,
		                    monthNamesShort : OpalHotel.datepicker.monthNamesShort,
		                    dayNames        : OpalHotel.datepicker.dayNames,
		                    dayNamesShort   : OpalHotel.datepicker.dayNamesShort,
		                    dayNamesMin     : OpalHotel.datepicker.dayNamesMin,
		                    dateFormat      : OpalHotel.datepicker.dateFormat,
		                    firstDay        : OpalHotel.datepicker.firstDay,
		                    isRTL           : OpalHotel.datepicker.isRTL,
		                    onSelect		: function( date ) {
		                    	var _self = $( this ),
		                    		_date = _self.datepicker( 'getDate' );
		                    	departure.datepicker( 'option', 'minDate', _date );
		                    }
						});

						departure.datepicker({
							closeText       : OpalHotel.datepicker.closeText,
		                    currentText     : OpalHotel.datepicker.currentText,
		                    monthNames      : OpalHotel.datepicker.monthNames,
		                    monthNamesShort : OpalHotel.datepicker.monthNamesShort,
		                    dayNames        : OpalHotel.datepicker.dayNames,
		                    dayNamesShort   : OpalHotel.datepicker.dayNamesShort,
		                    dayNamesMin     : OpalHotel.datepicker.dayNamesMin,
		                    dateFormat      : OpalHotel.datepicker.dateFormat,
		                    firstDay        : OpalHotel.datepicker.firstDay,
		                    isRTL           : OpalHotel.datepicker.isRTL,
		                    onSelect		: function( date ) {
		                    	var _self = $( this ),
		                    		_date = _self.datepicker( 'getDate' );
		                    	arrival.datepicker( 'option', 'maxDate', _date );
		                    }
						});

						room_select.select2({
							placeholder: room_select.attr( 'data-placeholder' ),
		                    formatInputTooShort: OpalHotel.select,
		                    minimumInputLength: 3,
		                    ajax: {
		                        url: OpalHotel.ajaxurl,
		                        dataType: 'json',
		                        type: 'POST',
		                        quietMillis: 50,
		                        data: function ( name ) {
		                            return {
		                                name: name.term,
		                                action: 'opalhotel_load_room_by_name',
		                                nonce: OpalHotel.load_room_by_name,
		                                order_id: $( '#post_ID' ).val()
		                            };
		                        },
		                        processResults: function ( data ) {
		                            return {
		                                results: $.map( data, function ( item ) {
		                                    return {
		                                        text    : item.post_title,
		                                        id      : item.ID
		                                    }
		                                })
		                            };
		                        },
		                        cache: true
		                    }
						});

					}
				},

				/* add line item */
				add_line_item: function( e ) {
					e.preventDefault();
					var _self = $( this ),
						_order_id = _self.attr( 'data-order-id' );

					_self.opalhotel_modal({
		                tmpl: 'opalhotel-template-order-item',
		                settings: {
		                	action: 'opalhotel_admin_add_order_item',
		                	// message: OpalHotel.add_line_item_message,
		                	nonce: OpalHotel.add_order_item,
		                	order_id: $( '#post_ID' ).val()
		                }
		            });

				},

				/* order check available */
				order_check_avaibility: function( e ) {
					e.preventDefault();
					var _self = $( this ),
						_parent = _self.parents( 'form:first' ),
						_data = _parent.serializeArray();
						_data.push({
							name: 'action',
							value: 'opalhotel_admin_check_available'
						});

					var arrival = _parent.find( 'input[name="arrival"]' ).datepicker( 'getDate' );
					if ( arrival ) {
						arrival = opalhotel_format_date( new Date( arrival ) );//new Date( arrival ).getTime() / 1000;
						_data.push({
							name: 'arrival',
							value: arrival
						});
					}

					var departure = _parent.find( 'input[name="departure"]' ).datepicker( 'getDate' );
					if ( departure ) {
						departure = opalhotel_format_date( new Date( departure ) );//new Date( departure ).getTime() / 1000;
						_data.push({
							name: 'departure',
							value: departure
						});
					}

					_data.push({
						name: 'nonce',
						value: OpalHotel.load_available_room_nonce
					});

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: _data,
						beforeSend: function(){
							_parent.find( '.qty' ).remove();
							_self.append( '<i class="fa fa-spinner fa-spin"></i>' );
							_parent.find( 'header h2' ).find( 'select:not(.select2-hidden-accessible)' ).remove();
						}
					}).always( function() {
						_self.find( '.fa' ).remove();
					}).done( function( res ) {
						if ( typeof res.status === 'undefined' ) {
							/* alert error message */
							alert( OpalHotel.something_wrong ); return;
						}
						if ( res.status === false ) {
							alert( res.message ); return;
						}

						if ( typeof res.html !== 'undefined' ) {
							_parent.find( 'header h2' ).append( res.html );
						}
						if ( res.status === true ) {
							_parent.find( '.qty' ).on( 'change', function( e ){
								e.preventDefault();
								if ( $( this ).val() !== '' ) {
									_parent.find( '.opalhotel-button-submit' ).removeAttr( 'disabled' );
								} else {
									_parent.find( '.opalhotel-button-submit' ).attr( 'disabled', true );
								}
							} );
						}

					});
				},

				/* edit order item */
				edit_order_item: function( e ) {
					e.preventDefault();
					var _self = $( this ),
						_order_id = _self.attr( 'data-order-id' ),
						_order_item_id = _self.attr( 'data-id' );

					$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
			                	action: 'opalhotel_admin_edit_order_item',
			                	nonce: OpalHotel.edit_order_item,
			                	order_id: _order_id,
			                	order_item_id: _order_item_id
			                },
							dataType: 'json',
							beforeSend: function() {
								OpalHotelAdmin.metaboxes.booking.beforeSend();
							}
						}).always(function(){
							OpalHotelAdmin.metaboxes.booking.afterSend();
						}).done( function( res ) {
							if ( res.status === true ) {
								_self.opalhotel_modal({
					                tmpl: 'opalhotel-template-order-item',
					                settings: res
					            });
					            $( document ).on( 'change', '#opalhotel_backbone_modal :input,#opalhotel_backbone_modal select', function( e ){
									e.preventDefault();
									if ( $( this ).val() !== '' ) {
										$( '.opalhotel-button-submit' ).removeAttr( 'disabled' );
									} else {
										$( '.opalhotel-button-submit' ).attr( 'disabled', true );
									}
								} );
							} else if ( res.status === false && typeof res.message !== 'undefined' ) {
								_self.opalhotel_modal({
					                tmpl: 'opalhotel-confirm',
					                settings: {
					                	message: res.message
					                }
					            });
							}
						}).fail( function(){
							/* alert error message */
							_self.opalhotel_modal({
				                tmpl: 'opalhotel-confirm',
				                settings: {
				                	message: OpalHotel.something_wrong
				                }
				            });
						});
					return false;
				},

				/* submit modal form */
				submit_modal: function( e, target, data ) {
					e.preventDefault();
					if ( typeof data.action !== 'undefined' && ( target === 'opalhotel-confirm' || target === 'opalhotel-template-order-item' ) ) {
						var _body = $( 'body' );
						var arrival = data.arrival;
						if ( arrival ) {
							arrival = opalhotel_format_date( new Date( arrival ) );//new Date( arrival ).getTime() / 1000;
							data.arrival = arrival;
						}
						var departure = data.departure;
						if ( departure ) {
							departure = opalhotel_format_date( new Date( departure ) );//new Date( arrival ).getTime() / 1000;
							data.departure = departure;
						}
						if ( data.action === 'opalhotel_remove_order_item' || data.action === 'opalhotel_order_remove_coupon' ) {
							$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: data,
								dataType: 'html',
								beforeSend: function() {
									OpalHotelAdmin.metaboxes.booking.beforeSend();
								}
							}).always(function(){
								OpalHotelAdmin.metaboxes.booking.afterSend();
							}).done( function( html ) {
								$('.opalhotel_order_items').replaceWith( html );
								OpalHotelAdmin.metaboxes.booking.select_coupon();
							}).fail( function(){
								/* alert error message */
								_body.opalhotel_modal({
					                tmpl: 'opalhotel-confirm',
					                settings: {
					                	message: OpalHotel.something_wrong
					                }
					            });
							});
						}

						if ( data.action === 'opalhotel_admin_add_order_item' || data.action === 'opalhotel_admin_update_order_item' ) {
							$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: data,
								dataType: 'json',
								beforeSend: function() {
									OpalHotelAdmin.metaboxes.booking.beforeSend();
								}
							}).always(function(){
								OpalHotelAdmin.metaboxes.booking.afterSend();
							}).done( function( res ) {
								if ( typeof res.html !== 'undefined' && res.status === true ) {
									$('.opalhotel_order_items').replaceWith( res.html );
									OpalHotelAdmin.metaboxes.booking.select_coupon();
								}

								if ( res.status === false && typeof res.message !== 'undefined' ) {
									/* alert error message */
									_body.opalhotel_modal({
						                tmpl: 'opalhotel-confirm',
						                settings: {
						                	message: res.message
						                }
						            });
								}
							}).fail( function(){
								/* alert error message */
								_body.opalhotel_modal({
					                tmpl: 'opalhotel-confirm',
					                settings: {
					                	message: OpalHotel.something_wrong
					                }
					            });
							});
						}

					}
				},

				beforeSend: function() {
					$( '#opalhotel-booking-item .inside' ).append( '<div class="opalhotel_booking-items-overlay"></div>' );
				},

				afterSend: function() {
					$( '.opalhotel_booking-items-overlay' ).remove();
				},

			},

		},

		/* galleries media popup */
		galleries: function ( target ){
			var fileFrame = wp.media.frames.file_frame = wp.media({
				multiple: true
			});
			var _doc = $( document ),
				attachments = false;
			fileFrame.on( 'select', function() {
				attachments = fileFrame.state().get( 'selection' ).toJSON();

				/* allow hook js like javascript event */
				_doc.triggerHandler( 'opalhotel_gallery_selected', [ target, attachments ] );
			});
			fileFrame.open();

			/* return atachments */
			return attachments;
		},

	};

	$( document ).ready( function(){
		/* init ready function */
		OpalHotelAdmin.init();
	} );

	$( document ).on( 'change', '#opalhotel_comment_layout', function(e) {
		e.preventDefault();
		var value = $( this ).val();
		var tr = $('#opalhotel_comment_rating').parents('tr:first');

		if ( value ) {
			tr.removeClass( 'hide-if-js' );
		} else {
			tr.addClass( 'hide-if-js' );
		}
		return false;
	} );

	// add media
	$( document ).on( 'click', '.add-media', function( e ){
		e.preventDefault();

		var _this = $( this );
		var fileFrame = wp.media.frames.file_frame = wp.media({
            multiple: false
        });
        var _doc = $( document ),
            attachments = false;
        fileFrame.on( 'select', function() {
            attachments = fileFrame.state().get( 'selection' ).toJSON();
            var thumbnail = attachments[0],
                id = thumbnail.id,
                url = thumbnail.url,
                imageWrapper = _this.parents( '.term-thumbnail' ).find( '.media-thumbnail' ),
                input = _this.parents( '.term-thumbnail' ).find( '._thumbnail_id' );

            imageWrapper.html( '<img src="' + url + '" width="100" height="100" />' );
            input.val( id );
        });
        fileFrame.open();

		return false;
	} );

})(jQuery);