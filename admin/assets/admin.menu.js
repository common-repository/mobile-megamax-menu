(function($){

	var megamaxmenu_admin_is_initialized = false;

	//jQuery( document ).ready( function($){
	jQuery(function($) {
		initialize_megamaxmenu_admin( 'document.ready' );
	});

	//Backup
	$( window ).on( 'load' , function(){
		initialize_megamaxmenu_admin( 'window.load' );
	});

	function initialize_megamaxmenu_admin( init_point ){

		if( megamaxmenu_admin_is_initialized ) return;

		megamaxmenu_admin_is_initialized = true;

		if( ( typeof console != "undefined" ) && init_point == 'window.load' ) console.log( 'MegaMaxMenu admin initialized via ' + init_point );


		var $current_menu_item = null;
		var current_menu_item_id = '';	//menu-item-x
		var $current_panel = null;

		var $settingswrap = $( '.megamaxmenu-menu-item-settings-wrapper' );
		var $shiftwrap = $( '.shiftnav-menu-item-settings-wrapper' );
		var shiftnav = $shiftwrap.length > 0 ? true : false;

		//remove loading notice
		$( '.megamaxmenu-js-check' ).remove();

		//Add View in Sandbox button
		//$( '.publishing-action' ).before( '<a href="#" class="button button-primary">View MegaMaxMenu in Sandbox</a>' );
		$( '.nav-tab-wrapper' ).append( '<a target="_blank" href="'+megamaxmenu_meta.sandbox_url+'" class="nav-tab">View MegaMaxMenu in Sandbox</a>' );

		//handle adding the "Uber" button on each menu item upon first interaction
		$( '#menu-management' ).on( 'mouseenter touchEnd MSPointerUp pointerup' , '.menu-item:not(.megamaxmenu-processed)' , function(e){
			$(this).addClass( 'megamaxmenu-processed' );
			$(this).find( '.item-title' ).append( '<span class="megamaxmenu-settings-toggle" data-megamax-toggle="' + $(this).attr('id') + '"><i class="fas fa-cog"></i> MegaMax <span class="megamaxmenu-unsaved-alert"><i class="fas fa-exclamation-triangle"></i> <span class="megamaxmenu-unsaved-alert-message">Unsaved</span></span></span>' );
			//console.log( $(this).find( '.item-title' ).text() );
		});

		//Close when ShiftNav is opened
		if( shiftnav ){
			$( '#menu-management' ).on( 'click' , '.shiftnav-settings-toggle' , function( e ){
				$settingswrap.removeClass( 'megamaxmenu-menu-item-settings-open' );
				$( 'body' ).removeClass( 'megamaxmenu-settings-panel-is-open' );
			});
		}

		//Don't allow clicks to propagate when clicking the toggle button, to avoid drag-starts of the menu item
		$( '#menu-management' ).on( 'mousedown' , '.megamaxmenu-settings-toggle' , function( e ){
			e.preventDefault();
			e.stopPropagation();

			return false;
		});

		$(document).on( 'keyup', function(e) {
			if(e.keyCode == 27) {
				if( $current_panel ){
					$settingswrap.toggleClass('megamaxmenu-menu-item-settings-open');
				}
			}
		});

		//Handle clicking the "Uber" button on each menu item - open settings
		$( '#menu-management' ).on( 'click' , '.megamaxmenu-settings-toggle' , function( e ){

			var this_menu_item_id = $(this).attr( 'data-megamax-toggle' );
			var this_menu_item_id_num = this_menu_item_id.substr(10);

			$current_menu_item = $(this).parents( 'li.menu-item' ); //closest?

			//Close ShiftNav
			if( shiftnav ){
				$shiftwrap.removeClass( 'shiftnav-menu-item-settings-open' );
				$( 'body' ).removeClass( 'shiftnav-settings-panel-is-open' );
			}

			//This is already the current item
			if( this_menu_item_id == current_menu_item_id ){
				$settingswrap.toggleClass( 'megamaxmenu-menu-item-settings-open' );
				$( 'body' ).toggleClass( 'megamaxmenu-settings-panel-is-open' );
			}
			//Switching to a different item
			else{
				$settingswrap.addClass( 'megamaxmenu-menu-item-settings-open' );
				$( 'body' ).addClass( 'megamaxmenu-settings-panel-is-open' );
				//$( '.megamaxmenu-menu-item-tab' ).click();
				//Update

				$current_panel = $settingswrap.find( '.megamaxmenu-menu-item-panel-' + this_menu_item_id );

				//Create Panel if it doesn't exist
				if( $current_panel.length === 0 ){
					$current_panel = $( '.megamaxmenu-menu-item-panel-negative' ).clone();
					$current_panel.removeClass( 'megamaxmenu-menu-item-panel-negative' );
					$current_panel.addClass( 'megamaxmenu-menu-item-panel-' + this_menu_item_id );
					$current_panel.attr( 'data-menu-item-target-id' , this_menu_item_id_num );

					//If settings have loaded properly, remove the error notice
					if( $current_panel.find( '.megamaxmenu-settings-completion-marker' ).length ){
						$current_panel.find( '.megamaxmenu-menu-item-load-error-notice' ).remove();
					}

					var hash = '#' + this_menu_item_id;

					var item_type = $current_menu_item.find('.item-type:first').text();
					if( $current_menu_item.find('.item-type').length > 1 && typeof console != "undefined" )
						console.warn( '[MegaMaxMenu Notice] Multiple menu item types defined by custom nav walker.  If you are not seeing the proper settings, try enabling "Disable Custom Menus Panel Walker" in the MegaMaxMenu Control Panel > General Settings > Theme Integration' );
					var item_type_class = item_type.replace( '[' , '' ).replace( ']' , '' ).replace( / /g , '_' ).toLowerCase();
					//console.log( item_type + ' :: ' + item_type_class );
					$current_panel.addClass( 'megamaxmenu-menu-item-panel-type-' + item_type_class );

					var data_object_type = $current_menu_item.find( '.menu-item-data-object' ).val();
					if( data_object_type == 'megamaxmenu-custom' ) data_object_type = item_type;

	// console.log( data_object_type );
	// console.log( item_type );
	// console.log( item_type_class );

					//Get the Title from the Menu Item
					var _title = $current_menu_item.find('.menu-item-title').text();
					if( !_title ){
						_title = $current_menu_item.find('.item-title').text();
						_title = _title.substring( 0 , _title.indexOf( ' MegaMax' ) );
					}

					//Set Panel's Item Title, ID link, and Type
					$current_panel.find( '.megamaxmenu-menu-item-title' ).html( '<a href="'+hash+'"><i class="fas fa-location-arrow"></i> '+_title+'</a>' );
					$current_panel.find( '.megamaxmenu-menu-item-id' ).html( '<a href="'+hash+'">'+hash+'</a>' );
					$current_panel.find( '.megamaxmenu-menu-item-type' ).text( item_type );

					//Remove unneeded tabs based on megamaxmenu_get_menu_item_panels_map
					var item_panels = megamaxmenu_meta.item_panels['default'];
					if( typeof megamaxmenu_meta.item_panels[data_object_type] != 'undefined' ){
						//item_panels = megamaxmenu_meta.item_panels[item_type];
						item_panels = megamaxmenu_meta.item_panels[data_object_type];
					}
	//console.log( item_panels );
					$current_panel.find( 'li.megamaxmenu-menu-item-tab' ).each( function(){
						if( item_panels.indexOf( $(this).data( 'megamaxmenu-tab' ) ) == -1 ){
							$(this).remove();
							$current_panel.find( '.megamaxmenu-menu-item-tab-content[data-megamaxmenu-tab-content=' + $(this).data( 'megamaxmenu-tab' ) + ']' ).remove();
						}
					});



					var item_data = megamaxmenu_menu_item_data[this_menu_item_id_num];
					if( item_data ){

						$current_panel.find( '[data-megamaxmenu-setting]' ).each( function(){
							var _data_name = $(this).data( 'megamaxmenu-setting' );

							//if( item_data[_data_name] ){
							if( item_data.hasOwnProperty( _data_name ) ){

								//console.log( _data_name + ' :: ' +  $(this).attr('type') );
								switch( $(this).attr('type') ){

									case 'checkbox':

										//Multicheck
										if( $(this).hasClass( 'megamaxmenu-multicheckbox' ) ){
											//console.log( 'multicheck ' + $(this).parent('label').text() );
											if( $.isArray( item_data[_data_name] ) ){
												//console.log( 'an array' );
												if( 0 <= $.inArray( $(this).attr( 'value' ) , item_data[_data_name] ) ){
													//console.log( 'in array - check' );
													$(this).prop( 'checked' , true );
												}
												else{
													//console.log( 'not in array' );
													$(this).prop( 'checked' , false );
												}
											}
											else{
												//console.log( 'not an array - uncheck' );
												$(this).prop( 'checked' , false );
											}
										}
										//Single Check
										else if( item_data[_data_name] == 'on' ){
											$(this).prop( 'checked' , true );
										}
										else{
											$(this).prop( 'checked' , false );
										}
										break;

									case 'radio':

										if( item_data[_data_name] == $( this ).val() ){
											$(this).prop( 'checked' , true );
											$(this).closest( '.megamaxmenu-menu-item-setting-input-wrap' ).find( '.megamaxmenu-radio-label' ).removeClass( 'megamaxmenu-radio-label-selected' );
											$(this).closest( '.megamaxmenu-radio-label' ).addClass( 'megamaxmenu-radio-label-selected' );
										}
										break;

									case 'text':

										$(this).val( item_data[_data_name] );	//the basics;

										//Now check for specific types of settings that use text inputs

										//Media
										if( $(this).hasClass( 'megamaxmenu-media-id' ) ){

											if( item_data.hasOwnProperty( _data_name+'_url' ) ){
												//console.log( 'media: ' + _data_name );
												$wrap = $(this).closest( '.megamaxmenu-media-wrapper' );
												$wrap.find( '.media-preview-wrap' )
													.html( '<img src="'+item_data[_data_name+'_url']+'" />' );
												$wrap.find('.megamaxmenu-edit-media-button' ).css( 'display' , 'block' )
													.attr( 'href' , item_data[_data_name+'_edit'] );

											}
										}
										//Colorpicker
										else if( $(this).hasClass( 'megamaxmenu-colorpicker' ) ){
											//console.log( item_data[_data_name] );
											//$(this).attr( 'data-default-color' , item_data[_data_name] );
											//console.log( 'colorpicker' );
										}
										else if( $(this).hasClass( 'megamaxmenu-autocomplete-setting' ) ){
											var val = $(this).val();
											if( val ){
												if( !isNaN( val) ){
													var txt = $(this).closest( '.megamaxmenu-autocomplete' )
														.find( '.megamaxmenu-autocomplete-op[data-val='+val+'] .megamaxmenu-autocomplete-op-name' )
														.text();
													$( this ).prev( '.megamaxmenu-autocomplete-input' ).val( txt );
												}
											}
										}

										break;


									default:

										//if( $(this).is( 'textarea' ) ){
										//	$(this).
										//}

										$(this).val( item_data[_data_name] );

								}
							}

							switch( _data_name ){

								case 'icon':
									var $icon_wrap = $( this ).parents( '.megamaxmenu-icon-settings-wrap' );
									//console.log( item_data.icon );
									$icon_wrap.find( '.megamaxmenu-icon-selected i' ).attr( 'class' , 'megamaxmenu-icon ' + item_data.icon ); //This can stay as an <i> because it isn't processed into an SVG prior to this
									break;

							}
						});

						//for( _setting in item_data ){
							//console.log( _setting + ' :: ' + item_data[_setting] );
						//}

					}

					//Setup Colorpickers
					$current_panel.find( '.megamaxmenu-colorpicker' ).wpColorPicker();



					$current_panel.find( '.megamaxmenu-menu-item-tab-content' ).hide();

					$current_panel.on( 'click' , '.megamaxmenu-menu-item-tab a' , function( e ){
						e.preventDefault();
						e.stopPropagation();
	//console.log( $(this).data('megamaxmenu-tab') );
	//console.log( $current_panel.find( '[data-megamaxmenu-tab-content="'+$(this).data('megamaxmenu-tab') + '"]' ).size() );
	//
						$current_panel.find( '.megamaxmenu-menu-item-tab > a' ).removeClass( 'megamaxmenu-menu-item-tab-current' );
						$(this).addClass( 'megamaxmenu-menu-item-tab-current' );
						$current_panel.find( '.megamaxmenu-menu-item-tab-content' ).slideUp();
						$current_panel.find( '[data-megamaxmenu-tab-content="'+$(this).data('megamaxmenu-tab') + '"]' ).slideDown();

						return false;
					});

					$current_panel.find( '.megamaxmenu-menu-item-tab > a' ).first().click();

					$settingswrap.append( $current_panel );
				}

				//Set Depth
				var cmi_class = $current_menu_item.attr( 'class');
				var substrstart = cmi_class.indexOf( 'menu-item-depth-' ) + 16;
				var depth = cmi_class.substring( substrstart , substrstart + 1 );
				$current_panel.attr( 'data-depth' , depth );

				//Hide all other panels
				$settingswrap.find( '.megamaxmenu-menu-item-panel' ).hide();
				$settingswrap.show();
				$current_panel.fadeIn();

			}

			current_menu_item_id = this_menu_item_id;

			return false;
		});

		//When a setting is changed, set the flag on the settings panel and on the menu item itself
		$settingswrap.on( 'change' , '.megamaxmenu-menu-item-setting-input' , function( e ){

			//Flag Settings Panel
			var $form = $(this).parents( 'form.megamaxmenu-menu-item-settings-form' );
			$form.find( '.megamaxmenu-menu-item-status' ).attr( 'class' , 'megamaxmenu-menu-item-status megamaxmenu-menu-item-status-warning' );
			$form.find( '.megamaxmenu-status-message' ).html( 'Settings have changed.  Click <strong>Save Menu Item</strong> to preserve these changes.' );

			//Flag Menu Item
			var item_id = $form.parents( '.megamaxmenu-menu-item-panel' ).data( 'menu-item-target-id' );
			$( '#menu-item-' + item_id ).addClass( 'megamaxmenu-unsaved' );
		});

		//Highlight radio selections
		$settingswrap.on( 'click' , '.megamaxmenu-radio-label' , function(){
			$(this).closest( '.megamaxmenu-menu-item-setting-input-wrap' ).find( '.megamaxmenu-radio-label' ).removeClass( 'megamaxmenu-radio-label-selected' );
			$(this).addClass( 'megamaxmenu-radio-label-selected' );
		});


		//Save Settings Button
		$settingswrap.on( 'click' , '.megamaxmenu-menu-item-save-button', function( e ){
			e.preventDefault();
			e.stopPropagation();

			var $form = $(this).parents('form.megamaxmenu-menu-item-settings-form' );
			var serialized = $form.serialize();
			//console.log( 'serial: ' + serialized );

			//return;

			var data = {
				action: 'megamaxmenu_save_menu_item',
				settings: serialized,
				menu_item_id: current_menu_item_id,
				megamaxmenu_nonce: megamaxmenu_meta.nonce
			};

			$formStatus = $form.find( '.megamaxmenu-menu-item-status' );
			$formStatusMessage = $form.find( '.megamaxmenu-status-message' );
			$formStatus.attr( 'class', 'megamaxmenu-menu-item-status megamaxmenu-menu-item-status-working' );
			$formStatusMessage.text( 'Processing save request...' );

			$.post( megamaxmenu_meta.ajax_url, data, function( response ) {
				//console.log('Got this from the server: ' , response );
				if( response == -1 ){
					$formStatus.attr( 'class', 'megamaxmenu-menu-item-status megamaxmenu-menu-item-status-error' );
					$formStatusMessage.html( '<strong>Error encountered.  Settings could not be saved.</strong>  Your login/nonce may have expired.  Please try refreshing the page.');
					//console.log( response );
				}
				else{
					//$( '.megamaxmenu-menu-item-panel-' + response.menu_item_id )
					$formStatus.attr( 'class', 'megamaxmenu-menu-item-status megamaxmenu-menu-item-status-success' );
					$formStatusMessage.text( 'Settings Saved' );
					megamaxmenu_meta.nonce = response.nonce;	//update nonce

					//Remove flag on menu item
					var item_id = $form.parents( '.megamaxmenu-menu-item-panel' ).data( 'menu-item-target-id' );
					$( '#menu-item-' + item_id ).removeClass( 'megamaxmenu-unsaved' );
				}

			}, 'json' ).fail( function( d ){
				$formStatus.attr( 'class', 'megamaxmenu-menu-item-status megamaxmenu-menu-item-status-error' );
				$formStatusMessage.html( '<strong>Error encountered.  Settings could not be saved.</strong>  Response Text: <br/><textarea>' + d.responseText + '</textarea>');
				//console.log( d.responseText );
				//console.log( d );
			});

			return false;
		});

		//Close Settings Button
		$settingswrap.on( 'click' , '.megamaxmenu-menu-item-settings-close' , function( e ){
			e.preventDefault();
			e.stopPropagation();

			$settingswrap.removeClass( 'megamaxmenu-menu-item-settings-open' );
			$( 'body' ).removeClass( 'megamaxmenu-settings-panel-is-open' );
		});

		//Scroll to the menu item when the ID is clicked
		$settingswrap.on( 'click' , '.megamaxmenu-menu-item-id a, .megamaxmenu-menu-item-title a' , function( e ){
			var $item = $( $(this).attr( 'href' ) );
			//console.log( $item.offset() );
			var y = $item.offset().top - 50;
			$('html, body').animate({scrollTop:y}, 'normal');
			return false;
		});

		//Show Icon Selection panel when icon is clicked
		$settingswrap.on( 'click' , '.megamaxmenu-icon-selected' , function( e ){
			// console.log( 'clicked' );
			// console.log( this );
			var $icon_set = $( this ).closest( '.megamaxmenu-icon-settings-wrap' );
			$icon_set.find( '.megamaxmenu-icons' ).fadeToggle(100);
			$icon_set.find( '.megamaxmenu-icons-search' ).focus();
		});


		//Change selected icon when clicked
		$settingswrap.on( 'click' , '.megamaxmenu-icon-settings-wrap .megamaxmenu-icon-wrap' , function( e ){
			$icon = $( this ).find( '.megamaxmenu-icon' ); //The icon that was clicked
			var $icon_set = $( this ).closest( '.megamaxmenu-icon-settings-wrap' ); //The wrapper for this setting
			//console.log( $icon.attr( 'class' ) + ' | ' + $icon.data( 'megamaxmenu-icon' )  );
			//$icon_set.find( '.megamaxmenu-icon-selected i' ).attr( 'class' , $icon.attr( 'class' ) ); //TODO

			//Change the SVG's data and Font Awesome will reload it - what about non-SVG or another standard?
			//$icon_set.find( '.megamaxmenu-icon-selected .megamaxmenu-icon' ).attr( 'data-prefix' , $icon.attr( 'data-prefix' ) ).attr( 'data-icon' , $icon.attr( 'data-icon' ) );
			$icon_set.find( '.megamaxmenu-icon-selected .megamaxmenu-icon' ).replaceWith( '<i class="megamaxmenu-icon ' + $icon.attr( 'data-megamaxmenu-icon' ) + '"></i>' );
			$icon_set.find( 'select' ).val( $icon.data( 'megamaxmenu-icon' ) ).change();
			$( this ).parents( '.megamaxmenu-icons' ).fadeOut();
		});

		/* Filter Icons */
		$settingswrap.on( 'keyup' , '.megamaxmenu-icons-search' , function( e ){
			var $icon_set = $( this ).closest( '.megamaxmenu-icon-settings-wrap' ).find( '.megamaxmenu-icon-wrap' );
			var val = $(this).val();
			if( val == '' ){
				$icon_set.show();
			}
			else{
				$icon_set.filter( ':not( [data-megamaxmenu-search-terms*="' +val+ '"] )' ).hide();
				//console.log( 'not( [data-megamaxmenu-search-terms*=' +$(this).val().toLowerCase()+ '] )' );
			}
		});


		/* Filtering autocompletes */
		$settingswrap.on( 'click' , '.megamaxmenu-autocomplete-clear' , function( e ){
			var $wrap = $( this ).closest( '.megamaxmenu-autocomplete' );

			$wrap.find( '.megamaxmenu-menu-item-setting-input' )
				.val( '' )
				.trigger( 'change' );

			//Hide dropdown
			$wrap.find( '.megamaxmenu-autocomplete-ops' ).hide();

			var $autocomp = $wrap.find( '.megamaxmenu-autocomplete-input' );
			$autocomp.val( '' );
			$autocomp.data( 'current-id' , '' ); //$(this).data( 'val' ) );
			$autocomp.data( 'current-name' , '' ); //$(this).text() );
			$autocomp.data( 'auto-set' , 'true' );
		});
		$settingswrap.on( 'click' , '.megamaxmenu-autocomplete-toggle' , function( e ){
			$( this ).closest( '.megamaxmenu-autocomplete' ).find( '.megamaxmenu-autocomplete-ops' ).toggle();
		});
		$settingswrap.on( 'focus' , '.megamaxmenu-autocomplete-input' , function( e ){
			$( this ).closest( '.megamaxmenu-autocomplete' ).find( '.megamaxmenu-autocomplete-ops' ).show();
		});
		$settingswrap.on( 'blur' , '.megamaxmenu-autocomplete-input' , function( e ){
	//console.log( 'blur ' + $(this).data('auto-set' ) + ' :: ' +  $(this).data( 'current-name' ) );

			if( $(this).data( 'auto-set' ) == 'true' ){
				//$( this ).closest( '.megamaxmenu-autocomplete' ).find( '.megamaxmenu-autocomplete-ops' ).hide();
			}
			//If not set, revert val
			else{
				$( this ).val( $(this).data( 'current-name' ) );
			}

		});
		$settingswrap.on( 'keyup' , '.megamaxmenu-autocomplete-input' , function( e ){

			$(this).data( 'auto-set' , 'false' );

			$opwrap = $( this ).closest( '.megamaxmenu-autocomplete' ).find( '.megamaxmenu-autocomplete-ops' );
			$opwrap.show();

			var val = $(this).val().toLowerCase();
			$ops = $opwrap.find( '.megamaxmenu-autocomplete-op' );
			if( val == '' ){
				$ops.show();
			}
			else{
				$ops.filter( '[data-opname*='+val+']' ).show();
				$ops.filter( ':not( [data-opname*='+val+'] )' ).hide();
			}

		});
		$settingswrap.on( 'click' , '.megamaxmenu-autocomplete-op' , function( e ){

			//console.log( 'val = ' + $(this).data( 'val' ) );
			var $wrap = $( this ).closest( '.megamaxmenu-autocomplete' );

			//Change actual setting val
			$wrap.find( '.megamaxmenu-menu-item-setting-input' )
				.val( $(this).data( 'val' ) )
				.trigger( 'change' );

			//Hide dropdown
			$wrap.find( '.megamaxmenu-autocomplete-ops' ).hide();
		});
		$settingswrap.on( 'change' , '.megamaxmenu-autocomplete .megamaxmenu-menu-item-setting-input' , function( e ){

			var $wrap = $( this ).closest( '.megamaxmenu-autocomplete' );

			var id = $(this).val();
			var name = '';

			if( id ){
				var $match = $wrap.find( '.megamaxmenu-autocomplete-op[data-val='+id+'] .megamaxmenu-autocomplete-op-name' );
				if( $match.size() > 0 ){
					name = $match.text();
				}
			}

			var $autocomp = $wrap.find( '.megamaxmenu-autocomplete-input' );
			$autocomp.val( name );
			$autocomp.data( 'current-id' , id ); //$(this).data( 'val' ) );
			$autocomp.data( 'current-name' , name ); //$(this).text() );
			$autocomp.data( 'auto-set' , 'true' );


		});


		/* Tooltips */
		/*
		$( '[data-um-tooltip]' ).each( function(){
			$(this).append( '<span class="um-tooltip">' + $(this).data( 'um-tooltip' ) + '</span>' );
		});
		*/



		/* Image Selection */
		var file_frame;
		var $wrap;

		$settingswrap.on( 'click', '.megamaxmenu-media-wrapper .megamaxmenu-setting-button' , function( event ){

			$wrap = $( this ).closest( '.megamaxmenu-media-wrapper' );
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery( this ).data( 'uploader-title' ),
				button: {
					text: jQuery( this ).data( 'uploader-button-text' ),
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				$wrap.find( '.media-preview-wrap' ).html( '<img src="' + attachment.url + '"/>' );
				$wrap.find( 'input.megamaxmenu-menu-item-setting-input' ).val( attachment.id ).trigger('change');

				//$wrap.find( '.megamaxmenu-edit-media-button' ).attr( 'href' , attachment.id );
				//wp-admin/post.php?post=274&action=edit
			});

			// Finally, open the modal
			file_frame.open();
		});

		$settingswrap.on( 'click', '.megamaxmenu-media-wrapper .megamaxmenu-remove-button' , function(e){
			var $wrap = $( this ).parents( '.megamaxmenu-media-wrapper' );
			$wrap.find( '.media-preview-wrap' ).html( '' );
			$wrap.find( 'input.megamaxmenu-menu-item-setting-input' ).val('').trigger('change');
			return false;
		});


		/* Reset Settings */
		$settingswrap.on( 'click', '.megamaxmenu-clear-settings' , function( e ){

			//Find the settings panel
			$item_settings = $( this ).parents( '.megamaxmenu-menu-item-panel' );

			//Get the menu item ID
			var menu_item_id = $item_settings.data( 'menu-item-target-id' );

			//Remove the data that the panel settings are loaded from
			delete megamaxmenu_menu_item_data[ menu_item_id ];

			//Remove the settings panel from the DOM
			$item_settings.remove();

			//Close the settings panel
			$settingswrap.removeClass( 'megamaxmenu-menu-item-settings-open' );
			$( 'body' ).removeClass( 'megamaxmenu-settings-panel-is-open' );

			//Reset the current menu item ID
			current_menu_item_id = false;

			//Re-open the settings
			$( '#menu-item-' + menu_item_id ).find( '.megamaxmenu-settings-toggle' ).click();

			//Flag as unsaved
			var $panel = $( '.megamaxmenu-menu-item-panel-menu-item-' + menu_item_id );
			var $form = $panel.find( 'form.megamaxmenu-menu-item-settings-form' );
			$form.find( '.megamaxmenu-menu-item-status' ).attr( 'class' , 'megamaxmenu-menu-item-status megamaxmenu-menu-item-status-warning' );
			$form.find( '.megamaxmenu-status-message' ).html( 'Settings have been cleared.  Click <strong>Save Menu Item</strong> to complete setting reset.' );

			//Flag Menu Item
			$( '#menu-item-' + menu_item_id ).addClass( 'megamaxmenu-unsaved' );


		});

	}

})(jQuery);
