/*
* @Author: someone
* @Date:   2016-05-07 16:35:24
* @Last Modified by:   someone
* @Last Modified time: 2016-05-10 23:00:49
*/

'use strict';
// oaplhotel modal box
( function( $, Backbone, _ ){

    $.fn.opalhotel_modal = function( options ){

        var options = $.extend( {}, {
                tmpl        : '',
                settings    : {}
            }, options );

        if ( options.tmpl ) {
            new OpalHotelModal.view( options.tmpl, options.settings );
        }
    };

    var OpalHotelModal = {

        view: function( target, options ){
            var view = Backbone.View.extend({

                id      : 'opalhotel_backbone_modal',
                options : options,
                target  : target,

                // events handles
                events  : {
                    'click .opalhotel_button_close'     		: 'close',
                    'click .opalhotel_backbone_modal_overflow'  : 'close',
                    'click .opalhotel-button-submit'     		: 'submit'
                },

                // initialize
                initialize: function( data ){
                    this.render();
                },

                // render
                render: function(){
                    var template = wp.template( this.target );

                    template = template( this.options );

                    $( 'body' ).append( this.$el.html( template ) );

                    var _content = $( '.opalhotel_backbone_modal_content' ),
                        _content_width = _content.outerWidth(),
                        _content_height = _content.outerHeight();

                    _content.css({
                        'margin-top'    : '-' + _content_height / 2 + 'px',
                        'margin-left'   : '-' + _content_width / 2 + 'px'
                    });

                    $( document ).trigger( 'opalhotel_modal_open', [ this.target, _content.find( 'form' ) ] );
                },

                // submit
                submit: function(){
                    $( document ).trigger( 'opalhotel_submit_action', [ this.target, this.form_data() ] );

                    // close
                    this.close();

                    $( document ).trigger( 'opalhotel_submited_action', [ this.target, this.form_data() ] );

                    return false;
                },

                // close
                close: function() {

                    $( document ).trigger( 'opalhotel_close', [ this.target, this.form_data() ] );

                    this.$el.remove();

                    return false;
                },

                // form data
                form_data: function(){
                    var _data = $( this.$el ).find( 'form:first-child' ).serializeArray(),
                        data = {};
                    for( var i = 0; i < Object.keys( _data ).length; i++ ){
                        var _ob = _data[i];
                        if ( data.hasOwnProperty( _ob.name ) ) {
                            data[ _ob.name ] = $.makeArray( data[ _ob.name ] );
                            data[ _ob.name ].push( _ob.value );
                        } else {
                            data[ _ob.name ]    = _ob.value;
                        }
                    }
                    return data;
                }

            });

            return new view( options );
        },

    };

} )( jQuery, Backbone, _ );
// oaplhotel modal box

