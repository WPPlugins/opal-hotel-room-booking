const $ = jQuery;

/**
 * Custom marker google map
 */
/** @constructor */
function CustomMarker( options ) {
	this.options = options;
    this.setValues( options );
    this.position = new google.maps.LatLng({ lat: options.lat, lng: options.lng });
    this.div = null;
}

CustomMarker.prototype = new google.maps.OverlayView();

CustomMarker.prototype.draw = function() {

    var self = this;

    var div = this.div;

    if ( ! div ) {

        var div = document.createElement('div');
        div.style.position = 'absolute';
        div.className = 'opalhotel-marker-icon';

        if( this.title ) {
            var title = 'map-marker-' + this.title;
            title = title.toLowerCase();
            title = title.replace(/ /g,'-');
            div.setAttribute('id', title);
        }

        var markerTemplate = wp.template( 'opalhotel-marker-icon' );
        markerTemplate = markerTemplate( self.options );
        $( div ).append( markerTemplate );

        this.div = div;

        var panes = this.getPanes();
        panes.overlayImage.appendChild( this.div );

        google.maps.event.addDomListener( this.div, 'click', function(event) {
            google.maps.event.trigger( self, 'click', event );
        });
    }

    var point = this.getProjection().fromLatLngToDivPixel( this.position );

    if ( point ) {
        this.div.style.left = ( point.x - $( this.div ).find( 'svg' ).width() / 2 ) + 'px';
        this.div.style.top = ( point.y - $( this.div ).find( 'svg' ).height() ) + 'px';
    }
};

CustomMarker.prototype.remove = function() {
    if (this.div) {
        this.div.parentNode.removeChild(this.div);
        this.div = null;
    }
};

CustomMarker.prototype.getPosition = function() {
    return this.position;
};

CustomMarker.prototype.getDraggable = function() {
    return false;
};

CustomMarker.prototype.getVisible = function () {
    return true;
};

export default CustomMarker;