/**
 * jquery-hover-dropdown-box	- v1.3.0
 * (c) Masanori Ohgita - 2014.
 * https://github.com/mugifly/jquery-hover-dropdown-box/
 * License: MIT
**/

(function($){

	/**
		Containers Array
	**/
	var hoverDropdownBoxs = new Array();

	/**
		Container Object
	**/
	var hoverDropdownBox = function( obj, options ){
		if(options.isInline){ // Inline mode
			this.parentDOM = obj;
			this.triggerDOM = obj;
		} else { // Hover mode
			this.parentDOM = $('body'); // parent object
			this.triggerDOM = obj; // Hover triger object (such as button) for Hover mode
		}

		this.options = options;

		this.dom = null;
		this.isVisible = true;
		this.items = new Array(); // hoverDropdownBoxItem
		this.footerItem = null; // hoverDropdownBoxItem

		// Initialize
		this.id = hoverDropdownBoxs.length;
		this.init();
		hoverDropdownBoxs.push(this);

		this.options.hoverDropdownBox_id = this.id;
		this.options.dropdownBox = function(){
			return hoverDropdownBoxs[ this.hoverDropdownBox_id ];
		};
		this.options.getHoverDropdownBox = function(){ // TODO! Deprecated
			return hoverDropdownBoxs[ this.hoverDropdownBox_id ];
		};

		if(!this.options.isInline){ // Hover mode
			// Append to parent DOM and set event-handler to triger DOM
			this.appendToParent();
		}

	};

	// Initialize
	hoverDropdownBox.prototype.init = function( opt_container ){
		// Generate of container
		var $container;
		if( opt_container == null ){
			$container = jQuery('<div/>');
			$container.addClass('hover_dropdown_box');
			$container.data('hoverDropdownBoxId', this.id);
			this.dom = $container;
			this.parentDOM.append($container);
			if(this.options.isInline){
				$container.addClass('hover_dropdown_box_inline');
			} else {
				$container.addClass('hover_dropdown_box_hover');
			}
		} else {
			$container = opt_container;
			this.dom = $container;
		}
		
		var _width = '200px';
		if( null != this.options.width){
			_width = this.options.width;
		}
		$container.css('width', _width);
		
		var _height = '220px';
		if( null != this.options.height){
			_height = this.options.height;
		}
		$container.css('height', _height);

		// Generate of header
		var $header = jQuery('<h3/>');
		if(this.options.title == null){
			$header.html("&nbsp;");
		} else {
			$header.text( this.options.title);
		}
		$container.append($header);

		// Generate of list
		var $list = jQuery('<ul/>');
		$container.append($list);
		for ( key in this.options.items ){
			// Item
			var $li = jQuery('<li/>');
			$li.addClass('hover_dropdown_box_item');
			$li.data('hoverDropdownBoxBoxItemKey', key);
			$list.append($li);

			if( this.options.onClick != null ) { // Event: onClick
				$li.click(function(){
					var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
					var item_key = $(this).data('hoverDropdownBoxBoxItemKey')
					var item_obj = hoverDropdownBoxs[id].items[item_key];
					if( hoverDropdownBoxs[id].options.items[item_key].inputType != null
					 && hoverDropdownBoxs[id].options.items[item_key].inputType == 'text'){
					 	// Ignore When input form (text) is visible
						if($(item_obj.innerInputObject).is(':visible')){
							return true;
						}
					}
					// options.onClick( item_key, item_object, dom_object )
					(hoverDropdownBoxs[id].options.onClick)(item_key, item_obj, this);
				});
			}

			if( this.options.onLabelClick != null ) { // Event: onLabelClick
				$li.click(function(evt){
					var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
					var item_key = $(this).data('hoverDropdownBoxBoxItemKey');
					var item_obj = hoverDropdownBoxs[id].items[item_key];
					// Check event source
					if(item_obj.innerInputObject != null && $(item_obj.innerInputObject).get(0) == $(evt.target).get(0)){
						return true;
					}
					if(item_obj.innerInputActionLeftObject != null && $(item_obj.innerInputActionLeftObject).get(0) == $(evt.target).get(0)){
						return true;
					}
					if(item_obj.innerInputActionRightObject != null && $(item_obj.innerInputActionRightObject).get(0) == $(evt.target).get(0)){
						return true;
					}
					if( hoverDropdownBoxs[id].options.items[item_key].inputType != null
					 && hoverDropdownBoxs[id].options.items[item_key].inputType == 'text'){
						if($(item_obj.innerInputObject).is(':visible')){
							return true;
						}
					}
					// options.onLabelClick( item_key, item_object, dom_object )
					(hoverDropdownBoxs[id].options.onLabelClick)(item_key, item_obj, this);
				});
			}
			
			// Color tag
			if( this.options.items[key].color != null ){
				var color = this.options.items[key].color;
				var $color_tag = jQuery('<div/>');
				$color_tag.addClass('hover_dropdown_box_item_color_tag');
				$color_tag.html('&nbsp;');
				$color_tag.css('background-color', color);
				$li.append($color_tag);
			}
			var ixLbl	= key;
			if( this.options.items[key].label != null ){
				ixLbl	= this.options.items[key].label;//Luis Balam
			}
			// Label
			var $label = jQuery('<span/>')
			$label.addClass('hover_dropdown_box_item_label');
			$label.text(ixLbl);
			$li.append($label);

			// Item container object
			this.items[key] = new hoverDropdownBoxItem( this, $li, key );
			
			// Input object
			// Insert later
		}

		// Footer
		if(this.options.footer != null){
			$container.addClass('hover_dropdown_box_with_footer');

			// Item
			var $li = jQuery('<div/>');
			$li.addClass('hover_dropdown_box_item');
			$li.addClass('hover_dropdown_box_footer');
			$li.data('hoverDropdownBoxBoxItemKey', 'footer');
			$list.append($li);

			// Label
			var $label = jQuery('<span/>')
			$label.addClass('hover_dropdown_box_item_label');
			$label.text( this.options.footer.label );
			$li.append($label);

			// Item container object
			this.footerItem = new hoverDropdownBoxItem( this, $li, 'footer' );

			// Input object
			if(this.options.footer.inputType != null){
				this.footerItem.generateInputObject_( this.options.footer );
			}
		}

		// Input object
		for ( key in this.options.items ){
			if(this.options.items[key].inputType != null){
				this.items[key].generateInputObject_( this.options.items[key] );
			}
		}

	};

	// Append to parent dom (for Hover mode)
	hoverDropdownBox.prototype.appendToParent = function( ){
		var options = this.options;
		$(this.triggerDOM).data('hoverDropdownBoxId', this.id);
		$(this.triggerDOM).hover(
			function(evt){ // Mouse-hover on parent
				var id = $(this).data('hoverDropdownBoxId');
				var $dom = hoverDropdownBoxs[id].dom;
				var $parentDOM = hoverDropdownBoxs[id].parentDOM;
				var $triggerDOM = hoverDropdownBoxs[id].triggerDOM;

				$dom.unbind('mouseout');
				if($dom.data('timeout') != null){
					window.clearTimeout( $dom.data('timeout') );
				}
				if($triggerDOM.data('timeout') != null){
					window.clearTimeout( $triggerDOM.data('timeout') );
				}

				// Show and adjust with parent element
				//console.log('mousehover on trigger');
				
				$dom.show();
				hoverDropdownBoxs[id].isVisible = true;
				var top_margin = 30;
				if(parseInt($(window).height()) <=  parseInt($triggerDOM.offset().top - $(window).scrollTop()) + top_margin + parseInt($dom.height()) ){
					// Display to top of parent
					$dom.css('top', $triggerDOM.offset().top + $triggerDOM.height() - $dom.height() );
				} else {
					// Display to bottom of parent
					$dom.css('top', $triggerDOM.offset().top + top_margin );
				}
				if(parseInt($(window).width()) < parseInt($triggerDOM.offset().left) + parseInt($dom.width()) ){
					// Display to left of parent
					$dom.css('left', $triggerDOM.offset().left - ($dom.width() / 2) );
				} else {
					// Display to right of parent
					$dom.css('left', $triggerDOM.offset().left );
				}
				$dom.removeClass('hover_dropdown_box_hide');
				
				// Bind mouse-out event
				var t = window.setTimeout(function(){
					//console.log("Bind: mouseout from Dropdown");
					$dom.bind('mouseout', function(evt){
						//console.log("mouseout from Dropdown");
						if( hoverDropdownBoxs[id].isOnHoverMouse( evt ) ){ // If mouse is hover on trigger and dropdown
							// Cancel
							if($dom.data('timeout') != null){
								//console.log("  Cancel timer: unbined mouseout from Dropdown -> hide");
								window.clearTimeout( $dom.data('timeout') );
							}
							return;
						}
						// Hide
						$dom.addClass('hover_dropdown_box_hide');
						var t = window.setTimeout(function(){
							//console.log("unbined mouseout from Dropdown -> hide");
							$dom.unbind('mouseout');
							$dom.hide();
							if(options.onClose != null){
								options.onClose.call(null);
							}
							hoverDropdownBoxs[id].isVisible = false;
						}, 300);
						$dom.data('timeout', t);
					});
				}, 200);
				$dom.data('timeout', t);
				//console.log("Timer start: mouseout from Dropdown");
			},
			function(evt){ // Mouse-out from parent
				var id = $(this).data('hoverDropdownBoxId');
				var $dom = hoverDropdownBoxs[id].dom;
				var $parentDOM = hoverDropdownBoxs[id].parentDOM;
				var $triggerDOM = hoverDropdownBoxs[id].triggerDOM;

				if( hoverDropdownBoxs[id].isOnHoverMouse( evt ) ){ // If mouse is hover on trigger and dropdown
					// Cancel
					if($triggerDOM.data('timeout') != null){
						//console.log("  Cancel timer: unbined mouseout from Dropdown -> hide");
						window.clearTimeout( $triggerDOM.data('timeout') );
					}
					return;
				}

				// Hide
				$dom.addClass('hover_dropdown_box_hide');
				var t = window.setTimeout(function(){
					//console.log("  mouseout from trigger -> hide");
					$dom.unbind('mouseout');
					$dom.hide();
					if(options.onClose != null){
						options.onClose.call(null);
					}
					hoverDropdownBoxs[id].isVisible = false;
				}, 300);
				$triggerDOM.data('timeout', t);
			}
		);
		
		// Hide now
		$(this.dom).addClass('hover_dropdown_box_hide');
		$(this.dom).hide();
		this.isVisible = false;
	};

	// Add item
	hoverDropdownBox.prototype.addItem = function( key, item ){
		this.options.items[key] = item;
		$(this.dom).children().remove();
		this.init( this.dom );
	};

	// Remove item
	hoverDropdownBox.prototype.removeItem = function( item_key ){
		// TODO!
	};

	// Check hover mouse on Dropdown dom & trigger dom
	hoverDropdownBox.prototype.isOnHoverMouse = function( evt ){
		var $dom = $(this.dom);
		var $triggerDOM = $(this.triggerDOM);
		if(( evt.pageX < $dom.offset().left || $dom.offset().left + $dom.width() < evt.pageX ||
			evt.pageY < $dom.offset().top || $dom.offset().top + $dom.height() < evt.pageY )
			&& ( evt.pageX < $triggerDOM.offset().left || $triggerDOM.offset().left + $triggerDOM.get(0).offsetWidth < evt.pageX ||
			evt.pageY < $triggerDOM.offset().top || $triggerDOM.offset().top + $triggerDOM.get(0).offsetHeight < evt.pageY)
		){
			//console.log("Not mouse hover.")
			return false;
		}
		//console.log("Mouse hover.")
		return true;
	};

	/**
		Item Object
	**/
	var hoverDropdownBoxItem = function(hoverDropdownBox, dom_object, item_key){
		this.parentObject = hoverDropdownBox;
		this.key = item_key; // key or 'footer'
		this.dom = dom_object;
		this.labelObject = $(dom_object.children('.hover_dropdown_box_item_label'))[0];
		this.innerInputObject = null; // Input form or checkbox
		this.innerInputObjectType = null; // Input form or checkbox
		this.innerInputActionRightObject = null; // Enter link
		this.innerInputActionLeftObject = null; // Cancel link
	};

	// Generate of input object
	hoverDropdownBoxItem.prototype.generateInputObject_ = function( item_options ){
		var input_type = item_options.inputType;

		// Checkbox (Checkbox like div)
		if( input_type == "checkbox" ){
			var $check = jQuery('<div/>');
			$check.addClass('hover_dropdown_box_item_checkbox');
			if(item_options.inputSelected != null && item_options.inputSelected == true){ // Deprecated
				$check.data('isChecked', true);
				$check.addClass('checkbox_checked');
			} else if(item_options.inputChecked != null && item_options.inputChecked == true){
				$check.data('isChecked', true);
				$check.addClass('checkbox_checked');
			} else {
				$check.data('isChecked', false);
			}

			$check.click(function(evt){
				var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
				var item_key =  $(this).parents('.hover_dropdown_box_item').data('hoverDropdownBoxBoxItemKey');
				var item_obj;
				if(item_key == 'footer'){
					item_obj = hoverDropdownBoxs[id].footerItem;
				} else {
					item_obj = hoverDropdownBoxs[id].items[item_key];
				}

				if($check.data('isChecked')){
					item_obj.checked( false );
				} else {
					item_obj.checked( true );
				}
			});

			this.dom.append($check);
			this.innerInputObject = $check;
			this.innerInputObjectType = 'checkbox';
		}

		// Textbox
		if( input_type == "text" ){
			var $text = jQuery('<input/>');
			$text.attr('type', 'text');
			$text.addClass('hover_dropdown_box_item_textbox');
			if(item_options.inputPlaceholder != null){
				$text.attr('placeholder', item_options.inputPlaceholder);
			}
			$text.hide();
			this.dom.click( function( evt ){
				var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
				var item_key = $(this).data('hoverDropdownBoxBoxItemKey');
				var item_obj;
				if(item_key == 'footer'){
					item_obj = hoverDropdownBoxs[id].footerItem;
				} else {
					item_obj = hoverDropdownBoxs[id].items[item_key];
				}
				if($(item_obj.innerInputObject).is(':visible')){
					return true;
				}
				// Check event source
				if(item_obj.innerInputObject != null && $(item_obj.innerInputObject).get(0) == $(evt.target).get(0)){
					return true;
				}
				if(item_obj.innerInputActionLeftObject != null && $(item_obj.innerInputActionLeftObject).get(0) == $(evt.target).get(0)){
					return true;
				}
				if(item_obj.innerInputActionRightObject != null && $(item_obj.innerInputActionRightObject).get(0) == $(evt.target).get(0)){
					return true;
				}
				// Hide the label
				$(item_obj.labelObject).hide();
				// Show the textbox, and focus to it
				$(item_obj.innerInputObject).show();
				$(item_obj.innerInputObject).focus();
				// Show the action link
				$(item_obj.innerInputActionLeftObject).show();
				$(item_obj.innerInputActionRightObject).show();
			});
			$text.keypress( function(evt){
				if(evt.which == 13){ // Enter
					var $text = $(evt.target);
					var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
					var item_key = $($(this).parents('.hover_dropdown_box_item')[0]).data('hoverDropdownBoxBoxItemKey');
					var item_obj;
					if(item_key == 'footer'){
						item_obj = hoverDropdownBoxs[id].footerItem;
					} else {
						item_obj = hoverDropdownBoxs[id].items[item_key];
					}

					if(hoverDropdownBoxs[id].options.onInputText != null){
						// options.onTextInput(item_key, item_object, value)
						(hoverDropdownBoxs[id].options.onTextInput)( item_key, item_obj, $text.val() );
					}
					if(hoverDropdownBoxs[id].options.onTextInput != null){ // TODO! Deprecated
						// options.onTextInput(value, item_key, item_object, dom_object)
						(hoverDropdownBoxs[id].options.onTextInput)( item_key, item_obj, $text.val(), this );
					}

					// Hide the textbox
					$(item_obj.innerInputObject).hide();
					$(item_obj.innerInputActionLeftObject).hide();
					$(item_obj.innerInputActionRightObject).hide();
					// Show the label
					$(item_obj.labelObject).show();

					// Fire the event of OnChange
					if(item_obj.parentObject.options.onChange != null){
						(function(func, item_key, item_obj, value){
							window.setTimeout( function(){ (func)( item_key, item_obj, value ); }, 10);
						})(item_obj.parentObject.options.onChange, item_key, item_obj, $text.val());
					}
					return false;
				} else if(evt.keyCode == 27){ // ESC
					// Cancel
					$(item_obj.innerInputObject).hide();
					$(item_obj.innerInputActionLeftObject).hide();
					$(item_obj.innerInputActionRightObject).hide();
					$(item_obj.labelObject).show();
				}
			});

			this.dom.append($text);
			this.innerInputObject = $text;
			this.innerInputObjectType = 'text';

			// Action link (right) - OK
			this.generateInputActionObject_(item_options, '&gt;', 'right',
				function(evt) {
					var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
					var item_key = $($(this).parents('.hover_dropdown_box_item')[0]).data('hoverDropdownBoxBoxItemKey');
					var item_obj;
					if(item_key == 'footer'){
						item_obj = hoverDropdownBoxs[id].footerItem;
					} else {
						item_obj = hoverDropdownBoxs[id].items[item_key];
					}
					var $text = item_obj.innerInputObject;

					if(hoverDropdownBoxs[id].options.onInputText != null){
						// options.onTextInput(item_key, item_object, value)
						(hoverDropdownBoxs[id].options.onTextInput)( item_key, item_obj, $text.val() );
					}
					if(hoverDropdownBoxs[id].options.onTextInput != null){ // TODO! Deprecated
						// options.onTextInput(value, item_key, item_object, dom_object)
						(hoverDropdownBoxs[id].options.onTextInput)( item_key, item_obj, $text.val(), $text );
					}
					
					// Fire the event of OnChange
					if(item_obj.parentObject.options.onChange != null){
						(function(func, item_key, item_obj, value){
							window.setTimeout( function(){ (func)( item_key, item_obj, value ); }, 10);
						})(item_obj.parentObject.options.onChange, item_key, item_obj, $text.val());
					}

					// Hide the textbox
					$(item_obj.innerInputObject).hide();
					$(item_obj.innerInputActionLeftObject).hide();
					$(item_obj.innerInputActionRightObject).hide();
					// Show the label
					$(item_obj.labelObject).show();
				}
			);

			// Action link (left) - Cancel
			this.generateInputActionObject_(item_options, 'X', 'left',
				function(evt) {
					var id = $($(this).parents('.hover_dropdown_box')[0]).data('hoverDropdownBoxId');
					var item_key = $($(this).parents('.hover_dropdown_box_item')[0]).data('hoverDropdownBoxBoxItemKey');
					var item_obj;
					if(item_key == 'footer'){
						item_obj = hoverDropdownBoxs[id].footerItem;
					} else {
						item_obj = hoverDropdownBoxs[id].items[item_key];
					}
					// Hide the textbox
					$(item_obj.innerInputObject).hide();
					$(item_obj.innerInputActionLeftObject).hide();
					$(item_obj.innerInputActionRightObject).hide();
					// Show the label
					$(item_obj.labelObject).show();
				}
			);
		}
	};

	// Generate of action link object (for input object)
	hoverDropdownBoxItem.prototype.generateInputActionObject_ = function( item_options, label, position, on_click ){
		var $action = $('<span/>');
		$action.hide();

		$action.addClass('hover_dropdown_box_item_input_action');
		$action.html(label);
		$action.click( on_click );

		this.dom.append($action);
		if(position == 'left'){
			$action.addClass('input_action_left');
			this.innerInputActionLeftObject = $action;
		} else {
			$action.addClass('input_action_right');
			this.innerInputActionRightObject = $action;
		}
	};

	// Getter of input object
	hoverDropdownBoxItem.prototype.inputElement = function( ){
		if(this.innerInputObject){
			return this.innerInputObject;
		}
		return null;
	};
	hoverDropdownBoxItem.prototype.getInputObject = function( ){// TODO! Deprecated
		if(this.innerInputObject){
			return this.innerInputObject;
		}
		return null;
	};

	// Getter and Setter for isChecked of input object
	hoverDropdownBoxItem.prototype.checked = function( value ){
		if(this.innerInputObject && this.innerInputObjectType == 'checkbox'){
			if(value != null && value == true){
				this.parentObject.options.items[this.key].inputChecked = true;
				$(this.innerInputObject[0]).data('isChecked', true);
				// Drawing custom checkbox
				$(this.innerInputObject[0]).addClass('checkbox_checked');
			} else if(value != null && value == false){
				this.parentObject.options.items[this.key].inputChecked = false;
				$(this.innerInputObject[0]).data('isChecked', false);
				// Drawing custom checkbox
				$(this.innerInputObject[0]).removeClass('checkbox_checked');
			}

			if(value != null){
				// Fire the event of OnChange
				if(this.parentObject.options.onChange != null){
					(function(func, item_key, item_obj, value){
						window.setTimeout( function(){ (func)( item_key, item_obj, value ); }, 10);
					})(this.parentObject.options.onChange, this.key, this, $(this.innerInputObject[0]).data('isChecked'));
				}
			}

			return $(this.innerInputObject[0]).data('isChecked');
		}
	};

	// Getter and Setter for value of input object
	hoverDropdownBoxItem.prototype.value = function( value ){
		if(this.innerInputObject){
			if(this.innerInputObjectType == 'checkbox'){
				return this.checked(value);
			} else if(this.innerInputObjectType == 'text'){
				if(value != null){
					$(this.innerInputObject[0]).val(value);
				}
				return $(this.innerInputObject[0]).val();
			}
		}
		return undefined;
	};

	/**
		Definitions of jQuery plugin
	**/

	// hover-dropdown-box - Inline mode
	$.fn.hoverDropdownBox = function(config){
		var defaults = {
			isInline: true
		};
		var options = $.extend(defaults, config);
		return this.each(function(i){
			var obj = new hoverDropdownBox( $(this), options );
		});
	};

	// hover-dropdown-box - Hover mode
	$.fn.appendHoverDropdownBox = function(config){
		var defaults = {
			isInline:	false
		};
		var options = $.extend(defaults, config);
		return this.each(function(i){
			var obj = new hoverDropdownBox( $(this), options );
		});
	};
})(jQuery);
