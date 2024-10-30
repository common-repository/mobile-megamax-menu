//Define dependencies first because deferred scripts don't respect document ready order
;var megamax_supports = (function() {
	var div = document.createElement('div'),
		vendors = 'Khtml Ms O Moz Webkit'.split(' ');
	return function(prop) {
		var len = vendors.length;
		if ( prop in div.style ) return true;
		prop = prop.replace(/^[a-z]/, function(val) {
			return val.toUpperCase();
		});
		while(len--) {
			if ( vendors[len] + prop in div.style ) {
				return true;
			}
		}
		return false;
	};
})();

function megamax_op( id , args , def ){
	//If no id
	if( !megamaxmenu_data.hasOwnProperty( id ) ){
		return def;
	}
	var val = megamaxmenu_data[id];
	if( args.hasOwnProperty( 'datatype' ) ){
		switch( args['datatype'] ){
			case 'numeric':
				val = parseInt( val );
				break;

			case 'boolean':
				if( val == 'on' || val == 1 || val == '1' ){
					val = true;
				}
				else{
					val = false;
				}
				break;
		}
	}
	return val;
}

;(function($,sr){
  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function (func, threshold, execAsap) {
      var timeout;
      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if (!execAsap)
                  func.apply(obj, args);
              timeout = null;
          };
          if (timeout)
              clearTimeout(timeout);
          else if (execAsap)
              func.apply(obj, args);
          timeout = setTimeout(delayed, threshold || 100);
      };
  }
  jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'megamaxsmartresize');