(function($){

    window.opalhotel_datepicker_init = function (){
        var inputs = $( '.opalhotel-has-datepicker' );
        var _calendar = $( '#opalhotel_check_availability' );
        /* date */
        var today = new Date();
        var tomorrow = new Date();

        /* allow hook set min date check out date */
        var start_plus = $( document ).triggerHandler( 'opalhotel_min_arrival_date', [ 1, today, tomorrow ] );
        if ( typeof start_plus === 'undefined' || ! Number( start_plus ) || ( start_plus % 1 !== 0 ) ) { // valid integer
            start_plus = 0;
        }
        tomorrow.setDate( today.getDate() + start_plus );
        /* each input datepicker */
        for ( var i = 0; i < inputs.length; i++ ) {
            var input = $( inputs[i] );

            var options = {
                    // showButtonPanel : true,
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
                };

            /* set min date */
            var mindate = input.attr( 'data-min-date' );
            if ( typeof mindate === 'undefined' || mindate == 'true' ) {
                options.minDate = tomorrow;
            }
            options.onSelect = function( date ){
                var _self = $( this ),
                    name = _self.attr( 'name' ),
                    _date = _self.datepicker( 'getDate' ),
                    end = _self.attr( 'data-end' ),
                    start = _self.attr( 'data-start' ),
                    wrap = _self.parents( '.opalhotel_datepick_wrap:first' );
                    var dateFormat = opalhotel_format_date( new Date( date ) );

                _self.parent().find( 'input[name="' + name + '_datetime"]' ).remove();
                _self.parent().append( '<input type="hidden" name="' + name + '_datetime" value="' + opalhotel_format_date( _date ) + '" />' );

                var range = $( document ).triggerHandler( 'opalhotel_range_check_in_check_out', [ 1, today, tomorrow ] );

                if ( ! Number( range ) || ( range % 1 !== 0 ) ) { // valid integer
                    range = 1;
                }

                if ( typeof wrap !== 'undefined' ) {
                    if ( typeof end !== 'undefined' ) {
                        if ( _date ) {
                            _date.setDate( _date.getDate() + range );
                        }
                        var _end = wrap.find( '.' + end );
                        _end.datepicker( 'option', 'minDate', _date );
                        var end_date = _end.datepicker( 'getDate' );

                        if ( end_date ) {
                            var end_name = _end.attr( 'name' );
                            var dateFormat = opalhotel_format_date( new Date( end_date ) );
                            _end.parent().find( 'input[name="' + end_name + '_datetime"]' ).remove();
                            _end.parent().append( '<input type="hidden" name="' + end_name + '_datetime" value="' + dateFormat + '" />' );
                        }
                    }
                    if ( typeof start !== 'undefined' ) {
                        if ( _date ) {
                            _date.setDate( _date.getDate() - range );

                            var _start = wrap.find( '.' + start );
                            _start = _start.datepicker( 'getDate' );
                            _start = new Date( _start ).getTime();
                            var count_day = ( new Date( _date ).getTime() - _start ) / ( 24 * 60 * 60 * 1000 );

                            if ( count_day > 1 ) {
                                wrap.find( '.' + start ).datepicker( 'option', 'maxDate', _date );
                            }
                        }
                    }
                    $('td.ui-datepicker-current-day a.ui-state-default').removeClass('ui-state-active');
                }
                _calendar.datepicker( 'refresh' );

            };

            options.beforeShow = function(){
                $( '#ui-datepicker-div' ).addClass( 'opalhotel-datpicker' );
            };

            input.datepicker( options );
        }
    };

    window.opalhotel_format_date = function( date ) {
        var yyyy = date.getFullYear();
        var mm = date.getMonth() < 9 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1); // getMonth() is zero-based
        var dd  = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
        return yyyy + '-' + mm + '-' + dd;
    }

    // init jBox modal
    window.opalhotel_jBox_init = function () {
        var modalbuttons = $( '.opalhotel-modal-button' );
        for ( var i = 0; i < modalbuttons.length; i++ ) {
            var button = $( modalbuttons[i] );
            var options = {
                attach: '#' + button.attr( 'id' ),
                width: typeof button.data( 'width' ) !== 'undefined' ? button.data( 'width' ) : $( document ).width() / 2,
                height: typeof button.data( 'height' ) !== 'undefined' ? button.data( 'height' ) : $(window).height() - 260,
                content: $( '#' + button.data( 'id' ) ),
                closeButton: 'title',
                title: '<h3 class="modal-title">' + button.data( 'title' ) + '</h3>',
                reposition: true,
                repositionOnOpen: true,
                audio: OpalHotel.assetsURI + 'libraries/jBox/audio/beep3',
                onOpen: function() {
                    this.source.removeClass( 'closed' ).addClass( 'opening' );
                    this.wrapper.addClass( 'opalhotel-jBox' );
                },
                onClose: function() {
                    this.source.removeClass( 'opening' ).addClass( 'closed' );
                    this.wrapper.removeClass( 'opalhotel-jBox' );
                }
            };
            new jBox('Modal', options);
        }
    }

    // init owlCarousel
    window.opalhotel_owlCarousel_init = function() {
        if ( $.fn.owlCarousel !== 'undefined' ) {
            var carousels = $('.opalhotel-owl-carousel');
            for ( var i = 0; i < carousels.length; i++ ) {
                var carousel = $( carousels[i] );
                var configs = {};
                if ( carousel.data( 'navigation' ) === true ) {
                    configs.navigation = true;
                }
                if ( carousel.data( 'pagination' ) === true ) {
                    configs.pagination = true;
                }
                if ( carousel.data( 'loop' ) === true ) {
                    configs.loop = true;
                }
                var speed = carousel.data( 'speed' );
                configs.slideSpeed = configs.paginationSpeed = 600;
                if ( speed ) {
                    configs.slideSpeed = speed;
                    configs.paginationSpeed = speed;
                }

                var items = parseInt( carousel.data( 'items' ) );
                if ( items ) {
                    if ( items == 1 ) {
                        configs.singleItem = true;
                    } else if ( items > 1 ) {
                        configs.items = items;

                        var itemDesktop = parseInt( carousel.data( 'desktop' ) );
                        if ( itemDesktop ) {
                            configs.itemsDesktop = itemDesktop;
                        }
                        var itemsDesktopSmall = parseInt( carousel.data( 'desktopsmall' ) );
                        if ( itemsDesktopSmall ) {
                            configs.itemsDesktopSmall = itemsDesktopSmall;
                        }
                        var itemsTablet = parseInt( carousel.data( 'tablet' ) );
                        if ( itemsTablet ) {
                            configs.itemsTablet = itemsTablet;
                        }
                        var itemsMobile = parseInt( carousel.data( 'mobile' ) );
                        if ( itemsMobile ) {
                            configs.itemsMobile = itemsMobile;
                        }

                    }
                }
                carousel.owlCarousel( configs );

                var mousewheel = carousel.data('mousewheel');
                if ( mousewheel === true ) {
                    carousel.on( 'mousewheel', '.owl-wrapper-outer', function( e ) {
                        e.preventDefault();
                        if ( e.deltaY > 0 ) {
                          carousel.trigger('owl.next');
                        } else {
                          carousel.trigger('owl.prev');
                        }
                    });
                }
            }
        }
    }

    $(document).on( 'click', '.opalhotel-tabs .tabs a', function(e){
        e.preventDefault();
        var _wrapper = $(this).parents( '.opalhotel-tabs:first' ),
            _panel = _wrapper.find( '.panel' ),
            _this = $( this ),
            _target = _this.attr( 'href' );

            _this.parents( 'li:first' ).addClass( 'active' ).siblings().removeClass( 'active' );
            _this.parents( '.tabs:first' ).find( 'li' ).removeClass( 'active' );
            _this.parent().addClass( 'active' );

            _panel.removeClass( 'fade' ).removeClass( 'in' );
            $( _target ).addClass( 'fade in' );
            var setTimeout = window.setTimeout( function() {
                if ( _target === '#video-preview' && typeof YT !== 'undefined' ) {
                    var player = YT.get( $(_target).find('.opalhotel-feature-video').attr('id') );
                    player.playVideo();
                } else {
                    var player = YT.get( $('#video-preview').find('.opalhotel-feature-video').attr('id') );
                    player.pauseVideo();
                }
                $( _target ).addClass( 'in' );
                clearTimeout( setTimeout );
            }, 100 );
        return false;
    } );

    $(document).on( 'click', '.opalhotel-hotel-actions .view-gallery', function( e ){
        e.preventDefault();
        if ( typeof $.fn.prettyPhoto !== 'undefined' ) {
            var area = $( this ).parent().find( 'area' );
            var images = [];
            for ( var i = 0; i < area.length; i++ ) {
                images.push( $( area[i] ).attr( 'href' ) );
            }
            $.prettyPhoto.open(images);
        }
        return false;
    } );

    $( document ).ready(function(){
        if ( typeof $.fn.prettyPhoto !== 'undefined' ) {
            $("[rel^='prettyPhoto']").prettyPhoto();
        }
        // datepicker init
        opalhotel_datepicker_init();

        // jBox Modal init
        opalhotel_jBox_init();

        var shares = $( '.opalhotel-hotel-info .opalhotel-share' );
        for ( var i = 0; i < shares.length; i++ ) {
            var share = $( shares[i] );
            new jBox('Modal', {
                    attach: '#' + share.attr( 'id' ),
                    width: 300,
                    height: 100,
                    content: $( '#' + share.data( 'id' ) ),
                    closeButton: 'title',
                    title: '<h3 class="modal-title">' + share.data( 'title' ) + '</h3>',
                    overlay: true,
                    reposition: true,
                    repositionOnOpen: true
            });
        }
    });

    $( document ).on( 'click', '.opalhotel-fb-share', function( e ){
        e.preventDefault();
        var url = $( this ).attr( 'href' );
        var fb_url = 'https://www.facebook.com/sharer/sharer.php?u='+ url;
        window.open( fb_url, "FaceBook", "width=600, height=400, scrollbars=no" );
        return false;
    } );

    $( document ).on( 'click', '.opalhotel-google-share', function( e ){
        e.preventDefault();
        var url = $( this ).attr( 'href' );
        var google_url='https://plus.google.com/share?url='+ url;
        window.open( google_url, 'GooglePlus', 'width=600, height=400' );
        return false;
    } );

    $( document ).on( 'click', '.opalhotel-twitter-share', function( e ){
        e.preventDefault();
        var url = $( this ).attr( 'href' ),
            image = $( this ).attr( 'data-image' ),
            share_text = $( this ).attr( 'data-text' ),
            url = "https://twitter.com/intent/tweet?url="+ url+"&text="+ share_text;
        window.open( url, 'Twitter', 'width=600,height=400' );
        return false;
    } );

    $( document ).on( 'click', '.opalhotel-pinterest-share', function( e ){
        e.preventDefault();
        var url = $( this ).attr( 'href' ),
            image = $( this ).attr( 'data-image' ),
            share_text = $( this ).attr( 'data-text' );
        var pin_url='http://pinterest.com/pin/create/button/?url='+ url+'&media='+ image+'&description='+ share_text;
        window.open( pin_url, 'Pinterest', 'width=600, height=400' );
        return false;
    } );

    // 
    window.opalhotel_print_notice = function opalhotel_print_notice( message, type ) {
        if ( ! message ) message = OpalHotel.label.geoLocationError;

        if ( typeof jBox == 'undefined' ) return;
        if ( typeof type == 'undefined' ) type = 'error';

        // print jBox Notice
        new jBox( 'Notice', {
            animation: 'pulse',
            color: type,
            content: message,
            autoClose: 2000,
            audio: OpalHotel.assetsURI + 'libraries/jBox/audio/beep2',
            attributes: {
                x: 'left',
                y: 'bottom'
            },
            onOpen: function() {
                this.wrapper.removeClass( 'closed' ).addClass( 'opening' ).addClass( 'opalhotel-jBox' );
            },
            onClose: function() {
                this.wrapper.removeClass( 'opening' ).addClass( 'closed' ).addClass( 'opalhotel-jBox' );
            }
        } );
    }

})(jQuery);