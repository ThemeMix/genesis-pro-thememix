/**
 * Font Awesome Picker
 *
 * Based on: https://github.com/bradvin/dashicons-picker/
 */

( function ( $ ) {

	/**
	 *
	 * @returns {void}
	 */
	$.fn.fontawesomePicker = function () {

		var icons = all_font_awesome_icons;

		return this.each( function () {

			var button = $( this );

			button.on( 'click.fontawesomePicker', function () {
				createPopup( button );
			} );

			function createPopup( button ) {

				var target = $( button.data( 'target' ) ),
					popup  = $( '<div class="fontawesome-picker-container"> \
						<div class="fontawesome-picker-control" /> \
						<ul class="fontawesome-picker-list" /> \
					</div>' )
						.css( {
							'top':  button.offset().top,
							'left': button.offset().left
						} ),
					list = popup.find( '.fontawesome-picker-list' );

				for ( var i in icons ) {
					list.append( '<li data-icon="' + icons[i] + '"><a href="#" title="' + icons[i] + '"><span class="icon fa-' + icons[i] + '"></span></a></li>' );
				};

				$( 'a', list ).click( function ( e ) {
					e.preventDefault();
					var title = $( this ).attr( 'title' );
					target.val( title );

					var thefields = document.getElementsByClassName('font-awesome-picker');
					Object.keys(thefields).forEach(function(key) {
						var thefield = thefields[key];
						thefield.value = title;
					});

					removePopup();
				} );

				var control = popup.find( '.fontawesome-picker-control' );

				control.html( '<a data-direction="back" href="#"> \
					<span class="fontawesome-icons fontawesome-arrow-left-alt2"></span></a> \
					<input type="text" class="" placeholder="Search" /> \
					<a data-direction="forward" href="#"><span class="fontawesome-icons fontawesome-arrow-right-alt2"></span></a>'
				);

				$( 'a', control ).click( function ( e ) {
					e.preventDefault();
					if ( $( this ).data( 'direction' ) === 'back' ) {
						$( 'li:gt(' + ( icons.length - 26 ) + ')', list ).prependTo( list );
					} else {
						$( 'li:lt(25)', list ).appendTo( list );
					}
				} );

				popup.appendTo( 'body' ).show();

				$( 'input', control ).on( 'keyup', function ( e ) {
					var search = $( this ).val();
					if ( search === '' ) {
						$( 'li:lt(25)', list ).show();
					} else {
						$( 'li', list ).each( function () {
							if ( $( this ).data( 'icon' ).toLowerCase().indexOf( search.toLowerCase() ) !== -1 ) {
								$( this ).show();
							} else {
								$( this ).hide();
							}
						} );
					}
				} );

                $( 'input[type=text].fontawesome-picker' ).on( 'click', 'button.fontawesome-picker', function ( event ) {

//				$( document ).bind( 'mouseup.fontawesome-picker', function ( e ) {
//					if ( ! popup.is( e.target ) && popup.has( e.target ).length === 0 ) {
//						removePopup();
//					}
				} );
			}

			function removePopup() {
				$( '.fontawesome-picker-container' ).remove();
				$( document ).unbind( '.fontawesome-picker' );
			}
		} );
	};

	$( function () {
		$( '.fontawesome-picker' ).fontawesomePicker();
	} );

	$(document).on('widget-updated widget-added', function( event, $widget ){
    	$widget.find( '.fontawesome-picker' ).fontawesomePicker();
	});


}( jQuery ) );
