(function($){

	var megamaxmenu_is_initialized = false;

	//jQuery( document ).ready( function($){
	jQuery(function($) {
		initialize_megamaxmenu( 'document.ready' );
	});

	//Backup
	$( window ).on( 'load' , function(){
		initialize_megamaxmenu( 'window.load' );
	});

	function initialize_megamaxmenu( init_point ){

		if( megamaxmenu_is_initialized ) return;

		megamaxmenu_is_initialized = true;

		if( console && init_point == 'window.load' ) console.log( 'Notice: MegaMaxMenu initialized via ' + init_point );

		//Manual code switcher
		$( '.megamaxmenu-manual-code-menu-selection' ).on( 'change' , function(){
			var $wrap = $( this ).closest( '.megamaxmenu-integration-code-wrap' );
			$wrap.find( '.megamaxmenu-integration-code' ).hide();
			$wrap.find( '.megamaxmenu-integration-code-'+$( this ).val() ).show();
		});


		//Color Gradients
		$('.megamaxmenu-color-stop').each( function(){
			var $colorstop = $(this);
			$colorstop.wpColorPicker({
				clear: _.throttle( function(){
					$colorstop.data( 'cleared' , true );
					//var hexcolor = $( this ).wpColorPicker( 'color' );
					//console.log( 'color = ' + $colorstop.wpColorPicker( 'color' ) );
					//$colorstop.wpColorPicker( 'color' , ' ' );
					//console.log( 'color = ' + $colorstop.wpColorPicker( 'color' ) );
					var $control = $(this).closest( 'td' );
					update_gradient_list( $control );
					//$colorstop.data( 'cleared' , false );

				}, 300 ),

				change: _.throttle( function( event , ui ){
					$colorstop.data( 'cleared' , false );
					//console.log( 'change ' + $(this).attr('class') );

					//var hexcolor = $( this ).wpColorPicker( 'color' );
					var $control = $(this).closest( 'td' );
					update_gradient_list( $control );

				}, 300 )
			});
		});


		$( '.megamax-sub-section-tab' ).on( 'click' , function(){
			var $panel = $(this).closest( 'form' );
			$( this ).siblings().removeClass( 'megamax-active' );
			$( this ).addClass( 'megamax-active' );
			var group = $(this).data( 'section-group' );
			if( group == '_all' ){
				$panel.find( '.megamax-settings-group' ).show();
			}
			else{
				$panel.find( '.megamax-settings-group' ).hide();
				$panel.find( '.megamax-settings-group-' + group ).show();
			}

			megamax_store( 'megamax_menu_settings_tab' , $panel.parent().attr( 'id' ) , group );
		});


		//Show tab on page load
		$( '.postbox .group' ).each( function(){
			var group = megamax_store( 'megamax_menu_settings_tab' , $(this).attr('id') );
			if( group  && group !== '_all'){
				$(this).find( '[data-section-group='+group+']' ).click();
			}
			else{
				$(this).find( '.megamax-sub-section-tab:first-child' ).click();
			}
		});



		$( '.megamaxmenu_instance_notice_close, .megamaxmenu_instance_close' ).on( 'click' , function(){
			$( '.megamaxmenu_instance_wrap' ).fadeOut();
		});
		$( '.megamaxmenu_instance_wrap' ).on( 'click' , function(e){
			if( $( e.target ).hasClass( 'megamaxmenu_instance_wrap' ) ){
				$(this).fadeOut();
			}
		});

		$( '.megamaxmenu_instance_toggle' ).on( 'click' , function(){
			$( '.megamaxmenu_instance_container_wrap' ).fadeIn();
			$( '.megamaxmenu_instance_container_wrap .megamaxmenu_instance_input' ).focus();
		});

		$form = $( 'form.megamaxmenu_instance_form' );
		$form.on( 'submit' , function(e){
			e.preventDefault();
			megamaxmenu_save_instance( $form );
			return false;
		});

		$( '.megamaxmenu_instance_create_button' ).on( 'click' , function(e){
			e.preventDefault();
			megamaxmenu_save_instance( $form );
			return false;
		});

		$( '.megamaxmenu_instance_button_delete' ).on( 'click' , function( e ){
			e.preventDefault();
			if( confirm( 'Are you sure you want to delete this MegaMaxMenu Configuration?' ) ){
				megamaxmenu_delete_instance( $(this) );
			}
			return false;
		});

		//Highlight code
		$( '.megamaxmenu-highlight-code' ).on( 'click' , function(e){
			megamax_selectText( $(this)[0] );
		});



		//Open Hash Tab
		setTimeout( function(){
			if( window.location.hash ){
				//console.log( window.location.hash + '-tab ' + $( window.location.hash + '-tab' ).size() );

				$( window.location.hash + '-tab' ).click();
			}
		} , 500 );

	}

	function update_gradient_list( $control ){
		var colors = '';
		var color = '';
		$control.find( '.megamaxmenu-color-stop' ).each( function(){
			if( $(this).data( 'cleared' ) ) color = '';
			else color = $( this ).wpColorPicker( 'color' );
			//console.log( color +',');
			if( color ){
				if( colors.length > 0 ) colors += ',';
				colors += color;
			}
		});
		//console.log( 'colors = ' + colors );
		$control.find( '.megamaxmenu-gradient-list' )
			.val( colors );
			//.trigger( 'change' );
	}

	function megamaxmenu_save_instance( $form ){
		var data = {
			action: 'megamaxmenu_add_instance',
			megamaxmenu_data: $form.serialize(),
			megamaxmenu_nonce: $form.find( '#_wpnonce' ).val()
		};
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post( ajaxurl, data, function(response) {
			//console.log( response );

			if( response == -1 ){
				$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
				$( '.megamaxmenu_instance_notice_error' ).fadeIn();

				$( '.megamaxmenu-error-message' ).text( 'Please try again.' );

				return;
			}
			else if( response.error ){
				$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
				$( '.megamaxmenu_instance_notice_error' ).fadeIn();

				$( '.megamaxmenu-error-message' ).text( response.error );

				return;
			}
			else{
				$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
				$( '.megamaxmenu_instance_notice_success' ).fadeIn();
			}

		}, 'json' ).fail( function(){
			$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
			$( '.megamaxmenu_instance_notice_error' ).fadeIn();
		});
	}

	function megamaxmenu_delete_instance( $a ){
		var data = {
			action: 'megamaxmenu_delete_instance',
			megamaxmenu_data: {
				'megamaxmenu_instance_id' : $a.data( 'megamaxmenu-instance-id' )
			},
			megamaxmenu_nonce: $a.data( 'megamaxmenu-nonce' )
		};

		//console.log( data );

		jQuery.post( ajaxurl, data, function(response) {
			//console.log( response );

			if( response == -1 ){
				$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
				$( '.megamaxmenu_instance_delete_notice_error' ).fadeIn();

				$( '.megamaxmenu-delete-error-message' ).text( 'Please try again.' );

				return;
			}
			else if( response.error ){
				$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
				$( '.megamaxmenu_instance_delete_notice_error' ).fadeIn();

				$( '.megamaxmenu-delete-error-message' ).text( response.error );

				return;
			}
			else{
				$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
				$( '.megamaxmenu_instance_delete_notice_success' ).fadeIn();

				var id = response.id;
				$( '#megamaxmenu_'+id+', #megamaxmenu_'+id+'-tab' ).remove();	//delete tab and content
				$( '.nav-tab-wrapper > a' ).first().click();			//switch to first tab
			}

		}, 'json' ).fail( function(){
			$( '.megamaxmenu_instance_container_wrap' ).fadeOut();
			$( '.megamaxmenu_instance_delete_notice_error' ).fadeIn();
		});


	}

	function megamax_store( item , key , val ){
		val = val || false;

		var store = localStorage.getItem( item );

		var jstore = {};
		if( store ){
			jstore = JSON.parse( store );
		}

		//retrieve
		if( val === false ){
			if( jstore ){
				return jstore[key];
			}
		}

		//store
		else{
			jstore[key] = val;
			var jstore_string = JSON.stringify( jstore );
			localStorage.setItem( item , jstore_string );
		}

	}

	function megamax_selectText( element ) {
		var doc = document
			//, text = element //doc.getElementById(element)
			, range, selection
		;
		if (doc.body.createTextRange) { //ms
			range = doc.body.createTextRange();
			range.moveToElementText( element );
			range.select();
		} else if (window.getSelection) { //all others
			selection = window.getSelection();
			range = doc.createRange();
			range.selectNodeContents( element );
			selection.removeAllRanges();
			selection.addRange(range);
		}
	}



})(jQuery);