;(function ( $, window, document, undefined ) {
	'use strict';
	var pluginName = "megamaxmenu",
		defaults = {
			breakpoint: megamax_op( 'responsive_breakpoint' , { datatype: 'numeric' } , 959 ),
			touchEvents: true,
			mouseEvents: true,
			retractors: true,
			touchOffClose: megamax_op( 'touch_off_close' , {datatype: 'boolean' } , true ), //true,
			submenuIndicatorCloseMobile: megamax_op( 'submenu_indicator_close_mobile' , {datatype: 'boolean' } , true ),
			moveThreshold: 10,				//Distance until tap is cancelled in deference to move/scroll
			submenuAnimationDuration: 500,	//ms duration or submenu close time
			ignoreDummies: true,
			clicktest: false,
			windowstest: false,
			debug: false,
			debug_onscreen: false,

			remove_conflicts: megamax_op( 'remove_conflicts' , {datatype: 'boolean' } , true ),
			reposition_on_load: megamax_op( 'reposition_on_load' , { datatype: 'boolean' } , false ),
			accessible: megamax_op( 'accessible' , {datatype: 'boolean' } , true ),
			retractor_display_strategy: megamax_op( 'retractor_display_strategy' , {datatype: 'string'}, 'responsive' ),

			intent_delay: megamax_op( 'intent_delay' , { datatype: 'numeric' } , 300 ),			//delay before the menu closes
			intent_interval: megamax_op( 'intent_interval' , { datatype: 'numeric' } , 100 ),		//polling interval for mouse comparisons
			intent_threshold: megamax_op( 'intent_threshold' , { datatype: 'numeric' } , 300 ),	//maximum number of pixels mouse can move to be considered intent

			scrollto_offset: megamax_op( 'scrollto_offset' , { datatype: 'numeric' } , 0 ),
			scrollto_duration: megamax_op( 'scrollto_duration' , { datatype: 'numeric' } , 1000 ),
			collapse_after_scroll: megamax_op( 'collapse_after_scroll' , {datatype:'boolean'}, true ),

			aria_role_navigation: megamax_op( 'aria_role_navigation' , {datatype:'boolean' } , false ),
			aria_nav_label: megamax_op( 'aria_nav_label' , {datatype:'boolean'} , false ),
			aria_expanded: megamax_op( 'aria_expanded' , {datatype:'boolean'} , false ),
			aria_hidden: megamax_op( 'aria_hidden' , {datatype:'boolean'} , false ),
			aria_responsive_toggle: megamax_op( 'aria_responsive_toggle' , {datatype:'boolean'} , false ),
			icon_tag: megamax_op( 'icon_tag', {datatype:'string'}, 'i' )

		}/*,
		keys = {
			BACKSPACE: 8,
            COMMA: 188,
            DELETE: 46,
            DOWN: 40,
            END: 35,
            ENTER: 13,
            ESCAPE: 27,
            HOME: 36,
            LEFT: 37,
            PAGE_DOWN: 34,
            PAGE_UP: 33,
            PERIOD: 190,
            RIGHT: 39,
            SPACE: 32,
            TAB: 9,
            UP: 38
        }*/;

	// instantiate variables
	// cX, cY = current X and Y position of mouse, updated by mousemove event
	// pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
	var cX, cY, pX, pY;

	function Plugin ( element, options ) {

		var plugin = this;	//reference for all
		this.element = element;
		this.$megamaxmenu = $( this.element );

		this.orientation = this.$megamaxmenu.hasClass( 'megamaxmenu-vertical' ) ? 'v' : 'h';

		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.settings.responsive = this.$megamaxmenu.hasClass( 'megamaxmenu-responsive' ) ? true : false;

		if( this.settings.debug && this.settings.debug_onscreen ){
			$( 'body' ).append( '<div id="megamax-onscreen-debug" style="color:#eee;z-index:10000;background:#222;position:fixed;left:0; bottom:0; width:100%; height:50%; padding:10px;overflow:scroll;"> ' );
			this.debug_target = $( '#megamax-onscreen-debug' );
			this.debug_target.on( 'click' , function(){ if( $(this).height() < 100 ) $(this).height( '50%' ); else $(this).height( '50px' ); });
		}
		this.log( '-- START MEGAMAXMENU DEBUG --' );

		this.events_disabled = false;
		this.suppress_clicks = false; //Set to true if the mouse events support 'pointerup', which handles both touch and click (but not hover)

		this.touchenabled = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);
		if( this.touchenabled ){
			this.$megamaxmenu.addClass( 'megamaxmenu-touch' );
		}
		else this.$megamaxmenu.addClass( 'megamaxmenu-notouch' );

		if( window.navigator.pointerEnabled ){
			this.touchStart = 'pointerdown';
			this.touchEnd	= 'pointerup';
			this.touchMove	= 'pointermove';

			this.suppress_clicks = true;
		}
		else if( window.navigator.msPointerEnabled ){
			this.touchStart = 'MSPointerDown';
			this.touchEnd	= 'MSPointerUp';
			this.touchMove	= 'MSPointerMove';

			this.suppress_clicks = true;
		}
		else{
			this.touchStart = 'touchstart';
			this.touchEnd	= 'touchend';
			this.touchMove	= 'touchmove';
		}

		this.toggleevent = this.touchEnd == 'touchend' ? this.touchEnd + ' click' : this.touchEnd;	//add click except for IE
		this.transitionend = 'transitionend.megamaxmenu webkitTransitionEnd.megamaxmenu msTransitionEnd.megamaxmenu oTransitionEnd.megamaxmenu';

		//Are transitions supported?
		this.transitions = megamax_supports( 'transition' ) && !this.$megamaxmenu.hasClass( 'megamaxmenu-transition-none' );
		if( !this.transitions ) this.$megamaxmenu.addClass( 'megamaxmenu-no-transitions' );

		//Detect crappy Android & Windows browsers and disable transitions
		var ua = navigator.userAgent.toLowerCase();
		this.log( ua );

		this.allow_trigger_overrides = true;
		this.noTouchEnd = false;

		var android = this.settings.android = /android/.test( ua );
		var windowsmobile = this.settings.windowsmobile = /iemobile/.test( ua );
		if( android || windowsmobile ){
			//this.log( 'android or windows' );
			//alert( ua );
			if( ( android && !(	/chrome/.test( ua ) || /firefox/.test( ua ) || /opera/.test( ua ) ) ) ||
				( windowsmobile )
				){
				this.settings.touchOffClose = false;
				this.disableTransitions();

				//Crap Android browsers don't fire touchend properly
				if( android && !windowsmobile ){	//adjust to better determine android UA string instead
					this.$megamaxmenu
						.removeClass( 'megamaxmenu-trigger-hover_intent' )
						.removeClass( 'megamaxmenu-trigger-hover' )
						.addClass( 'megamaxmenu-trigger-click' );
					this.settings.touchEvents = false;
					this.allow_trigger_overrides = false;
				}
			}
		}

		if( windowsmobile ){
			this.log( 'disable touchoff close and accessibility' );
			this.settings.touchOffClose = false;	//improper event delegation in windows
			this.settings.accessible = false;		//WTF Microsoft
			this.settings.mouseEvents = false;
		}

		var safari5 = ( !/chrome/.test( ua ) ) && ( /safari/.test( ua ) ) && ( /version\/5/.test( ua ) );
		if( safari5 ){
			this.disableTransitions();
		}


		//Reflow right items
		this.last_width = window.innerWidth;
		var cur_width = this.last_width;
		var $right_items = plugin.$megamaxmenu.find( '.megamaxmenu-item-level-0.megamaxmenu-align-right' );
		if( $right_items.length ){
			$(window).megamaxsmartresize( function(){
				cur_width = window.innerWidth;
				if( plugin.last_width <= plugin.settings.breakpoint &&
					cur_width >= plugin.settings.breakpoint ){
					$right_items.hide(); $right_items[0].offsetHeight; $right_items.show(); //reflow
				}
				plugin.last_width = cur_width;
			});
		}

		//TESTING
		if( this.settings.clicktest ) this.touchEnd = 'click';

		this.init();

	}

	Plugin.prototype = {

		init: function () {

			this.log( 'Initializing MegaMaxMenu' );

			this.$megamaxmenu.removeClass( 'megamaxmenu-nojs' );		//We're off and running

			this.removeConflicts();	//Stop other JS from interfering when possible

			// var plugin = this;
			// $( '.megamaxmenu a' ).on( 'click touchend mouseenter mouseover mousedown mouseup' , function( e ){
			// 	plugin.log( e.type + ' ' + $(this).text() );
			// });

			//Initialize user interaction events
			this.initializeSubmenuToggleTouchEvents();
			this.initializeSubmenuToggleMouseEvents();
			this.initializeRetractors();
			this.initializeResponsiveToggle();
			this.initializeTouchoffClose();
			this.initializeTabs();
			this.initializeSubmenuPositioning();
			this.initializeSegmentCurrentStates();
			this.initializeAccessibilityOnTab();
			this.initializeAccessibilityStates();
			this.initializeImageLazyLoad();
		},

		removeConflicts: function(){
			if( this.settings.remove_conflicts ){
				this.$megamaxmenu.find( '.megamaxmenu-item, .megamaxmenu-target, .megamaxmenu-submenu' ).add( this.$megamaxmenu )
					.removeAttr( 'style' )
					.unbind().off();
			}
		},



		/* Initalizers */

		initializeAccessibilityStates: function(){
			var plugin = this;

			//aria role="navigation" on nav element
			if( this.settings.aria_role_navigation ){
				plugin.$megamaxmenu.attr('role','navigation');
			}

			//aria-label="{menu title}" on nav element
			if( this.settings.aria_nav_label ){
				plugin.$megamaxmenu.attr('aria-label' , this.$megamaxmenu.find( '> .megamaxmenu-nav' ).attr( 'data-title' ));
			}

			//aria-expanded on menu items with dropdowns
			if( plugin.settings.aria_expanded ){
				plugin.$megamaxmenu.find( '.megamaxmenu-item.megamaxmenu-has-submenu-drop > .megamaxmenu-target' ).attr( 'aria-expanded' , false );
			}

			//aria-hidden on dropdowns
			if( plugin.settings.aria_hidden ){
				plugin.$megamaxmenu.find( '.megamaxmenu-submenu-drop' ).attr( 'aria-hidden' , true );
			}

		},

		initializeAccessibilityOnTab: function(){

			if( !this.settings.accessible ) return;

			var plugin = this;

			//Initialize
			$( 'body' ).on( 'keydown.megamaxmenu' , function( e ){
				var keyCode = e.keyCode || e.which;
				if( keyCode == 9 ){
					$( 'body' ).off( 'keydown.megamaxmenu' );
					plugin.initializeAccessibility();
				}
			});

		},

		initializeImageLazyLoad: function(){
			var plugin = this;
			$( '.megamaxmenu-item-level-0' ).one( 'megamaxmenuopen' , function(){
				$( this ).find( '.megamaxmenu-image-lazyload' ).each( function(){
					//Responsive images - add srcset and sizes if present
					if( $( this ).data( 'srcset' ) ){
						$( this )
							.attr( 'srcset' , $( this ).data( 'srcset' ) )
							.attr( 'sizes' , $( this ).data( 'sizes' ) )
					}
					//Whether responsive images (4.4+) or not, set src attribute
					$( this )
						.attr( 'src' , $( this ).data( 'src' ) )
						.removeClass( 'megamaxmenu-image-lazyload' );
				});
				setTimeout( function(){
					plugin.clearTabSizes();
					plugin.sizeTabs();
				}, 300 );
			});
		},

		initializeAccessibility: function(){

			var plugin = this;
			var tabbables = '.megamaxmenu-target, a, input, select, textarea';

			plugin.$current_focus = false;
			plugin.mousedown = false;
			plugin.$megamaxmenu.addClass( 'megamaxmenu-accessible' );

			//Focus
			plugin.$megamaxmenu.on( 'focus' , tabbables , function(){

				if( !plugin.mousedown ){

					var $target = $(this);

					plugin.$current_focus = $target;
					var $item = $target.parent( '.megamaxmenu-item' );	//get the LI parent of A

					if( $item.length ){
						//Top level items - just close everything else
						if( $item.is( '.megamaxmenu-item-level-0' ) ){
							plugin.closeAllSubmenus();
						}

						//Items with submenus - open the submenus
						if( $item.is( '.megamaxmenu-has-submenu-drop' ) ){
							//Delay .5s so that if we want we can tab right through
							setTimeout( function(){
								if( !$target.is( ':focus' ) ) return; //skip if item no longer focused

								//Close the submenus of all siblings
								$item.siblings( '.megamaxmenu-has-submenu-drop' ).each( function(){
									plugin.closeSubmenu( $(this) , 'umac' , plugin );
								});

								//Open this item's submenu
								plugin.openSubmenu( $item, 'umac' , plugin );

							}, 500 );
						}

						//Focusout - any time an element is blurred, check to see if the
						//xUse focusout because it handles blur on all types of child elements
						//plugin.$megamaxmenu.on( 'focusout' , '.megamaxmenu-item-level-0' , function(){
						//plugin.$megamaxmenu.on( 'blur' , tabbables , function(e){
						$target.on( 'blur.megamaxmenu' , tabbables , function(e){
							if( !plugin.mousedown ){
								plugin.$current_focus = false;
								$(this).off( 'blur.megamaxmenu' );

								//If a new focus within the menu isn't set within .5s, assume we've left the menu focus
								setTimeout( function(){
									if( !plugin.$current_focus ){
										//console.log( 'abandon ' , plugin.$current_focus );
										plugin.closeAllSubmenus();
									}
								}, 500 );
							}
							plugin.mousedown = false;
						});
					}
				}

				plugin.mousedown = false;

			});

			//Focus leaves MegaMaxMenu, close any open submenus
			plugin.$megamaxmenu.on( 'focusout' , function(){
				setTimeout( function(){
					if( !$( document.activeElement ).closest( plugin.$megamaxmenu ).length ){
						plugin.closeAllSubmenus();
					}
  			}, 10 );
			});


			//Keyboard interactions - left, right, escape
			plugin.$megamaxmenu.find( '.megamaxmenu-item-level-0' ).on( 'keydown' , function(e){
			  //var $megamax = jQuery( '.megamaxmenu' );
			  //console.log( e.which );
			  switch( e.which ){
			      case 39:  //right
			         plugin.closeAllSubmenus();
			         //console.log( 'right ' + jQuery( this ).next().attr('id') );
			         $( this ).next().find( '>.megamaxmenu-target' ).focus();
			         break;

			      case 37: //left
			         plugin.closeAllSubmenus();
			         //console.log( 'left '+ jQuery( this ).prev().attr('id') );
			         jQuery( this ).prev().find( '>.megamaxmenu-target' ).focus();
			         break;

			      case 27:  //escape
			         //console.log( 'esc' );
			         //jQuery( this ).find( '.megamaxmenu-target' ).focus();
			         plugin.closeAllSubmenus();
			         break;
			  }
			});



			//Mousedown - flag a mouse interaction - focus event above will be ignored if focus is result of mouse
			plugin.$megamaxmenu.on( 'mousedown' , function(e){
				plugin.mousedown = true;
				setTimeout( function(){ plugin.mousedown = false; }, 100 );
			});
		},


		initializeSubmenuPositioning: function(){
			var plugin = this;
			plugin.positionSubmenus();
			$(window).megamaxsmartresize(function(){
				plugin.positionSubmenus();
			});

			if( this.settings.reposition_on_load ){
				$(window).on( 'load' , function(){
					plugin.positionSubmenus();
				});
			}
		},

		initializeSubmenuToggleTouchEvents: function (){

			if( !this.settings.touchEvents ) return;

			var plugin = this;

			//Touch Events
			//this.$megamaxmenu.on( this.touchStart , '.megamaxmenu-item' , function(e){ plugin.handleTouchInteraction( e , this , plugin ); } );

			this.$megamaxmenu.on( this.touchStart , '.megamaxmenu-target:not(.shiftnav-toggle)' , function(e){ plugin.handleTouchInteraction( e , this , plugin ); } );
			//this.$megamaxmenu.on( this.touchEnd ,   '.megamaxmenu-item' , this.handleSubmenuToggle );			//Added only on touchStart
			//this.$megamaxmenu.on( this.touchMove ,  '.megamaxmenu-item' , this.preventInteractionOnScroll );

			//Prevent "Ghost Clicks"
			this.$megamaxmenu.on( 'click' , '.megamaxmenu-has-submenu-drop > .megamaxmenu-target, .megamaxmenu-tab.megamaxmenu-item-has-children > .megamaxmenu-target' , function(e){ plugin.handleClicks( e , this , plugin ); } );

		},

		initializeSubmenuToggleMouseEvents: function( plugin ){

			plugin = plugin || this;	//Assign this to plugin if not passed


			//Don't initialize if mouse events are disabled
			if( !plugin.settings.mouseEvents ) return;
			if( plugin.settings.clicktest ) return;
			if( plugin.settings.windowstest ) return;


			plugin.log( 'initializeSubmenuToggleMouseEvents' );


			var evt = '';
			var trigger = 'hover';
			if( plugin.$megamaxmenu.hasClass( 'megamaxmenu-trigger-click' ) ){
				trigger = 'click';
			}
			else if( plugin.$megamaxmenu.hasClass( 'megamaxmenu-trigger-hover_intent' ) ){
				trigger = 'hover_intent';
			}

			if( trigger == 'click' ){
				//In the event this device supports 'pointerup', let that event handle the interactions rather than the click event
				if( !this.suppress_clicks ){
					evt = 'click.megamaxmenu-submenu-toggle';
					//evt = 'mouseup.megamaxmenu-submenu-toggle';
					this.$megamaxmenu.on( evt , '.megamaxmenu-item.megamaxmenu-has-submenu-drop:not([data-megamaxmenu-trigger]) > .megamaxmenu-target' , function(e){ plugin.handleMouseClick( e , this , plugin ); } );
					this.$megamaxmenu.on( 'click.megamaxmenu-click-target' , '.megamaxmenu-item:not(.megamaxmenu-has-submenu-drop):not([data-megamaxmenu-trigger]) > .megamaxmenu-target' , function(e){ plugin.handleLink( e , this , plugin ); } );
				}
			}
			else if( trigger == 'hover_intent' ){
				evt = 'mouseenter.mouse_intent';	// > .megamaxmenu-target
				this.$megamaxmenu.on( evt , '.megamaxmenu-item.megamaxmenu-has-submenu-drop:not([data-megamaxmenu-trigger])' , function(e){ plugin.handleMouseIntent( e , this , plugin ); } );
				this.$megamaxmenu.on( 'click.megamaxmenu-click-target' , '.megamaxmenu-item:not([data-megamaxmenu-trigger]) > .megamaxmenu-target' , function(e){ plugin.handleLink( e , this , plugin ); } );
			}
			else{
				evt = 'mouseenter.megamaxmenu-submenu-toggle';
				this.$megamaxmenu.on( evt , '.megamaxmenu-item.megamaxmenu-has-submenu-drop:not([data-megamaxmenu-trigger]) > .megamaxmenu-target' , function(e){ plugin.handleMouseover( e , this , plugin ); } );
				this.$megamaxmenu.on( 'click.megamaxmenu-click-target' , '.megamaxmenu-item:not([data-megamaxmenu-trigger]) > .megamaxmenu-target' , function(e){ plugin.handleLink( e , this , plugin ); } );
			}

			//Now find divergents
			if( this.allow_trigger_overrides ){
				plugin.$megamaxmenu.find( '.megamaxmenu-item[data-megamaxmenu-trigger]' ).each( function(){

					var $li = $( this );
					trigger = $li.data( 'megamaxmenu-trigger' );

					if( trigger == 'click' ){
						if( !this.suppress_clicks ){	//hmm
							$li.on( 'click.megamaxmenu-submenu-toggle' , '.megamaxmenu-target' , function(e){ plugin.handleMouseClick( e , this , plugin ); } );
						}
					}
					else if( trigger == 'hover_intent' ){
						$li.on( 'mouseenter.mouse_intent' , function(e){ plugin.handleMouseIntent( e , this , plugin ); } );
						//$li.find( '> .megamaxmenu-target' ).on( 'click.megamaxmenu-click-target' , function(e){ plugin.handleLink( e , this , plugin ); } );
					}
					//Hover
					else{
						$li.on( 'mouseenter.megamaxmenu-submenu-toggle' , '.megamaxmenu-target' , function(e){ plugin.handleMouseover( e , this , plugin ); } );
					}
				});
			}
			else{
				//If trigger overrides aren't allowed, default tabs to click
				plugin.$megamaxmenu.find( '.megamaxmenu-tab' ).on( 'click.megamaxmenu-submenu-toggle' , '.megamaxmenu-target' , function(e){ plugin.handleMouseClick( e , this , plugin ); } );
			}

			//this.$megamaxmenu.on( 'mouseout.megamaxmenu-submenu-toggle'  , '.megamaxmenu-item' , this.handleMouseout );	//now only added on mouseover
		},

		disableSubmenuToggleMouseEvents: function(){
			this.log( 'disableSubmenuToggleMouseEvents' );
			this.events_disabled = true;
		},


		reenableSubmenuToggleMouseEvents: function( plugin ){
			plugin = plugin || this;

			plugin.log( 'reenableSubmenuToggleMouseEvents' );
			plugin.events_disabled = false;
		},


		initializeRetractors: function() {

			if( !this.settings.retractors ) return;	//Don't initialize if retractors are disabled

			var plugin = this;

			this.$megamaxmenu.on( 'click' , '.megamaxmenu-retractor' , function(e){ plugin.handleSubmenuRetractorEnd( e , this , plugin ); } );

			//set up the retractors
			if( this.settings.touchEvents ) this.$megamaxmenu.on( this.touchStart , '.megamaxmenu-retractor' , function(e){ plugin.handleSubmenuRetractorStart( e , this , plugin ); } );	// > .megamaxmenu-target

			//If this device does not support touch interactions, and the strategy is touch, remove the mobile retractors
			if( !this.touchenabled && plugin.settings.retractor_display_strategy == 'touch' ){
				this.$megamaxmenu.find( '.megamaxmenu-retractor-mobile' ).remove();
				this.$megamaxmenu.find( '.megamaxmenu-submenu-retractor-top' ).removeClass( 'megamaxmenu-submenu-retractor-top' ).removeClass( 'megamaxmenu-submenu-retractor-top-2' );
			}

			//Indicator toggles
			if( this.settings.submenuIndicatorCloseMobile ){
				var $target_subs = this.$megamaxmenu.find( '.megamaxmenu-has-submenu-drop > .megamaxmenu-target' )
					.append( '<span class="megamaxmenu-sub-indicator-close"><'+plugin.settings.icon_tag+' class="fas fa-times"></'+plugin.settings.icon_tag+'></span>' );
				var $indicator_toggles = $target_subs.find( '>.megamaxmenu-sub-indicator-close' );
				$indicator_toggles.on( 'click' , function(e){
						e.preventDefault();
						e.stopPropagation();
						plugin.closeSubmenuInstantly( $(this).closest( '.megamaxmenu-item' ) , 'toggleMegaMaxMenuSubmenuClosed', plugin );
						return false;
					});
				if( this.settings.touchEvents ){
					$indicator_toggles.on(this.touchStart, function(e){
						e.preventDefault();
						e.stopPropagation();
						plugin.closeSubmenuInstantly( $(this).closest( '.megamaxmenu-item' ) , 'toggleMegaMaxMenuSubmenuClosed', plugin );
						return false;
					});
				}
			}

		},


		initializeResponsiveToggle: function(){
			var plugin = this;
			var toggle_selector = '.megamaxmenu-responsive-toggle[data-megamaxmenu-target=' + plugin.$megamaxmenu.attr('id') + '], .megamaxmenu-responsive-toggle[data-megamaxmenu-target=_any_]';
			var $toggle = $( toggle_selector );

			//if( plugin.$megamaxmenu.hasClass( 'megamaxmenu-main' ) ){
			//	toggle_selector+= ', .megamaxmenu-responsive-toggle[data-megamaxmenu-target=_main_]';
			//}
			plugin.log( 'initializeResponsiveToggle ' + this.toggleevent );

			//When the window is resized, adjust the toggle visibility ARIA
			if( plugin.settings.aria_responsive_toggle ){

				//aria-hidden for toggle
				var toggle_hidden = window.innerWidth > plugin.settings.breakpoint;
				$toggle.attr( 'aria-hidden' , toggle_hidden );
				$(window).megamaxsmartresize(function(){
					$toggle.attr( 'aria-hidden' , window.innerWidth > plugin.settings.breakpoint );
				});
				var toggle_expanded = $toggle.hasClass( 'megamaxmenu-responsive-toggle-open' );
				$toggle.attr( 'aria-expanded' , toggle_expanded );
			}

			$( document ).on( this.toggleevent , toggle_selector ,
				function(e){ plugin.handleResponsiveToggle( e , this , plugin ); } );

			//IE11 Keyboard accessibility
			var isIE11 = /Trident.*rv[ :]*11\./.test(navigator.userAgent);
			if( isIE11 ){
			  $toggle.on( 'keypress', function(e){
			    if( e.keyCode === 13 || e.keyCode === 32 ){
			      plugin.handleResponsiveToggle( e , this , plugin );
			    }
			  });
			}
		},

		initializeTouchoffClose: function(){

			if( !this.settings.touchOffClose ) return;  //Don't initialize if touch off close is disabled

			var plugin = this;
			$( document ).on( this.touchStart+'.megamaxmenu_touchoff' , function(e){ plugin.handleTouchoffCloseStart( e, this , plugin ); } );
			$( document ).on( this.touchEnd+'.megamaxmenu_touchoff' , function(e){ plugin.handleTouchoffClose( e, this , 'touch' , plugin ); } );
			if( !this.suppress_clicks ) $( document ).on( 'mouseup.megamaxmenu_clickoff' , function(e){ plugin.handleTouchoffClose( e, this , 'click' , plugin ); } ); //use mouseup instead of click for firefox

		},


		initializeTabs: function(){

			var plugin  = this;
			var responsive = plugin.settings.responsive && ( window.innerWidth <= plugin.settings.breakpoint ) ? true : false;

			plugin.$tab_blocks = plugin.$megamaxmenu.find( '.megamaxmenu-tabs' );

			//Reverse order so we do the innermost panels first
			plugin.$tab_blocks = $( plugin.$tab_blocks.get().reverse() );

			//When all the images load, check the tabs
			$(window).on( 'load' , function(){
				plugin.sizeTabs();
			});

			//When the window is resized, check the tabs
			plugin.windowwidth = window.innerWidth;
			$(window).megamaxsmartresize(function(){
				plugin.oldwindowwidth = plugin.windowwidth;
				plugin.windowwidth = window.innerWidth;
				//only run if width has changed
				if( plugin.windowwidth != plugin.oldwindowwidth ){
					plugin.clearTabSizes( plugin );
					plugin.sizeTabs();
					plugin.checkActiveTabs( plugin );
				}
			});

			//When the submenu is opened (first time only), check the tabs
			plugin.$megamaxmenu.find( '.megamaxmenu-item-level-0.megamaxmenu-has-submenu-drop' ).on( 'megamaxmenuopen.sizetabs' , function(){
				$(this).off( 'megamaxmenuopen.sizetabs' );
				plugin.sizeTabs();
			});
			//plugin.sizeTabs();

			//Look for dynamically sized tabs, as we need to resize each time we switch tabs
			plugin.$megamaxmenu.find( '.megamaxmenu-tabs.megamaxmenu-tabs-dynamic-sizing' ).on( 'megamaxmenuopen' , '> .megamaxmenu-tabs-group > .megamaxmenu-tab' , function(){
				plugin.sizeTabsDynamic( $(this).closest( '.megamaxmenu-tabs' ) );
			});

			if( !responsive ){
				plugin.initializeActiveTab( plugin );
			}

		},

		checkActiveTabs: function( plugin ){
			if( window.innerWidth <= plugin.settings.breakpoint ){
				//close all open tabs
				plugin.$tab_blocks.find( '.megamaxmenu-tab.megamaxmenu-active' ).removeClass( 'megamaxmenu-active' );
			}
			else{
				plugin.initializeActiveTab( plugin );
			}
		},

		initializeActiveTab: function( plugin ){

			plugin.$tab_blocks.each( function(){
				var show_default = $(this).hasClass( 'megamaxmenu-tabs-show-default' );
				var show_current = $(this).hasClass( 'megamaxmenu-tabs-show-current' );
				var $tabs_group = $(this).find( '> .megamaxmenu-tabs-group' );

				//If there's already an active tab, skip - nothing to do
				if( $tabs_group.find( '> .megamaxmenu-tab.megamaxmenu-active' ).length ) return;

				var tab_shown = false;

				//Current
				if( show_current ){
					//Fill in any ancestor tab gaps
					$tabs_group.find( '.megamaxmenu-current-menu-item' ).parentsUntil( $tabs_group , '.megamaxmenu-tab:not( .megamaxmenu-nocurrent )' ).addClass( 'megamaxmenu-current-menu-ancestor' );

					//Find any current tabs
					var $current_tab = $tabs_group.find( '> .megamaxmenu-tab.megamaxmenu-current-menu-ancestor, > .megamaxmenu-tab.megamaxmenu-current-menu-item' );
					if( $current_tab.length ){
						plugin.openSubmenu( $current_tab.first() , 'tab current' , plugin );
						tab_shown = true;
					}
				}

				//Default
				if( show_default && !tab_shown ){
					//If there are no active tabs, activate the first one
					if( $tabs_group.find( '> .megamaxmenu-tab.megamaxmenu-active' ).length === 0 ){
						plugin.openSubmenu( $tabs_group.find( '> .megamaxmenu-tab' ).first() , 'tab default' , plugin );
					}
				}

				//Close all others? .megamaxmenu( 'closeSubmenu' , $current_tabs.siblings() );

			});
		},

		clearTabSizes: function( plugin ){
			plugin = plugin || this;
			plugin.$megamaxmenu.find( '.megamaxmenu-submenu , .megamaxmenu-tabs , .megamaxmenu-tab-content-panel , .megamaxmenu-tabs-group' ).css( 'min-height' , '' );
		},


		sizeTabs: function(){

			var plugin = this;
			var responsive = plugin.settings.responsive && ( window.innerWidth <= plugin.settings.breakpoint ) ? true : false;

			//If this is responsive and we're below the breakpoint, don't size the tabs
			if( responsive ) return;

			plugin.initializeActiveTab( plugin );

			//Do each tabs block
			plugin.$tab_blocks.each( function(){

				//Stacked is if tabs are (a) Layout: Top, (b) Layout: Bottom, (c) Responsive Mode
				var stacked = false;

				if( ( $(this).hasClass( 'megamaxmenu-tab-layout-top' ) ||
					  $(this).hasClass( 'megamaxmenu-tab-layout-bottom' ) ) &&
					!responsive ) {

					stacked = true;
				}

				$(this).data( 'um-stacked' , stacked );

				var maxh = 0;


				var $tree;
				if( responsive ){
					$tree = $(this).parentsUntil( '.megamaxmenu' ).add( $(this).parents( '.megamaxmenu' ) );
				}
				else{
					$tree = $(this).parentsUntil( '.megamaxmenu-item-level-0' );
				}
				$tree.addClass( 'megamaxmenu-test-dimensions' );

				//Find the maximum panel height for this group - only immediate tab panels, not nested
				var $panels = $( this ).find( ' > .megamaxmenu-tabs-group > .megamaxmenu-tab > .megamaxmenu-tab-content-panel' );
				var oh;
				$panels.each( function(){
					$(this).addClass( 'megamaxmenu-test-dimensions' );
					oh = $(this).outerHeight();	//outerheight
					if( oh > maxh ) maxh = oh;
					$(this).data('um-oh', oh);
					$(this).removeClass( 'megamaxmenu-test-dimensions' );
				});

				$(this).data( 'um-max-panel-height' , maxh );

				//if( $(this).is( '#menu-item-185' ) ){
				if( $(this).hasClass( 'megamaxmenu-tabs-dynamic-sizing' ) ){
					plugin.sizeTabsDynamic( $(this) , false );
				}
				else{
					plugin.sizeTabsMax( $(this) );
				}

				$tree.removeClass( 'megamaxmenu-test-dimensions' );

			});

		},

		sizeTabsMax: function( $tab_block ){

			var maxh = $tab_block.data( 'um-max-panel-height' );
			var stacked = $tab_block.data( 'um-stacked' );
			var $tabsgroup = $tab_block.find( '> .megamaxmenu-tabs-group' );

			//If left or right layout, set min-height on tabs group as well
			if( !stacked ){
				if( $tabsgroup.outerHeight() > maxh ){
					maxh = $tab_block.outerHeight();
				}
				$tabsgroup.css( 'min-height' , maxh );
			}
			//If top or bottom layout, set height for entire block
			else{
				//console.log( maxh , $tabsgroup.outerHeight() );
				$tab_block.css( 'min-height' , maxh + $tabsgroup.outerHeight() );
			}

			//var $sub = $tab_block.closest( '.megamaxmenu-submenu-drop' );
			//$sub.css( 'min-height' , false );	//this line does nothing, false is not valid - use '' to unset

			//Set panel heights
			$tabsgroup.find( '> .megamaxmenu-tab > .megamaxmenu-tab-content-panel' ).css( 'min-height' , maxh );

		},

		sizeTabsDynamic: function( $tab_block , animate ){

			if( animate === undefined ) animate = true;
			if( animate ){
				animate = $tab_block.hasClass( 'megamaxmenu-tabs-dynamic-sizing-animate' );
			}

			var plugin = this;
			var responsive = plugin.settings.responsive && ( window.innerWidth <= plugin.settings.breakpoint ) ? true : false;
			if( responsive ) return; //don't size when mobile

			var stacked = $tab_block.data( 'um-stacked' );

			//Look at the tabs group for the active item
			var $tabsgroup = $tab_block.find( '> .megamaxmenu-tabs-group' );
			var prev_height = $tabsgroup.outerHeight();
			$tabsgroup.css( 'min-height' , '0' );	//reset height
			var $active_panel = $tabsgroup.find( '> .megamaxmenu-active > .megamaxmenu-tab-content-panel' );
			var active_oh = $active_panel.data( 'um-oh' );
			var tg_oh = $tabsgroup.outerHeight();
			var maxh = tg_oh > active_oh ? $tab_block.outerHeight() : active_oh;	//get the max height of the tabs group vs active tab

			if( !stacked ){
				if( animate ){
					$tabsgroup.css( 'min-height' , prev_height );
					$tabsgroup.stop().animate( { 'min-height' : maxh } , 300 , 'swing' , function(){
						$active_panel.css( 'overflow' , 'auto' );	//force repaint for background images
					});	//set the tabs group height
				}
				else{
					$tabsgroup.css( 'min-height' , maxh );	//set the tabs group height
				}

			}
			//If top or bottom layout, set height for entire block
			else{
				if( animate ){
					$tab_block.stop().animate( { 'min-height' : maxh + $tabsgroup.outerHeight() } , 300 , 'swing' , function(){
						$active_panel.css( 'overflow' , 'auto' );
					});
				}
				else{
					$tab_block.css( 'min-height' , maxh + $tabsgroup.outerHeight() );
				}
			}
		},

		initializeSegmentCurrentStates: function(){
			this.$megamaxmenu.find( '.megamaxmenu-current-menu-item' ).first().parents( '.megamaxmenu-item:not( .megamaxmenu-nocurrent )' ).addClass( 'megamaxmenu-current-menu-ancestor' );
		},

		disableTransitions: function(){
			this.transitions = false;
			this.$megamaxmenu
					.removeClass( 'megamaxmenu-transition-slide' )
					.removeClass( 'megamaxmenu-transition-fade' )
					.removeClass( 'megamaxmenu-transition-shift' )
					.addClass( 'megamaxmenu-no-transitions' )
					.addClass( 'megamaxmenu-transition-none' );
		},



		/* Handlers */

		handleClicks: function( e , a , plugin ){

			var $a = $(a);

			//Kill any clicks flagged by touchend
			if( $a.data( 'megamaxmenu-killClick' ) ){
				e.preventDefault();
				//e.stopPropagation();
				plugin.log( 'killed click after touchend ', e );
			}

			//Reset flag in any event
			//plugin.log( 'reset kill click' );
			//$a.data( 'megamaxmenu-killClick' , false );
		},

		handleTouchInteraction: function( e , target , plugin ){

			// e.preventDefault();	//this would stop clicks, but also scroll, which is a problem
			e.stopPropagation();

			//If we're touching Windows, disable transitions
			if( e.type.indexOf( 'pointer' ) >= 0 ) plugin.disableTransitions();

			var $target = $( target );
			//Move to HandleTap
			// $target.data( 'megamaxmenu-killClick' , true );  //Prevent Clicks from being handled normally
			// $target.data( 'megamaxmenu-killHover' , true );  //Prevent hover from being handled normally
			// setTimeout( function(){ $target.data( 'megamaxmenu-killClick' , false ).data( 'megamaxmenu-killHover' , false ); } , 1000 );
			$target.parent().off( 'mouseleave.mouse_intent_none' );	//don't allow hover intent out if we're touching

			plugin.log( 'touchstart ' + e.type + ' ' + $target.text() , e );

			//Setup touch events
			//plugin.log( 'setup touch events' );
			$target.on( plugin.touchEnd ,  function( e ){ plugin.handleTap( e , this , plugin ); } );
			$target.on( plugin.touchMove , function( e ){ plugin.preventInteractionOnScroll( e , this , plugin ); } );


			//Note original event points for comparison
			//Standard
			if( e.originalEvent.touches ){
				$target.data( 'megamaxmenu-startX' , e.originalEvent.touches[0].clientX );
				$target.data( 'megamaxmenu-startY' , e.originalEvent.touches[0].clientY );
			}
			//Microsoft
			else if( e.originalEvent.clientY ){
				var pos = $target.offset();
				//$target.data( 'megamaxmenu-pos' , pos );
				$target.data( 'megamaxmenu-startX' , e.originalEvent.clientX );
			 	$target.data( 'megamaxmenu-startY' , e.originalEvent.clientY );
			}
			// else if( e.changedTouches ){
			// 	$target.data( 'megamaxmenu-startX' , e.changedTouches[0].pageX );
			// 	$target.data( 'megamaxmenu-startY' , e.changedTouches[0].pageY );
			// }
		},

		preventInteractionOnScroll: function( e , target , plugin ){

			plugin.log( 'touchmove interaction ' + e.type, e );

			var $target = $( target );

			//Check to see if the touch points were close enough together to be considered a tap
			//If not, they were a move/scroll, so unbind the touch event handlers and don't trigger menu
			if( e.originalEvent.touches ){
				if( Math.abs( e.originalEvent.touches[0].clientX - $target.data( 'megamaxmenu-startX' ) ) > plugin.settings.moveThreshold ||
					Math.abs( e.originalEvent.touches[0].clientY - $target.data( 'megamaxmenu-startY' ) ) > plugin.settings.moveThreshold ){
					plugin.log( 'Preventing interaction on scroll, reset handlers (standard)' );
					plugin.resetHandlers( $target , 'preventScroll touches' , plugin );
				}
				else{
					plugin.log( 'diff = ' + Math.abs( e.originalEvent.touches[0].clientY - $target.data( 'megamaxmenu-startY' ) ) );
				}
			}
			else if( e.originalEvent.clientY ){
				var pos = $target.data( pos );
				if( Math.abs( e.originalEvent.clientX - $target.data( 'megamaxmenu-startX' ) ) > plugin.settings.moveThreshold ||
					Math.abs( e.originalEvent.clientY - $target.data( 'megamaxmenu-startY' ) ) > plugin.settings.moveThreshold ){
					plugin.log( 'Preventing interaction on scroll, reset handlers (standard)' );
					plugin.resetHandlers( $target , 'preventScroll client' , plugin );
				}
				else{
					plugin.log( 'diff = ' + e.originalEvent.clientY + ' - ' + $target.data( 'megamaxmenu-startY' ) + ' = ' + Math.abs( e.originalEvent.clientY - $target.data( 'megamaxmenu-startY' ) ) );
				}
			}
			/*
			else if( e.changedTouches ){
				if( Math.abs( e.changedTouches[0].pageX - $target.data( 'megamaxmenu-startX' ) ) > plugin.settings.moveThreshold ||
					Math.abs( e.changedTouches[0].pageY - $target.data( 'megamaxmenu-startY' ) ) > plugin.settings.moveThreshold ){
					plugin.log( 'Preventing interaction on scroll, reset handlers (Windows)' );
					plugin.resetHandlers( $target );
				}
			}*/
			else plugin.log( 'no touch points found!' );

		},


		/* Handle tap - a touch interaction that completed */
		handleTap: function( e , target , plugin ){

			e.preventDefault();
			e.stopPropagation();

			var $target = $( target ); //.parent();

			//Quit if this was triggered after regular hover (which happens on IE)
			//Kill any touches flagged by mouseenter
			if( $target.data( 'megamaxmenu-killTouch' ) ){
				plugin.log( 'kill tap' );
				e.preventDefault();
				e.stopPropagation();
			}
			else{

				var $li = $target.parent();

				plugin.log( 'handleTap [' + $target.text() + ']', e.type );

				//$target.data( 'megamaxmenu-killHover' , true );

				$target.data( 'megamaxmenu-killClick' , true );  //Prevent Clicks from being handled normally
				$target.data( 'megamaxmenu-killHover' , true );  //Prevent hover from being handled normally
				setTimeout( function(){ $target.data( 'megamaxmenu-killClick' , false ).data( 'megamaxmenu-killHover' , false ); } , 1000 );

				//
				//Close up other submenus before proceeding
				//
				plugin.closeSubmenuInstantly( $li.siblings( '.megamaxmenu-active' ) );


				//
				//Toggle Submenus
				//

				if( $li.hasClass( 'megamaxmenu-has-submenu-drop' ) ){

					//if submenu is already open, close it and allow link to be followed
					if( $li.hasClass( 'megamaxmenu-active' ) ){

						//Don't close tabs, unless we're on mobile
						if( !$li.hasClass( 'megamaxmenu-tab' ) || window.innerWidth <= plugin.settings.breakpoint ){
							plugin.closeSubmenu( $li , 'toggleMegaMaxMenuActive' , plugin );
						}
						//plugin.followLink( e , $li , plugin );
						plugin.handleLink( e , target , plugin , true );
					}
					//if submenu is closed, open the submenu, prevent link from being followed
					else{
						plugin.openSubmenu( $li , 'toggle' , plugin );
					}
				}

				//
				//Follow links that don't have submenus
				//
				else{
					//plugin.followLink( e , $li , plugin );
					plugin.handleLink( e , target , plugin , true );
				}
			}

			//Reset flag in any event
			$target.data( 'megamaxmenu-killTouch' , false );

			//plugin.resetHandlers( $li );
			//plugin.log( 'reset handlers' );
			plugin.resetHandlers( $target , 'handleTap' , plugin );

		},


		handleLink: function( e , link , plugin , follow ){

			follow = follow || false;

			plugin.log( 'handleLink' );

			//e.preventDefault();

			var $link = $( link );

			if( !$link.is( 'a' ) ) return;

			var href = $link.attr( 'href');

			var scrolltarget = $link.data( 'megamaxmenu-scrolltarget' );

			if( scrolltarget ){

				var $target_el = $( scrolltarget ).first();
				if( $target_el.length > 0 ){
					e.preventDefault();
					$link.trigger( 'megamaxmenuscrollto' );
					var $li = $link.parent( '.megamaxmenu-item' );
					$li.addClass( 'megamaxmenu-current-menu-item' );
					$li.siblings().removeClass( 'megamaxmenu-current-menu-item' ).removeClass( 'megamaxmenu-current-menu-parent' ).removeClass( 'megamaxmenu-current-menu-ancestor' );

					var anim_done = false;
					$( 'html,body' ).animate({
							scrollTop: $target_el.offset().top - plugin.settings.scrollto_offset
						}, plugin.settings.scrollto_duration , 'swing' ,
						function(){
							//after scroll
							if( !anim_done ){
								plugin.closeSubmenu( $link.closest( '.megamaxmenu-item-level-0' ) , 'handeLink' , plugin );
								if( plugin.settings.collapse_after_scroll && !plugin.$megamaxmenu.hasClass( 'megamaxmenu-responsive-nocollapse' ) ) plugin.toggleMenuCollapse( 'toggle' , false , plugin );
								$link.trigger( 'megamaxmenuscrollto_complete' );
								anim_done = true;
							}
						});
					return false; //don't follow any links if this scroll target is present
				}
				//if target isn't present here, redirect with hash
				else{
					if( href && href.indexOf( '#' ) == -1 ){		//check that hash does not already exist
						if( scrolltarget.indexOf( '#' ) == -1 ){	//if this is a class, add a hash tag
							scrolltarget = '#'+scrolltarget;
						}
						window.location = href + scrolltarget;		//append hash/scroll target to URL and redirect
						e.preventDefault();
					}
					//No href, no worries
				}
			}

			if( !href ){
				e.preventDefault();
			}

			//Handle clicks (where default was already prevented)
			else{
				if( follow && e.isDefaultPrevented() ){
					plugin.log( 'default prevented, follow link' );
					if( $link.attr( 'target' ) == '_blank' ){
						window.open( href , '_blank');
					}
					else{
						window.location = href;
					}
				}

			}


		},


		handleMouseClick: function( e , target , plugin ){
			plugin.log( 'handleMouseClick', e );

			//Stop link follow
			//e.preventDefault();	//defer to 'else' so that the second click can be handled naturally

			var $target	= $( target );

			if( $target.data( 'megamaxmenu-killClick' ) ){
				plugin.log( 'handleMouseClick: killClick' );
				return;	//3.0.4.1
			}

			var $item = $target.parent( '.megamaxmenu-item' ); //$( li );

			if( $item.length ){
				//Check if this is already open
				if( $item.hasClass( 'megamaxmenu-active' ) ){

					//If it's a link, follow
					if( $target.is( 'a' ) ){
						plugin.handleLink( e , target , plugin );
					}

					//If it's not a link (and not a tab), retract
					if( !$item.hasClass( 'megamaxmenu-tab' ) ){
						plugin.closeSubmenu( $item , 'retract' );
					}
				}

				//Close other menus, open this one
				else{

					//Submenu to open
					if( $item.hasClass( 'megamaxmenu-has-submenu-drop' ) ){
						e.preventDefault(); //Not already active, don't allow click
						plugin.closeSubmenuInstantly( $item.siblings( '.megamaxmenu-active' ) );
						plugin.openSubmenu( $item , 'click' , plugin );
					}
					//Allow link to be followed
					else{
						//Just let it go
						//plugin.handleLink( e , target , plugin );
					}
				}
			}

			//Only attach mouseout after mouseover, this way menus opened by touch won't be closed by mouseout
			//$( li ).on( 'mouseout.megamaxmenu-submenu-toggle' , function( e ){ plugin.handleMouseout( e , this , plugin ); } );
		},






		handleMouseIntent: function( e , item , plugin ){	///*target*/

			plugin.log( 'handleMouseIntent' );

			//var $target = $( target );
			//var $item = $target.parent( '.megamaxmenu-item' );
			var $item = $( item );

			//console.log( '[' + $item.find('> .megamaxmenu-target').text() + '] handle mouse_intent' );

			//Cancel Timer
			if( $item.data( 'mouse_intent_timer' ) ){
				$item.data( 'mouse_intent_timer' , clearTimeout( $item.data( 'mouse_intent_timer' ) ) );
				//console.log( 'clear timeout' );
			}

			//Prevent Touch events, which will occur on IE
			// var $target = $item.find( '> .megamaxmenu-target' );
			// $target.data( 'megamaxmenu-killTouch' , true );
			// setTimeout( function(){ $target.data( 'megamaxmenu-killTouch' , false ); } , 1000 );

			//Quit if this was triggered by touch
			var $a = $item.find( '.megamaxmenu-target' );
			//Kill any hovers flagged by touchstart
			if( $a.data( 'megamaxmenu-killHover' ) ){
				plugin.log( 'killHover MouseIntent' );
				e.preventDefault();
				e.stopPropagation();
				return;
			}

			pX = e.pageX; pY = e.pageY;


			$item.on( 'mousemove.mouse_intent' , plugin.trackMouse );

			$item.data( 'mouse_intent_timer' , setTimeout(
				function(){
					plugin.compare( e , $item , plugin.handleMouseIntentSuccess , plugin );
				},
				plugin.settings.intent_interval
			));

			//Cancel if out - since we only hook up the close submenu event after a successfull intent
			$item.on( 'mouseleave.mouse_intent_none' , function(){
				//plugin.log( 'mouseleave' );
				$(this).data( 'mouse_intent_timer' , clearTimeout( $(this).data( 'mouse_intent_timer' ) ) );
				$item.data( 'mouse_intent_state' , 0 );
				$item.off( 'mouseleave.mouse_intent_none' );

				if( $a.data( 'megamaxmenu-killHover' ) ){
					plugin.log( 'killHover MouseIntent_Cancel' );
					e.preventDefault();
					e.stopPropagation();
					return;
				}

				plugin.closeSubmenu( $item , 'mouse_intent_cancel' , plugin );
			});
			//}
			//else console.log( 'STATE 1' );


		},

		handleMouseIntentSuccess: function( e , $item , plugin ){

			plugin.log( 'handleMouseIntentSuccess' );

			//Cancel the quickleave event
			$item.off( 'mouseleave.mouse_intent_none' );

			//Quit if this was triggered by touch - on Android Stock, this is triggered after touch, but mouseenter is triggered before
			var $a = $item.find( '.megamaxmenu-target' );
			//Kill any hovers flagged by touchstart
			if( $a.data( 'megamaxmenu-killHover' ) ){
				plugin.log( 'Kill hover on IntentSuccess' );
				e.preventDefault();
				e.stopPropagation();
				return;
			}
			//Reset flag in any event
			$a.data( 'megamaxmenu-killHover' , false );


			plugin.triggerSubmenu( e , $item , plugin );

			//Setup mouseleave, but not for tabs, except below breakpoint
			if( !$item.hasClass( 'megamaxmenu-tab' ) || window.innerWidth <= plugin.settings.breakpoint ){
				$item.on( 'mouseleave.mouse_intent' , function( e ){ plugin.handleMouseIntentLeave( e , this , plugin ); } );
			}
		},

		handleMouseIntentLeave: function( e , item , plugin ){

			//var $target = $( target );
			//var $item = $target.parent( '.megamaxmenu-item' );
			var $item = $( item );

			//console.log( '[' + $item.find('> .megamaxmenu-target').text() + '] handle mouse_intent LEAVE' );

			//Cancel timer
			if( $item.data( 'mouse_intent_timer' ) ){
				$item.data( 'mouse_intent_timer' , clearTimeout( $item.data( 'mouse_intent_timer' ) ) );
				//console.log( 'clear timeout' );
			}

			$item.off( 'mousemove.mouse_intent' , plugin.trackMouse );
			//$item.off( 'mouseleave.mouse_intent' ); //special

			if( $item.data( 'mouse_intent_state' ) == 1 ){
				//$item.data( 'mouse_intent_state' , 0 );
				$item.data( 'mouse_intent_timer' , setTimeout(
					function(){
						plugin.delayMouseLeave( e , $item , plugin.handleMouseIntentLeaveSuccess , plugin );
					},
					plugin.settings.intent_delay
				) );
			}

		},

		handleMouseIntentLeaveSuccess: function( e , $el , plugin ){
			$el.off( 'mouseleave.mouse_intent' ); //special
			if( $el.find( '> .megamaxmenu-target' ).data( 'megamaxmenu-killHover' ) ) return;
			plugin.closeSubmenu( $el , 'mouse_intent_leave' , plugin );

		},

		delayMouseLeave: function( e , $el , func , plugin ){
			//console.log( 'delay mouse leave ' );
			$el.data( 'mouse_intent_timer' , clearTimeout( $el.data( 'mouse_intent_timer' ) ) );
			$el.data( 'mouse_intent_state' , 0 );
			return func.apply( $el ,[e,$el,plugin]);
		},

		trackMouse: function( e ) {
			cX = e.pageX;
			cY = e.pageY;
		},

        compare: function( e , $el , func , plugin ){

        	//console.log( 'compare' );

			$el.data( 'mouse_intent_timer' , clearTimeout( $el.data( 'mouse_intent_timer' ) ) );

			// compare mouse positions to see if they've crossed the threshold
			if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < plugin.settings.intent_threshold ) {
				//console.log( 'threshold met' );
				$el.off( 'mousemove.mouse_intent' , plugin.track );
				// set hoverIntent state to true (so mouseOut can be called)
                $el.data( 'mouse_intent_state' , 1 );
                return func.apply( $el , [e,$el,plugin] );
            } else {
            	//console.log( 'keep polling' );
                // set previous coordinates for next time
                pX = cX; pY = cY;
                // use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
                $el.data( 'mouse_intent_timer' , setTimeout( function(){ plugin.compare( e , $el , func , plugin ); } , plugin.settings.intent_interval ) );
            }
        },





        triggerSubmenu: function( e , $item , plugin ){
			plugin.closeSubmenuInstantly( $item.siblings( '.megamaxmenu-active, .megamaxmenu-in-transition' ) );
			plugin.openSubmenu( $item , 'mouseenter' , plugin );
        },




		handleMouseover: function( e , target , plugin ){

			if( plugin.events_disabled ) return;

			var $target = $( target );

			$target.data( 'megamaxmenu-killTouch' , true );
			setTimeout( function(){ $target.data( 'megamaxmenu-killTouch' , false ); } , 1000 );

			plugin.log( 'handleMouseenter, add mouseleave', e );

			var $item = $target.parent( '.megamaxmenu-item' ); //$( li );

			if( $item.length ){
				if( !$item.hasClass( 'megamaxmenu-active' ) ){

					plugin.triggerSubmenu( e , $item , plugin );

					//Set up mouseleave event, but no hover out for Tabs
					if( !$item.hasClass( 'megamaxmenu-tab' ) || window.innerWidth <= plugin.settings.breakpoint ){
						$item.on( 'mouseleave.megamaxmenu-submenu-toggle' , function( e ){ plugin.handleMouseleave( e , this , plugin ); } );
					}
				}
			}
		},

		//Mouseleave should be for LI so that we can use subs
		handleMouseleave: function( e , li , plugin ){

			plugin.log( 'handleMouseleave, remove mouseleave', e );

			$( li ).off( 'mouseleave.megamaxmenu-submenu-toggle' );	//Unbind mouseout event, it'll be rebind next mouseover

			plugin.closeSubmenu( $( li ) , 'mouseout' );
		},



		handleSubmenuRetractorStart: function( e , li , plugin ){
			e.preventDefault();
			e.stopPropagation();

			$( li ).on( plugin.touchEnd , function( e ){ plugin.handleSubmenuRetractorEnd( e , this , plugin ); } );

			plugin.log( 'handleSubmenuRetractorStart ' + $( li ).text());

		},

		handleSubmenuRetractorEnd: function( e , li , plugin ){
			e.preventDefault();
			e.stopPropagation();

			var $parent_item = $( li ).closest( '.megamaxmenu-item' );
			plugin.closeSubmenu( $parent_item , 'handleSubmenuRetractor' );

			$( li ).off( plugin.touchEnd );

			plugin.log( 'handleSubmenuRetractorEnd ' + $parent_item.find('> .megamaxmenu-target').text());
			return false;

		},


		handleResponsiveToggle: function( e , toggle , plugin ){

			plugin.log( 'handleResponsiveToggle ' + e.type , e );

			e.preventDefault();
			e.stopPropagation();

			//Prevent Double-fire
			//some browsers fire click and touch, so prevent the click manually
			if( e.type == 'touchend' ){
				plugin.$megamaxmenu.data( 'megamaxmenu-prevent-click' , true );
				setTimeout( function(){
					plugin.$megamaxmenu.data( 'megamaxmenu-prevent-click' , false );
				}, 500 );
			}
			else if( e.type == 'click' && plugin.$megamaxmenu.data( 'megamaxmenu-prevent-click' ) ){
				plugin.$megamaxmenu.data( 'megamaxmenu-prevent-click' , false );
				return;
			}

			//TODO just call off/on click.xyz?  or binding already fired?
			//Or should we just have a state set that returns, independent of event type

			plugin.toggleMenuCollapse( 'toggle' , toggle , plugin );

		},

		handleTouchoffCloseStart: function( e , _this , plugin ){
			plugin.touchoffclosestart = $( window ).scrollTop();
		},

		handleTouchoffClose: function( e , _this , eventtype , plugin ){
			//Only fire if the touch event occurred outside the menu AND we've clicked OR we've touched but haven't scrolled
			if( !$(e.target).closest( '.megamaxmenu' ).length && ( eventtype == 'click' || plugin.touchoffclosestart == $( window ).scrollTop() ) ){

				plugin.log( 'touchoff close ', e );

				//If there are any submenus to close, close them and temporarily disable hover events
				if( plugin.closeAllSubmenus() ){
					//console.log( plugin.settings.submenuAnimationDuration );
					//temporarily disable hovering so that the mouseover event doesn't fire and reopen the menu
					plugin.disableSubmenuToggleMouseEvents();
					window.setTimeout( function(){
							//plugin.initializeSubmenuToggleMouseEvents( plugin );
							plugin.reenableSubmenuToggleMouseEvents( plugin );
						},
						plugin.settings.submenuAnimationDuration );

					//can't call /e.preventDefault(); because it will stop mouseover events from firing
				}
			}
		},




		/* Controllers */

		toggleMenuCollapse: function( action , toggle , plugin ){

			plugin = plugin || this;
			toggle = toggle || '.megamaxmenu-resposive-toggle';
//console.log( toggle + '[data-megamaxmenu-target="'+ plugin.$megamaxmenu.attr( 'id' ) + '"]' );
			var $toggle;
			//Passed regular Object
			if( typeof toggle == 'object' ){
				$toggle = $( toggle );
			}
			//Passed selector string
			else{
				$toggle = $( toggle + '[data-megamaxmenu-target="'+ plugin.$megamaxmenu.attr( 'id' ) + '"]');
			}

			action = action || 'toggle';

			if( action == 'toggle' ){
				if( plugin.$megamaxmenu.hasClass( 'megamaxmenu-responsive-collapse' ) ){
					action = 'open';
				}
				else{
					action = 'close';
				}
			}

			//plugin.$megamaxmenu.slideToggle();

			if( action == 'open' ){
				plugin.$megamaxmenu.removeClass( 'megamaxmenu-responsive-collapse' );
				$toggle.trigger( 'megamaxmenutoggledopen' );
				$toggle.toggleClass( 'megamaxmenu-responsive-toggle-open' );

				if( plugin.settings.aria_responsive_toggle ){
					$toggle.attr( 'aria-expanded' , true );
				}
			}
			else{
				plugin.$megamaxmenu.addClass( 'megamaxmenu-responsive-collapse' );
				$toggle.trigger( 'megamaxmenutoggledclose' );
				$toggle.toggleClass( 'megamaxmenu-responsive-toggle-open' );

				if( plugin.settings.aria_responsive_toggle ){
					$toggle.attr( 'aria-expanded' , false );
				}
			}
			//plugin.$megamaxmenu.toggleClass( 'megamaxmenu-responsive-collapse' );
			//$toggle.trigger( 'megamaxmenutoggled' );

			if( plugin.transitions && !plugin.$megamaxmenu.hasClass( 'megamaxmenu-responsive-nocollapse' ) ){
				plugin.$megamaxmenu.addClass( 'megamaxmenu-in-transition' );
				plugin.$megamaxmenu.on( plugin.transitionend + '_togglemegamaxmenu', function(){
						plugin.$megamaxmenu.removeClass( 'megamaxmenu-in-transition' );
						plugin.$megamaxmenu.off( plugin.transitionend  + '_togglemegamaxmenu' );
					});
			}
		},

		positionSubmenus: function(){

			var plugin = this;
			if( plugin.orientation == 'h' ){
				plugin.$megamaxmenu.find( '.megamaxmenu-submenu-drop.megamaxmenu-submenu-align-center' ).each( function(){

					var $parent = $(this).parent( '.megamaxmenu-item' );
					var $sub = $(this);
					var $container;
					if( plugin.$megamaxmenu.hasClass( 'megamaxmenu-bound' ) ){
						$container = $parent.closest( '.megamaxmenu , .megamaxmenu-submenu' ); // main menu bar
					}
					else if( plugin.$megamaxmenu.hasClass( 'megamaxmenu-bound-inner' ) ){
						$container = $parent.closest( '.megamaxmenu-nav , .megamaxmenu-submenu' ); // inner menu bar
					}
					else{
						var $parent_sub = $parent.closest( '.megamaxmenu-submenu' );

						//Find the closest relatively positioned element
						if( $parent_sub.length === 0 ){
							$container = plugin.$megamaxmenu.offsetParent();
							if( !$container ) $container = $( 'body' ); //If no offset parent, assume bounds of body tag
						}
						else{
							$container = $parent_sub;
						}
					}

					var sub_width = $sub.outerWidth();

					var parent_width = $parent.outerWidth();
					var parent_left_edge = $parent.offset().left;

					var container_width = $container.width();
					var container_left_edge = $container.offset().left;

					// console.log( $container );
					// console.log( 'parent_width: ' + parent_width );
					// console.log( 'parent_left: ' + parent_left_edge );
					// console.log( 'container_width: ' + container_width );
					// console.log( 'container_left: ' + container_left_edge );


					var center_left = ( parent_left_edge + ( parent_width / 2 ) ) - ( container_left_edge + ( sub_width / 2 ) );

					//If submenu is left of container edge, align left
					var left = center_left > 0 ? center_left : 0;

					//If submenu width is larger than container width, center to container (menu bar)
					if( sub_width > container_width ){
						left = ( sub_width - container_width ) / -2;
					}
					//If submenu is right of container edge, align right
					else if( left + sub_width > container_width ){
						//left = container_width - sub_width;
						$sub.css( { 'right' : 0 , 'left' : 'auto' } );
						left = false;
					}

					if( left !== false ){
						$sub.css( 'left' , left );
					}
				});
			}
		},


		openSubmenu: function( $li , tag , plugin ){

			plugin = plugin || this;

			plugin.log( 'Open Submenu ' + tag );

			if( !$li.hasClass( 'megamaxmenu-active' ) ){
				$li.addClass( 'megamaxmenu-active' );

				//ARIA
				if( plugin.settings.aria_expanded ){
					$li.find( '>.megamaxmenu-target' ).attr( 'aria-expanded' , 'true' );
				}
				if( plugin.settings.aria_hidden ){
					$li.find( '>.megamaxmenu-submenu' ).attr( 'aria-hidden' , 'false' );
				}

				if( plugin.transitions ){
					$li.addClass( 'megamaxmenu-in-transition' );

					$li.find( '> .megamaxmenu-submenu' ).on( plugin.transitionend + '_opensubmenu', function(){
						plugin.log( 'finished submenu open transition' );
						$li.removeClass( 'megamaxmenu-in-transition' );
						$( this ).off( plugin.transitionend  + '_opensubmenu' );
					});
				}
				$li.trigger( 'megamaxmenuopen' );
			}
		},

		closeSubmenu: function( $li , tag , plugin ){

			plugin = plugin || this;

			plugin.log( 'closeSubmenu ' + $li.find( '>a' ).text() + ' [' + tag + ']' );

			//If this menu is currently active and has a submenu, close it
			if( $li.hasClass( 'megamaxmenu-item-has-children' ) && $li.hasClass( 'megamaxmenu-active' ) ){

				if( plugin.transitions ){
					$li.addClass( 'megamaxmenu-in-transition' );	//transition class keeps visual flag until transition completes
				}

				$li.each( function(){
					var _$li = $(this);
					var _$ul = _$li.find( '> .megamaxmenu-submenu' );

					//Remove the transition flag once the transition is completed
					if( plugin.transitions ){
						_$ul.on( plugin.transitionend + '_closesubmenu', function(){
							plugin.log( 'finished submenu close transition' );
							_$li.removeClass( 'megamaxmenu-in-transition' );
							_$ul.off( plugin.transitionend  + '_closesubmenu' );
						});
					}
				});
			}

			//Actually remove the active class, which causes the submenu to close
			$li.removeClass( 'megamaxmenu-active' );
			$li.trigger( 'megamaxmenuclose' );

			//ARIA
			if( plugin.settings.aria_expanded ){
				$li.find( '>.megamaxmenu-target' ).attr( 'aria-expanded' , 'false' );
			}
			if( plugin.settings.aria_hidden ){
				$li.find( '>.megamaxmenu-submenu' ).attr( 'aria-hidden' , 'true' );
			}

		},

		//Close subs without transition
		closeSubmenuInstantly: function( $li ){
			if( $li.length === 0 ) return;
			var plugin = this;
			$li.addClass( 'megamaxmenu-notransition' );
			$li.removeClass( 'megamaxmenu-active' ).removeClass( 'megamaxmenu-in-transition' );
			$li[0].offsetHeight;	//triggers reflow
			$li.removeClass( 'megamaxmenu-notransition' );
			$li.trigger( 'megamaxmenuclose' );

			//ARIA
			if( plugin.settings.aria_expanded ){
				$li.find( '>.megamaxmenu-target,>.megamaxmenu-submenu' ).attr( 'aria-expanded' , 'false' );
			}
			if( plugin.settings.aria_hidden ){
				$li.find( '>.megamaxmenu-submenu' ).attr( 'aria-hidden' , 'true' );
			}
		},

		//Top level subs only
		closeAllSubmenus: function(){
			//$( this.element ).find( 'li.menu-item-has-children' ).removeClass( 'megamaxmenu-active' );
			var $actives = this.$megamaxmenu.find( '.megamaxmenu-item-level-0.megamaxmenu-active' );
			if( $actives.length ) this.closeSubmenuInstantly( $actives );
			return $actives.length;
		},

		resetHandlers: function( $target , tag , plugin ){
			plugin.log( 'ResetHandlers: ' + tag );
			$target.off( this.touchEnd );
			$target.off( this.touchMove );

			var $item = $target.parent();
			$item.off( 'mousemove.mouse_intent' );
			$item.off( 'mouseleave.mouse_intent_none' );
			$item.data( 'mouse_intent_timer' , clearTimeout( $item.data( 'mouse_intent_timer' ) ) );
			$item.data( 'mouse_intent_state' , 0 );
		},


		log: function( content , o , plugin ){
			plugin = plugin || this;

			if( plugin.settings.debug ){
				if( plugin.settings.debug_onscreen ){
					this.debug_target.prepend( '<div class="um-debug-content">'+content+'</div>' );
				}
				else console.log( content , o );
			}
		}
	};

	/*
	$.fn[ pluginName ] = function ( options ) {
		return this.each(function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
	};
	*/

	$.fn[ pluginName ] = function ( options ) {

		var args = arguments;

		if ( options === undefined || typeof options === 'object' ) {
			return this.each(function() {
				if ( !$.data( this, "plugin_" + pluginName ) ) {
					$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
				}
			});
		}
		else if ( typeof options === 'string' && options[0] !== '_' && options !== 'init') {
			// Cache the method call to make it possible to return a value
			var returns;

			this.each(function () {
				var instance = $.data(this, 'plugin_' + pluginName);

				// Tests that there's already a plugin-instance and checks that the requested public method exists
				if ( instance instanceof Plugin && typeof instance[options] === 'function') {

					// Call the method of our plugin instance, and pass it the supplied arguments.
					returns = instance[options].apply( instance, Array.prototype.slice.call( args, 1 ) );
				}

				// Allow instances to be destroyed via the 'destroy' method
				if (options === 'destroy') {
					$.data(this, 'plugin_' + pluginName, null);
				}
			});

			// If the earlier cached method gives a value back return the value, otherwise return this to preserve chainability.
			return returns !== undefined ? returns : this;
		}
	};

})( jQuery, window, document );



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

		if( ( typeof console != "undefined" ) && init_point == 'window.load' ) console.log( 'Notice: MegaMaxMenu initialized via ' + init_point + '.  This indicates that an unrelated error on the site prevented it from loading via the normal document ready event.' );

		//Scroll to non-ID "hashes"
		if( window.location.hash.substring(1,2) == '.' ){
			var $scrollTarget = $( 'body' ).find( window.location.hash.substring(1) );
			if( $scrollTarget.length ) window.scrollTo( 0 , $scrollTarget.offset().top - megamaxmenu_data.scrollto_offset );
		}
		//Make sure offset is applied to normal hashes
		else if( window.location.hash.length ){
			setTimeout( function(){
				try{
					var $scrollTarget = $( 'body' ).find( window.location.hash );
					if( $scrollTarget.length ) window.scrollTo( 0 , $scrollTarget.offset().top - megamaxmenu_data.scrollto_offset  );
				}
				catch(error){
					//not an element identifier, don't scroll
				}
			}, 100 );
		}


		//Clean out empty items left by unbalanced dynamic items
		$( '.megamaxmenu-item:empty' ).each( function(){			//find all empty items
		  var $p = $(this).parent();											//get parent UL
		  $(this).remove();																//remove the empty item
		  if( $p.find( '.megamaxmenu-item' ).length == 0 ){	//if this submenu is now empty (no items)
				//remove submenu classes, remove submenu
		    $p.parent().removeClass( 'megamaxmenu-has-submenu-drop' ).removeClass( 'megamaxmenu-has-submenu-flyout' ).off().find('.megamaxmenu-target > .megamaxmenu-sub-indicator').remove();
		    $p.remove();
		  }
		});
		$( '.megamaxmenu-submenu:empty' ).each( function(){
			var $p = $(this).parent('li');
			$p.removeClass('megamaxmenu-has-submenu-drop' );
			$p.find( '.megamaxmenu-sub-indicator' ).remove();
		});


		$( '#wp-admin-bar-megamaxmenu_loading' ).remove();

		$( '.megamaxmenu' ).megamaxmenu({
			//touchOffClose: false
			//debug: true
			//debug_onscreen: true
			//mouseEvents: false
			//clicktest: true
		}).trigger( 'megamaxmenuinit' );


		//Search Autofocus
		$( '.megamaxmenu-submenu .megamaxmenu-search-input-autofocus' ).closest( '.megamaxmenu-has-submenu-drop' ).on( 'megamaxmenuopen' , function(){
			var $input = $(this).find( '.megamaxmenu-submenu .megamaxmenu-search-input');
			if( $input.length ){
				setTimeout( function(){
					$input[0].focus();
				}, 250 );
			}
		});

		//Google Maps
		if(
			typeof google !== 'undefined' &&
			typeof google.maps !== 'undefined' &&
			typeof google.maps.LatLng !== 'undefined') {
				$('.megamaxmenu-map-canvas').each(function(){

					var $canvas = $( this );
					var dataZoom = $canvas.attr('data-zoom') ? parseInt( $canvas.attr( 'data-zoom' ) ) : 8;

					var latlng = $canvas.attr('data-lat') ?
									new google.maps.LatLng($canvas.attr('data-lat'), $canvas.attr('data-lng')) :
									new google.maps.LatLng(40.7143528, -74.0059731);

					var myOptions = {
						zoom: dataZoom,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						center: latlng
					};

					var map = new google.maps.Map(this, myOptions);

					if($canvas.attr('data-address')){
						var geocoder = new google.maps.Geocoder();
						geocoder.geocode({
								'address' : $canvas.attr('data-address')
							},
							function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									map.setCenter(results[0].geometry.location);
									latlng = results[0].geometry.location;
									var marker = new google.maps.Marker({
										map: map,
										position: results[0].geometry.location,
										title: $canvas.attr('data-mapTitle')
									});
								}
						});
					}
					else{
						//place marker for regular lat/long
						var marker = new google.maps.Marker({
							map: map,
							position: latlng,
							title: $canvas.attr('data-mapTitle')
						});
					}

					var $li = $(this).closest( '.megamaxmenu-has-submenu-drop' );
					var mapHandler = function(){
						google.maps.event.trigger(map, "resize");
						map.setCenter(latlng);
						map.setZoom(dataZoom);
						//Only resize the first time we open
						$li.off( 'megamaxmenuopen', mapHandler );
					};
					$li.on( 'megamaxmenuopen', mapHandler );
				});
		}
	}

})(jQuery);


/*
 * Deprecated API Functions
 */
//jQuery( '.megamaxmenu' ).megamaxmenu( 'openSubmenu' , jQuery( '#menu-item-401' ) );
function megaMaxMenu_openMega( id ){
	jQuery( '.megamaxmenu' ).megamaxmenu( 'openSubmenu' , jQuery( id ) );
}
function megaMaxMenu_openFlyout( id ){
	jQuery( '.megamaxmenu' ).megamaxmenu( 'openSubmenu' , jQuery( id ) );
}
function megaMaxMenu_close( id ){
	jQuery( '.megamaxmenu' ).megamaxmenu( 'closeSubmenu' , jQuery( id ) );
}
function megaMaxMenu_redrawSubmenus(){
	jQuery( '.megamaxmenu' ).megamaxmenu( 'positionSubmenus' );
}
